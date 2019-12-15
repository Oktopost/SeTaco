<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\Exceptions\FatalSeTacoException;


class WhileThrowingLoop extends AbstractLoop
{
	private $callback;
	
	/** @var \Exception */
	private $exception = null;
	
	
	protected function execute(): bool
	{
		$callback = $this->callback;
		
		try
		{
			$callback();
			return false;
		}
		catch (\Exception $e)
		{
			$this->exception = $e;
			return true;
		}
	}
	
	protected function timedOut(): bool
	{
		if (!$this->exception)
		{
			throw new FatalSeTacoException(self::class . '::execute was never called!');
		}
		
		throw $this->exception;
	}
	
	
	public function __construct(callable $callback, $delay, $timeout)
	{
		parent::__construct($delay, $timeout);
		$this->callback = $callback;
	}
}