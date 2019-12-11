<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\Repeat\ILoop;


abstract class AbstractLoop implements ILoop
{
	private $delay		= 0.0;
	private $timeout	= 0.0;
	private $endTime	= null;
	
	
	protected function delay(): float { return $this->delay; }
	protected function timeout(): float { return $this->timeout; }
	
	
	protected abstract function execute(): bool;
	protected abstract function timedOut(): bool;
	
	
	public function __construct(float $delay, float $timeout)
	{
		$this->delay = $delay;
		$this->timeout = $timeout;
	}
	
	
	public function loop(): bool
	{
		if (!$this->endTime)
		{
			$this->endTime = microtime(true) + $this->timeout;
			return $this->execute();
		}
		
		usleep($this->delay * 1000000);
		$result = $this->execute();
		
		if (!$result)
		{
			return false;
		}
		else if ($this->endTime <= microtime(true))
		{
			return $this->timedOut();
		}
		else
		{
			return true;
		}
	}
}