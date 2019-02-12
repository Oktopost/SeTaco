<?php
namespace SeTaco;


use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use SeTaco\Config\TargetConfig;
use SeTaco\Exceptions\Browser\Element\ElementExistsException;
use SeTaco\Exceptions\Browser\Element\ElementNotFoundException;
use SeTaco\Exceptions\Browser\Element\MultipleElementExistsException;
use SeTaco\Exceptions\Browser\URLCompareException;
use SeTaco\Session\IDomElement;
use SeTaco\Session\IDomElementsCollection;


class Browser implements IBrowser
{
	private $isClosed = false;
	
	/** @var BrowserSetup */
	private $config;
	
	
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
	
	public function click(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		$this->getElement($cssSelector, $timeout)->click();
		return $this;
	}
	
	public function hover(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		$element = $this->getElement($cssSelector, $timeout);
		$element->hover();
		
		return $this;
	}
	
	public function hoverAndClick(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		$this->hover($cssSelector, $timeout);
		return $this->click($cssSelector);
	}
	
	public function input(string $cssSelector, string $value, float $timeout = 2.5): IBrowser
	{
		$this->getElement($cssSelector, $timeout)->input($value);
		return $this;
	}
	
	public function formInput(array $elements, ?string $submit = null, float $timeout = 2.5): IBrowser
	{
		if (!isset($elements[0]))
		{
			foreach ($elements as $name => $value)
			{
				$element = $this->getElement('form [name="' . $name . '"]', $timeout);
				$element->input($value);
			}
		}
		else
		{
			foreach ($elements as $css => $input)
			{
				$this->input($css, $input, $timeout);
			}
		}
		
		if ($submit)
		{
			$this->click($submit);
		}
		
		return $this;
	}
	
	public function waitForElementToDisappear(string $cssSelector, float $timeout = 2.5): void
	{
		$endTime = microtime(true) + $timeout;
		
		while ($this->tryGetElement($cssSelector, 0.0))
		{
			if (microtime(true) >= $endTime)
			{
				throw new ElementExistsException($cssSelector, $timeout);
			}
			
			usleep(50000);
		}
	}
	
	public function waitForElement(string $cssSelector, float $timeout = 2.5): void
	{
		$this->getElement($cssSelector, $timeout);
	}
	
	public function hasElement(string $cssSelector, float $timeout = 2.5): bool
	{
		return (bool)$this->tryGetElement($cssSelector, $timeout);
	}
	
	public function getElement(string $cssSelector, float $timeout = 2.5): IDomElement
	{
		$result = $this->getElements($cssSelector, $timeout);
		
		if ($result->isEmpty())
		{
			throw new ElementNotFoundException($cssSelector);
		}
		else if ($result->count() > 1)
		{
			throw new MultipleElementExistsException($cssSelector, $timeout);
		}
		
		return $result->first();
	}
	
	public function getElements(string $selector, float $timeout = 2.5): IDomElementsCollection
	{
		$result =  new DomElementsCollection($this->getRemoteWebDriver());
		return $result->find($selector, $timeout);
	}
	
	public function tryGetElement(string $selector, float $secToWait = 2.5): ?IDomElement
	{
		try
		{
			return $this->getElement($selector, $secToWait);
		}
		catch (ElementNotFoundException $e)
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