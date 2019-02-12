<?php
namespace SeTaco;


use SeTaco\Config\TargetConfig;
use SeTaco\Session\IDomElement;
use SeTaco\Session\IDomElementsCollection;
use SeTaco\Exceptions\SeTacoException;
use SeTaco\Exceptions\Browser\URLCompareException;
use SeTaco\Exceptions\Browser\Element\ElementException;
use SeTaco\Exceptions\Browser\Element\ElementExistsException;
use SeTaco\Exceptions\Browser\Element\ElementNotFoundException;
use SeTaco\Exceptions\Browser\Element\MultipleElementExistsException;

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Structura\Strings;


class Browser implements IBrowser
{
	private $isClosed = false;
	
	/** @var BrowserSetup */
	private $config;
	
	
	private function resolveKeyword(string $keyword): array
	{
		$result = [];
		
		if (Strings::isStartsWith($keyword, 'selector:'))
		{
			$result[] = trim(Strings::replace($keyword, 'selector:', ''));
			return $result;
		}
		
		foreach ($this->config->KeywordsConfig->KeywordResolvers as $resolver)
		{
			$mapped = $resolver->resolve($keyword);
			
			if ($mapped)
				$result[] = $mapped;
		}
		
		return $result;
	}

	
	public function __construct(BrowserSetup $config)
	{
		$this->config = $config;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	
	public function getRemoteWebDriver(): RemoteWebDriver
	{
		return $this->config->RemoteWebDriver;
	}
	
	public function getTargetName(): ?string
	{
		return $this->config->TargetName;
	}
	
	public function getTargetConfig(): TargetConfig
	{
		return $this->config->TargetConfig;
	}
	
	public function getBrowserName(): string
	{
		return $this->config->BrowserName;
	}
	
	public function goto(string $url): IBrowser
	{
		$this->getRemoteWebDriver()->navigate()->to($this->getTargetConfig()->getURL($url));
		return $this;
	}
	
	public function click(string $keyword, float $timeout = 2.5): IBrowser
	{
		$this->getElement($keyword, $timeout)->click(true);
		return $this;
	}
	
	public function hover(string $keyword, float $timeout = 2.5): IBrowser
	{
		$element = $this->getElement($keyword, $timeout);
		$element->hover();
		
		return $this;
	}
	
	public function hoverAndClick(string $keyword, float $timeout = 2.5): IBrowser
	{
		$this->hover($keyword, $timeout);
		return $this->click($keyword);
	}
	
	public function input(string $keyword, string $value, float $timeout = 2.5): IBrowser
	{
		$this->getElement($keyword, $timeout)->input($value);
		return $this;
	}
	
	public function formInput(array $keywordValuePairs, ?string $submit = null, float $timeout = 2.5): IBrowser
	{
		if (!isset($keywordValuePairs[0]))
		{
			foreach ($keywordValuePairs as $name => $value)
			{
				$element = $this->getElement('selector:form [name="' . $name . '"]', $timeout);
				$element->input($value);
			}
		}
		else
		{
			foreach ($keywordValuePairs as $keyword => $value)
			{
				$this->input($keyword, $value, $timeout);
			}
		}
		
		if ($submit)
		{
			$this->click($submit, $timeout);
		}
		
		return $this;
	}
	
	public function waitForElementToDisappear(string $keyword, float $timeout = 2.5): void
	{
		$endTime = microtime(true) + $timeout;
		
		while ($this->tryGetElement($keyword, 0.0))
		{
			if (microtime(true) >= $endTime)
			{
				throw new ElementExistsException($keyword, $timeout);
			}
			
			usleep(50000);
		}
	}
	
	public function waitForElement(string $keyword, float $timeout = 2.5): void
	{
		$this->getElement($keyword, $timeout);
	}
	
	public function hasElement(string $keyword, float $timeout = 2.5): bool
	{
		return (bool)$this->tryGetElement($keyword, $timeout);
	}
	
	public function getElement(string $keyword, float $timeout = 2.5): IDomElement
	{
		$result = $this->getElements($keyword, $timeout);
		
		if ($result->isEmpty())
		{
			throw new ElementNotFoundException($keyword);
		}
		else if ($result->count() > 1)
		{
			throw new MultipleElementExistsException($keyword, $timeout);
		}
		
		return $result->first();
	}
	
	public function getElements(string $keyword, float $timeout = 2.5): IDomElementsCollection
	{
		$selectors = $this->resolveKeyword($keyword);
		
		if (!$selectors)
		{
			throw new SeTacoException('Keyword ' . $keyword . ' can not be resolved');
		}
	
		$endTime = microtime(true) + $timeout;
		
		$result = new DomElementsCollection($this->getRemoteWebDriver());
		$result->findMany($selectors);
		
		while ($result->isEmpty())
		{
			if (microtime(true) >= $endTime)
				throw new ElementNotFoundException($keyword);
			
			usleep(50000);
			$result->findMany($selectors);
		}
		
		return $result;
	}
	
	public function tryGetElement(string $keyword, float $timeout = 2.5): ?IDomElement
	{
		try
		{
			return $this->getElement($keyword, $timeout);
		}
		catch (ElementException $e)
		{
			return null;
		}
	}
	
	public function compareURL(string $url): bool
	{
		$currentURL = $this->getURL();
		
		if ($currentURL == $url)
			return true;
		
		$pattern = '/' . str_replace('/', '\\/', $url) . '/';
		
		return preg_match($pattern, $currentURL);
	}
	
	public function waitForURL(string $url, float $timeout = 2.5): void
	{
		$endTime = microtime(true) + $timeout;
		
		while (!$this->compareURL($url))
		{
			if (microtime(true) >= $endTime)
			{
				$currentUrl = $this->getURL();
				throw new URLCompareException($url, $currentUrl, $timeout);
			}
			
			usleep(50000);
		}
	}
	
	public function getTitle(): string
	{
		return $this->getRemoteWebDriver()->getTitle();
	}
	
	public function getURL(): string
	{
		return $this->getRemoteWebDriver()->getCurrentURL();
	}
	
	public function refresh(): void
	{
		$this->getRemoteWebDriver()->navigate()->refresh();
	}
	
	public function isClosed(): bool
	{
		return $this->isClosed;
	}
	
	public function close(): void
	{
		if ($this->isClosed)
			return;
		
		$this->isClosed = true;
		$this->getRemoteWebDriver()->close();
	}
	
	/**
	 * @param array|string $data If string, used as cookie name. 
	 * @param null|string $value If $data is string and $value is null, delete the cookie.
	 */
	public function setCookie($data, ?string $value = null): void
	{
		if (is_string($data))
		{
			if (is_null($value))
			{
				$this->getRemoteWebDriver()->manage()->deleteCookieNamed($data);
			}
			else
			{
				$this->getRemoteWebDriver()->manage()->addCookie(['name' => $data, 'value' => $value]);
			}
		}
		else
		{
			$this->getRemoteWebDriver()->manage()->addCookie($data);
		}
	}
	
	/**
	 * @return Cookie[]
	 */
	public function cookies(): array
	{
		return $this->getRemoteWebDriver()->manage()->getCookies();
	}
}