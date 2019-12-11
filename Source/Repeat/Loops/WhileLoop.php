<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\Exceptions\RepeaterException;


class WhileLoop extends AbstractLoop
{
	private $callback;
	
	
	protected function execute(): bool
	{
		$callback = $this->callback;
		$result = $callback();
		
		if (!is_bool($result))
		{
			throw new RepeaterException('Return value of the callback must be a boolean');
		}
		
		return $result;
	}
	
	protected function timedOut(): bool
	{
		throw new RepeaterException("Callback still returning true, after {$this->timeout()} seconds");
	}
	
	
	public function __construct(callable $callback, $delay, $timeout)
	{
		parent::__construct($delay, $timeout);
		$this->callback = $callback;
	}
}