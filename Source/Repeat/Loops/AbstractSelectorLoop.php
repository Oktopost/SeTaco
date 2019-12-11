<?php
namespace SeTaco\Repeat\Loops;


use SeTaco\IQuery;
use Structura\Arrays;


abstract class AbstractSelectorLoop extends AbstractLoop
{
	/** @var bool */
	private $isCaseSensitive;
	
	/** @var array */
	private $selector;
	
	/** @var IQuery */
	private $query;
	
	
	protected function selector(): array { return $this->selector; }
	protected function query(): IQuery { return $this->query; }
	protected function isCaseSensitive(): bool { return $this->isCaseSensitive; }
	
	
	/**
	 * AbstractSelectorLoop constructor.
	 * @param IQuery $query
	 * @param mixed $selector
	 * @param float $delay
	 * @param float $timeout
	 * @param bool $isCaseSensitive
	 */
	public function __construct(IQuery $query, $selector, float $delay, float $timeout, bool $isCaseSensitive)
	{
		parent::__construct($delay, $timeout);
		
		$this->selector	= Arrays::toArray($selector);
		$this->query = $query;
		$this->isCaseSensitive = $isCaseSensitive;
	}
}