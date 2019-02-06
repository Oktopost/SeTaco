<?php
namespace SeTaco;


use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

use SeTaco\Config\TargetConfig;
use SeTaco\Exceptions\Browser\URLCompareException;
use SeTaco\Exceptions\Browser\Element\ElementExistsException;
use SeTaco\Exceptions\Browser\Element\ElementNotFoundException;


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
		foreach ($elements as $css => $input)
		{
			$this->input($css, $input, $timeout);
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
		try
		{
			$selector = ($cssSelector[0] == '/' ? 
				WebDriverBy::xpath($cssSelector) :
				WebDriverBy::cssSelector($cssSelector));
			
			if ($timeout > 0)
			{
				$this->getRemoteWebDriver()
					->wait((int)$timeout, ($timeout - floor($timeout)) * 1000)
					->until(WebDriverExpectedCondition::presenceOfElementLocated($selector));
			}
			
			$element = $this->getRemoteWebDriver()->findElement($selector);
		}
		catch (TimeOutException $et)
		{
			throw new ElementNotFoundException($cssSelector);
		}
		catch (NoSuchElementException $e)
		{
			throw new ElementNotFoundException($cssSelector);
		}
		
		return new DomElement($element, $this->getRemoteWebDriver());
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
		
		while (!$this->compareURL($this))
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