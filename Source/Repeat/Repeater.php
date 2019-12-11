<?php
namespace SeTaco\Repeat;


use SeTaco\IQuery;
use SeTaco\IRepeater;
use SeTaco\IDomElement;
use SeTaco\IRepeatAction;
use SeTaco\Config\QueryConfig;
use SeTaco\Exceptions\SeTacoException;

use SeTaco\Repeat\Loops\WhileLoop;
use SeTaco\Repeat\Loops\WhileNullLoop;
use SeTaco\Repeat\Loops\WhileSameLoop;
use SeTaco\Repeat\Loops\WhileEqualsLoop;
use SeTaco\Repeat\Loops\WhileNotSameLoop;
use SeTaco\Repeat\Loops\WhileNotNullLoop;
use SeTaco\Repeat\Loops\WhileThrowingLoop;
use SeTaco\Repeat\Loops\WhileNotEqualsLoop;
use SeTaco\Repeat\Loops\WhileAnyElementExistsLoop;
use SeTaco\Repeat\Loops\WhileAnyElementMissingLoop;
use SeTaco\Repeat\Loops\WhileAllElementsExistsLoop;
use SeTaco\Repeat\Loops\WhileAllElementsMissingLoop;


class Repeater implements IRepeater, IRepeatAction
{
	/** @var IQuery */
	private $parent;
	
	/** @var QueryConfig */
	private $config;
	
	/** @var callable */
	private $callbacks = [];
	
	
	/** @var ILoop|null */
	private $loop = null;
	
	/** @var callable|null */
	private $action = null;
	
	private $useInvokerForAction = false;
	
	
	private function invokeCallbacks(): void
	{	
		foreach ($this->callbacks as $callback)
		{
			$this->config->invokeCallback($callback);
		}
	}
	
	private function start()
	{
		if (!$this->loop)
		{
			throw new SeTacoException("Incorrect use of " . Repeater::class);
		}
		
		while ($this->loop->loop())
		{
			$this->invokeCallbacks();
		}
		
		$action = $this->action;
		
		if ($this->useInvokerForAction)
		{
			return $this->config->invokeCallback($action);
		}
		else
		{
			return $action($this->parent);
		}
	}
	
	
	public function __construct(IQuery $parent, QueryConfig $config, ?callable $invoker = null)
	{
		$this->parent = $parent;
		$this->config = $config;
	}
	
	
	public function while(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileLoop($callback, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	public function whileNull(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileNullLoop($callback, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	public function whileNotNull(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileNotNullLoop($callback, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	public function whileEquals(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileEqualsLoop($callback, $value, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	public function whileNotEquals(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileNotEqualsLoop($callback, $value, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	public function whileSame(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileSameLoop($callback, $value, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	public function whileNotSame(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileNotSameLoop($callback, $value, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	public function whileThrowing(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileThrowingLoop($callback, $delay, $this->config->getTimeout($timeout));
		return $this;
	}
	
	
	public function whileElementExists(string $selector, bool $isCaseSensitive, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileAllElementsExistsLoop($this->parent, [$selector],
			$delay, $this->config->getTimeout($timeout), $isCaseSensitive);
		return $this;
	}
	
	public function whileElementMissing(string $selector, bool $isCaseSensitive, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileAllElementsMissingLoop($this->parent, [$selector],
			$delay, $this->config->getTimeout($timeout), $isCaseSensitive);
		return $this;
	}
	
	public function whileAnyElementExists(array $selector, bool $isCaseSensitive, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileAnyElementExistsLoop($this->parent, $selector,
			$delay, $this->config->getTimeout($timeout), $isCaseSensitive);
		return $this;
	}
	
	public function whileAnyElementMissing(array $selector, bool $isCaseSensitive, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileAnyElementMissingLoop($this->parent, $selector,
			$delay, $this->config->getTimeout($timeout), $isCaseSensitive);
		return $this;
	}
	
	public function whileAllElementsExist(array $selector, bool $isCaseSensitive, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileAllElementsExistsLoop($this->parent, $selector,
			$delay, $this->config->getTimeout($timeout), $isCaseSensitive);
		return $this;
	}
	
	public function whileAllElementsMissing(array $selector, bool $isCaseSensitive, float $delay = 0.1, ?float $timeout = null): IRepeatAction
	{
		$this->loop = new WhileAllElementsMissingLoop($this->parent, $selector,
			$delay, $this->config->getTimeout($timeout), $isCaseSensitive);
		return $this;
	}
	
	
	public function input(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $value, $timeout, $isCaseSensitive)
		{
			return $parent->input($query, $value, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function clearAndInput(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $value, $timeout, $isCaseSensitive)
		{
			return $parent->clearAndInput($query, $value, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function click(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $timeout, $isCaseSensitive)
		{
			return $parent->click($query, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function clickAny($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $timeout, $isCaseSensitive)
		{
			return $parent->clickAny($query, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function hover(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $timeout, $isCaseSensitive)
		{
			return $parent->hover($query, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function hoverAny($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $timeout, $isCaseSensitive)
		{
			return $parent->hoverAny($query, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function hoverAndClick(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $timeout, $isCaseSensitive)
		{
			return $parent->hoverAndClick($query, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function hoverAndClickAny($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement
	{
		$this->action = function (IQuery $parent) use ($query, $timeout, $isCaseSensitive)
		{
			return $parent->hoverAndClickAny($query, $timeout, $isCaseSensitive);
		};
		
		return $this->start();
	}
	
	public function addCallback(callable $callback): IRepeatAction
	{
		$this->callbacks[] = $callback;
		return $this;
	}
	
	public function execute(callable $callback): void
	{
		$this->action = $callback;
		$this->start();
	}
}