<?php
namespace SeTaco;


use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Exception\NoSuchElementException;

use SeTaco\Config\TargetConfig;
use SeTaco\Exceptions\Element\ElementNotFoundException;
use SeTaco\Exceptions\SeTacoException;


class Browser implements IBrowser
{
	private $isClosed = false;
	
	/** @var RemoteWebDriver */
	private $driver;
	
	/** @var TargetConfig */
	private $targetConfig;
	
	
	public function __construct(RemoteWebDriver $driver, TargetConfig $config)
	{
		$this->driver = $driver;
		$this->targetConfig = $config;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	
	public function getRemoteWebDriver(): RemoteWebDriver
	{
		return $this->driver;
	}
	
	public function goto(string $url): IBrowser
	{
		$this->driver->navigate()->to($this->targetConfig->getURL($url));
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
				throw new SeTacoException("Element $cssSelector still exists after waiting for $timeout seconds");
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
				$this->driver
					->wait((int)$timeout, ($timeout - floor($timeout)) * 1000)
					->until(WebDriverExpectedCondition::presenceOfElementLocated($selector));
			}
			
			$element = $this->driver->findElement($selector);
		}
		catch (TimeOutException $et)
		{
			throw new ElementNotFoundException($cssSelector);
		}
		catch (NoSuchElementException $e)
		{
			throw new ElementNotFoundException($cssSelector);
		}
		
		return new DomElement($element, $this->driver);
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
	
	public function getTitle(): string
	{
		return $this->driver->getTitle();
	}
	
	public function getURL(): string
	{
		return $this->driver->getCurrentURL();
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
		$this->driver->close();
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
				$this->driver->manage()->deleteCookieNamed($data);
			}
			else
			{
				$this->driver->manage()->addCookie(['name' => $data, 'value' => $value]);
			}
		}
		else
		{
			$this->driver->manage()->addCookie($data);
		}
	}
	
	/**
	 * @return Cookie[]
	 */
	public function cookies(): array
	{
		return $this->driver->manage()->getCookies();
	}
}