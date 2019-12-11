<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\IQuery;
use SeTaco\Exceptions\Query\ElementFoundException;


class WhileAnyElementMissingLoop extends AbstractSelectorLoop
{
	protected function execute(): bool
	{
		foreach ($this->selector() as $selector)
		{
			if (!$this->query()->exists($selector, 0.0, $this->isCaseSensitive()))
			{
				return true;
			}
		}
		
		return false;
	}
	
	protected function timedOut(): bool
	{
		throw new ElementFoundException($this->selector());
	}
	
	
	public function __construct(IQuery $query, array $selector, float $delay, float $timeout, bool $isCaseSensitive)
	{
		parent::__construct($query, $selector, $delay, $timeout, $isCaseSensitive);
	}
}