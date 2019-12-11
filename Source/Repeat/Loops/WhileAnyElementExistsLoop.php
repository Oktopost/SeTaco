<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\IQuery;
use SeTaco\Exceptions\Query\ElementNotFoundException;


class WhileAnyElementExistsLoop extends AbstractSelectorLoop
{
	protected function execute(): bool
	{
		return $this->query()->existsAny($this->selector(), 0.0, $this->isCaseSensitive());
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