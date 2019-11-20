<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverSearchContext;
use Facebook\WebDriver\Exception\WebDriverException;

use SeTaco\Exceptions\Element\ElementNotEditableException;
use SeTaco\Query\ISelector;
use SeTaco\Config\QueryConfig;

use SeTaco\Exceptions\Element\ElementException;
use SeTaco\Exceptions\Element\ElementObstructedException;
use SeTaco\Exceptions\Element\DomElementNotVisibleException;

use SeTaco\Exceptions\QueryException;
use SeTaco\Exceptions\Query\ElementNotFoundException;
use SeTaco\Exceptions\Query\ElementStillExistsException;
use SeTaco\Exceptions\Query\MultipleElementsExistException;
use SeTaco\Exceptions\Query\QueriedElementNotEditableException;
use SeTaco\Exceptions\Query\QueriedElementNotClickableException;


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
		$elements = $this->context->findElements($searchObject);
		
		while (!$elements && microtime(true) < $endTime)
		{
			usleep(50000);
			$elements = $this->context->findElements($searchObject);
		}
		
		$domElements = [];
		
		/** @var RemoteWebElement[] $elements */
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
	
	/** @noinspection PhpInconsistentReturnPointsInspection */
	private function executeRetryCallback(callable $c, ?float $timeout = null)
	{
		$timeout = $this->config->getTimeout($timeout);
		$endTime = microtime(true) + $timeout;
		
		while (true)
		{
			try
			{
				return $c(max(0.0, $endTime - microtime(true)));
			}
			catch (WebDriverException $wde)
			{
				$e = $wde;
			}
			catch (ElementException $ee)
			{
				$e = $ee;
			}
			catch (QueryException $qe)
			{
				$e = $qe;
			}
			
			if (microtime(true) > $endTime)
				throw $e;
			
			usleep(50000);
		}
	}
	
	private function unsafeClickElement(string $query, bool $isCaseSensitive, IDomElement $e)
	{
		$selector = $this->getSelector($query, $isCaseSensitive);
		
		try
		{
			$e->click();
		}
		catch (DomElementNotVisibleException $ev)
		{
			throw new QueriedElementNotClickableException($selector, $ev->getMessage());
		}
		catch (ElementObstructedException $eo)
		{
			throw new QueriedElementNotClickableException($selector, $eo->getMessage());
		}
	}
	
	private function unsafeInputElement(string $query, bool $isCaseSensitive, IDomElement $e, $value)
	{
		$selector = $this->getSelector($query, $isCaseSensitive);
		
		try
		{
			$e->input($value);
		}
		catch (ElementNotEditableException $eee)
		{
			throw new QueriedElementNotEditableException($selector, $eee->getMessage());
		}
		catch (DomElementNotVisibleException $ev)
		{
			throw new QueriedElementNotEditableException($selector, $ev->getMessage());
		}
		catch (ElementObstructedException $eo)
		{
			throw new QueriedElementNotEditableException($selector, $eo->getMessage());
		}
	}
	
	private function unsafeClick(callable $search, string $query, ?float $timeout, bool $isCaseSensitive)
	{
		$this->executeRetryCallback(
			function(float $timeout)
				use ($search, $query, $isCaseSensitive)
			{
				$el = $search($query, $timeout, $isCaseSensitive);
				$this->unsafeClickElement($query, $isCaseSensitive, $el);
			},
			$timeout);
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
	
	public function text(string $query, ?float $timeout = null, bool $isCaseSensitive = false): string
	{
		return $this->find($query, $timeout, $isCaseSensitive)->getRemoteWebElement()->getText();
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
		$elements = $this->context->findElements($searchObject);
		
		while ($elements && microtime(true) < $endTime)
		{
			if (microtime(true) >= $endTime)
				throw new ElementStillExistsException($selector, $timeout);
			
			usleep(50000);
			$elements = $this->context->findElements($searchObject);
		}
	}
	
	public function input(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->executeRetryCallback(
			function(float $timeout)
				use ($query, $isCaseSensitive, $value)
			{
				$el = $this->find($query, $timeout, $isCaseSensitive);
				$this->unsafeInputElement($query, $isCaseSensitive, $el, $value);
			},
			$timeout);
	}
	
	public function click(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->unsafeClick(
			function (string $query, ?float $timeout = null, bool $isCaseSensitive = false)
			{
				return $this->find($query, $timeout, $isCaseSensitive);
			},
			$query, $timeout, $isCaseSensitive
		);
	}
	
	public function clickAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->unsafeClick(
			function (string $query, ?float $timeout = null, bool $isCaseSensitive = false)
			{
				return $this->findFirst($query, $timeout, $isCaseSensitive);
			},
			$query, $timeout, $isCaseSensitive
		);
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
		$this->unsafeClick(
			function (string $query, ?float $timeout = null, bool $isCaseSensitive = false)
			{
				$el = $this->find($query, $timeout, $isCaseSensitive)->hover();
				return $el;
			},
			$query, $timeout, $isCaseSensitive
		);
	}
	
	public function hoverAndClickAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void
	{
		$this->unsafeClick(
			function (string $query, ?float $timeout = null, bool $isCaseSensitive = false)
			{
				$el = $this->findFirst($query, $timeout, $isCaseSensitive)->hover();
				return $el;
			},
			$query, $timeout, $isCaseSensitive
		);
	}
}