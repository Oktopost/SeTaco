<?php
namespace SeTaco;


use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;

use SeTaco\Exceptions\Element\ElementNotFoundException;
use SeTaco\Exceptions\SeTacoException;


class Browser implements IBrowser
{
	private $isDestroyed = false;
	
	/** @var RemoteWebDriver */
	private $driver;
	
	/** @var DriverConfig */
	private $config;
	
	
	public function __construct(RemoteWebDriver $driver, DriverConfig $config)
	{
		$this->driver = $driver;
		$this->config = $config;
	}
	
	public function __destruct()
	{
		$this->destroy();
	}
	
	
	public function getRemoteWebDriver(): RemoteWebDriver
	{
		return $this->driver;
	}
	
	public function goto(string $url): IBrowser
	{
		$this->driver->navigate()->to($this->config->Homepage->getURL($url));
		return $this;
	}
	
	public function click(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		$this->getElement($cssSelector, $timeout)->click();
		return $this;
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
			if ($timeout > 0)
			{
				$this->driver
					->wait((int)$timeout, ($timeout - floor($timeout)) * 1000)
					->until(WebDriverExpectedCondition::presenceOfElementLocated(
						WebDriverBy::cssSelector($cssSelector)
					));
			}
			
			$element = $this->driver->findElement(WebDriverBy::cssSelector($cssSelector));
		}
		catch (NoSuchElementException $e)
		{
			throw new ElementNotFoundException($cssSelector);
		}
		
		return new DomElement($element);
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
	
	public function isDestroyed(): bool
	{
		return $this->isDestroyed;
	}
	
	public function destroy(): void
	{
		if ($this->isDestroyed)
			return;
		
		$this->isDestroyed = true;
		$this->driver->close();
	}
}