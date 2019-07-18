<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverSearchContext;

use SeTaco\Query\ISelector;
use SeTaco\Config\QueryConfig;
use SeTaco\Exceptions\Query\ElementNotFoundException;
use SeTaco\Exceptions\Query\ElementStillExistsException;
use SeTaco\Exceptions\Query\MultipleElementsExistException;


class Query implements IQuery
{
	/** @var BrowserSetup */
	private $setup;
	
	/** @var WebDriverSearchContext */
	private $context;
	
	/** @var RemoteWebDriver */
	private $driver;
	
	/** @var QueryConfig */
	private $config;
	
	
	private function getSelector(string $query, bool $isCaseSensitive = false): ISelector
	{
		return $this->config->resolve($query, $isCaseSensitive);
	}
	
	private function queryAllBySelector(ISelector $selector, ?float $timeout = null): IDomElementsCollection
	{
		$timeout = $this->config->getTimeout($timeout);
		$endTime = microtime(true) + $timeout;
		
		$searchObject = $selector->getDriverSelector();
		$elements = $this->driver->findElements($searchObject);
		
		while (!$elements && microtime(true) < $endTime)
		{
			usleep(50000);
			$elements = $this->driver->findElements($searchObject);
		}
		
		$domElements = [];
		
		foreach ($elements as $element)
		{
			$domElements[] = new DomElement($element, $this->setup);
		}
		
		return new DomElementsCollection($domElements);
	}
	
	private function queryAll(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElementsCollection
	{
		$selector = $this->getSelector($query, $isCaseSensitive);
		return $this->queryAllBySelector($selector, $timeout);
	}
	
	
	public function __construct(BrowserSetup $setup, WebDriverSearchContext $searchContext)
	{
		$this->setup = $setup;
		
		$this->config = $setup->QueryConfig;
		$this->driver = $setup->RemoteWebDriver;
		$this->context = $searchContext;
	}
	
	
	
	public function exists(string $query, ?float $timeout = null, bool $isCaseSensitive = false): bool
	{
		return !$this->findAll($query, $timeout, $isCaseSensitive)->isEmpty();
	}
	
	public function count(string $query, ?float $timeout = null, bool $isCaseSensitive = false): int
	{
		return $this->findAll($query, $timeout, $isCaseSensitive)->count();
	}
	
	public function find(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$selector = $this->getSelector($query, $isCaseSensitive);
		$all = $this->queryAllBySelector($selector, $timeout);
		
		if ($all->isOne())
		{
			return $all->first();
		}
		else if ($all->count() > 1)
		{
			throw new MultipleElementsExistException($selector);
		}
		else
		{
			throw new ElementNotFoundException($selector);
		}
	}
	
	public function findFirst(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$selector = $this->getSelector($query, $isCaseSensitive);
		$all = $this->queryAllBySelector($selector, $timeout);
		
		if (!$all->hasAny())
			throw new ElementNotFoundException($selector);
		
		return $all->first();
	}
	
	public function findAll(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElementsCollection
	{
		return $this->queryAll($query, $timeout, $isCaseSensitive);
	}
	
	public function tryFind(string $query, ?float $timeout = null, bool $isCaseSensitive = false): ?IDomElement
	{
		$all = $this->queryAll($query, $timeout, $isCaseSensitive);
		return $all->isOne() ? $all->first() : null; 
	}
	
	public function tryFindFirst(string $query, ?float $timeout = null, bool $isCaseSensitive = false): ?IDomElement
	{
		return $this->queryAll($query, $timeout, $isCaseSensitive)->first();
	}

	public function waitForElement(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->find($query, $timeout, $isCaseSensitive);
	}
	
	public function waitForElements(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->findAll($query, $timeout, $isCaseSensitive);
	}
	
	public function waitToDisappear(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$timeout = $this->config->getTimeout($timeout);
		$endTime = microtime(true) + $timeout;
		
		$selector = $this->getSelector($query, $isCaseSensitive);
		$searchObject = $selector->getDriverSelector();
		$elements = $this->driver->findElements($searchObject);
		
		while ($elements && microtime(true) < $endTime)
		{
			if (microtime(true) >= $endTime)
				throw new ElementStillExistsException($selector, $timeout);
			
			usleep(50000);
			$elements = $this->driver->findElements($searchObject);
		}
	}
	
	public function input(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->find($query, $timeout, $isCaseSensitive)->input($value);
	}
	
	public function click(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->find($query, $timeout, $isCaseSensitive)->click();
	}
	
	public function clickAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->findFirst($query, $timeout, $isCaseSensitive)->click();
	}
	
	public function hover(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->find($query, $timeout, $isCaseSensitive)->hover();
	}
	
	public function hoverAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->findFirst($query, $timeout, $isCaseSensitive)->hover();
	}
	
	public function hoverAndClick(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->find($query, $timeout, $isCaseSensitive)->hover()->click();
	}
	
	public function hoverAndClickAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->findFirst($query, $timeout, $isCaseSensitive)->hover()->click();
	}
}