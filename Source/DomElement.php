<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Exception\WebDriverException;

use SeTaco\Exceptions\FacebookExceptionParser;
use SeTaco\Exceptions\Element\MissingAttributeException;


class DomElement implements IDomElement
{
	/** @var RemoteWebElement */
	private $element;
	
	/** @var BrowserSetup */
	private $setup;
	
	
	public function __construct(RemoteWebElement $element, BrowserSetup $setup)
	{
		$this->setup = $setup;
		$this->element = $element;
	}
	
	
	public function getRemoteWebElement(): RemoteWebElement
	{
		return $this->element;
	}
	
	
	public function click(bool $hover = false): IDomElement
	{
		try
		{
			if ($hover)
				$this->hover();
			
			$this->element->click();
		}
		catch (WebDriverException $e)
		{
			FacebookExceptionParser::parseElementException($e);
		}
		
		return $this;
	}
	
	public function hover(): IDomElement
	{
		$this->setup->RemoteWebDriver
			->action()
			->moveToElement($this->element)
			->perform();
		
		return $this;
	}
	
	public function input(string $input, bool $clear = false): IDomElement
	{
		try
		{
			if ($clear)
				$this->element->clear();
			
			$this->element->sendKeys($input);
		}
		catch (WebDriverException $e)
		{
			FacebookExceptionParser::parseElementException($e);
		}
		
		return $this;
	}
	
	public function clear(): IDomElement
	{
		try
		{
			$this->element->clear();
		}
		catch (WebDriverException $e)
		{
			FacebookExceptionParser::parseElementException($e);
		}
		
		return $this;
	}
	
	public function getName(bool $allowMissing = true): ?string
	{
		return $this->getAttribute('name', $allowMissing);
	}
	
	public function getText(): string
	{
		return $this->getRemoteWebElement()->getText();
	}
	
	public function getAttribute(string $name, bool $allowMissing = true): ?string
	{
		$value = null;
		
		try
		{
			$value = $this->element->getAttribute($name);
		}
		catch (WebDriverException $e)
		{
			FacebookExceptionParser::parseElementException($e);
		}
		
		
		if (is_null($value) && !$allowMissing)
			throw new MissingAttributeException($name);
		
		return $value;
	}
	
	public function query(): IQuery
	{
		return new Query($this->setup, $this->element);
	}
	
	
	/**
	 * @param RemoteWebElement[] $elements
	 * @param BrowserSetup $setup
	 * @return DomElement[]
	 */
	public static function convertAll(array $elements, BrowserSetup $setup): array
	{
		$domElements = [];
		
		/** @var RemoteWebElement[] $elements */
		foreach ($elements as $element)
		{
			$domElements[] = new DomElement($element, $setup);
		}
		
		return $domElements;
	}
}