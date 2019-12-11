<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\Exceptions\RepeaterException;


class WhileNotEqualsLoop extends AbstractLoop
{
	private $callback;
	private $value;
	
	
	protected function execute(): bool
	{
		$callback = $this->callback;
		return $callback() != $this->value;
	}
	
	protected function timedOut(): bool
	{
		$value = ValueConverter::toString($this->value);
		throw new RepeaterException("Callback still did not return $value, after {$this->timeout()} seconds");
	}
	
	
	public function __construct(callable $callback, $value, $delay, $timeout)
	{
		parent::__construct($delay, $timeout);
		$this->callback = $callback;
		$this->value = $value;
	}
}