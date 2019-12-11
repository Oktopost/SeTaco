<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\Exceptions\RepeaterException;


class WhileNotNullLoop extends AbstractLoop
{
	private $callback;
	
	
	protected function execute(): bool
	{
		$callback = $this->callback;
		return !is_null($callback());
	}
	
	protected function timedOut(): bool
	{
		throw new RepeaterException("Callback still not returning null, after {$this->timeout()} seconds");
	}
	
	
	public function __construct(callable $callback, $delay, $timeout)
	{
		parent::__construct($delay, $timeout);
		$this->callback = $callback;
	}
}