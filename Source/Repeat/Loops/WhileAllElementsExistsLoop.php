<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\IQuery;
use SeTaco\Exceptions\Query\ElementNotFoundException;


class WhileAllElementsExistsLoop extends AbstractSelectorLoop
{
	protected function execute(): bool
	{
		return $this->query()->existsAll($this->selector(), 0.0, $this->isCaseSensitive());
	}
	
	protected function timedOut(): bool
	{
		throw new ElementNotFoundException($this->selector());
	}
	
	
	public function __construct(IQuery $query, array $selector, float $delay, float $timeout, bool $isCaseSensitive)
	{
		parent::__construct($query, $selector, $delay, $timeout, $isCaseSensitive);
	}
}