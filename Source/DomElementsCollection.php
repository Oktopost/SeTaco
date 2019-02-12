<?php
namespace SeTaco;


use SeTaco\Session\IDomElement;
use SeTaco\Session\IDomElementsCollection;
use SeTaco\Exceptions\Browser\Element\ElementNotFoundException;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverExpectedCondition;


class DomElementsCollection implements IDomElementsCollection
{
	/** @var RemoteWebDriver */
	private $driver;
	
	/** @var IDomElement[]  */
	private $collection = [];
	
	
	private function asNewCollection(array $elements): IDomElementsCollection
	{
		return new DomElementsCollection($this->driver, $elements);
	}
	
	private function findByContent(string $text): IDomElementsCollection
	{
		$byValueCollection = $this->find('input[value="' . $text . '"]', 0)->get();
		$byContentCollection = $this->find('//*[contains(text(), "' . $text . '")]', 0)->get();
		
		return $this->asNewCollection(array_merge($byValueCollection, $byContentCollection));
	}
	
	
	public function __construct(RemoteWebDriver $driver, ?array $predefinedElements = [])
	{
		$this->driver = $driver;
		
		if ($predefinedElements)
			$this->collection = $predefinedElements;
	}
	
	
	public function find(string $selector, ?float $timeout = 2.5): IDomElementsCollection
	{
		$isXpath = $selector[0] == '/';
		$byContent = strstr($selector, 'value=') || strstr($selector, 'contains');
		
		try
		{
			$search = ($isXpath ?
				WebDriverBy::xpath($selector) :
				WebDriverBy::cssSelector($selector));
			
			if ($timeout && $timeout > 0)
			{
				$this->driver
					->wait((int)$timeout, ($timeout - floor($timeout)) * 1000)
					->until(WebDriverExpectedCondition::presenceOfElementLocated($search));
			}
			
			$elements = $this->driver->findElements($search);
		}
		catch (TimeOutException $et)
		{
			if (!$byContent)
				return $this->findByContent($selector);
			
			throw new ElementNotFoundException($selector);
		}
		
		if (!$elements && !$byContent)
			return $this->findByContent($selector);
		
		$result = [];
		
		foreach ($elements as $element)
		{
			$result[] = new DomElement($element, $this->driver);
		}
		
		$this->collection = $result;
		
		return $this;
	}
	
	public function filter(callable $closure): IDomElementsCollection
	{
		$result = [];
		
		foreach ($this->collection as $el)
		{
			if (!$closure($el))
				continue;
			
			$result[] = $el;
		}
		
		return $this->asNewCollection($result);
	}
	
	public function each(callable $closure): IDomElementsCollection
	{
		foreach ($this->collection as $el)
		{
			$closure($el);
		}
		
		return $this;
	}
	
	public function isEmpty(): bool
	{
		return !$this->collection;
	}
	
	public function count(): int
	{
		return count($this->collection);
	}
	
	public function first(): ?IDomElement
	{
		return $this->collection ? reset($this->collection) : null;
	}
	
	public function last(): ?IDomElement
	{
		return $this->collection ? end($this->collection) : null;
	}
	
	/**
	 * @return IDomElement[]|[]
	 */
	public function get(): array
	{
		return $this->collection;
	}
	
	public function click(bool $hover = false): void
	{
		if ($this->isEmpty()) return;
		
		foreach ($this->collection as $element)
		{
			$element->click($hover);
		}
	}
	
	public function input(string $input): void
	{
		if ($this->isEmpty()) return;
		
		foreach ($this->collection as $element)
		{
			$element->input($input);
		}
	}
	
	public function getAttribute(string $name, bool $allowMissing = true): array
	{
		$result = [];
		
		if ($this->isEmpty()) return $result;
		
		foreach ($this->collection as $element)
		{
			$result[] = $element->getAttribute($name, $allowMissing); 
		}
		
		return $result;
	}
}