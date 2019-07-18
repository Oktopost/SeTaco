<?php
namespace SeTaco;


use Structura\Arrays;


class DomElementsCollection implements IDomElementsCollection
{
	/** @var IDomElement[]  */
	private $collection = [];
	
	
	/**
	 * @param IDomElement[] $elements
	 */
	public function __construct(array $elements = [])
	{
		$this->collection = $elements;
	}
	
	
	public function filter(callable $closure): IDomElementsCollection
	{
		$result = [];
		
		foreach ($this->collection as $el)
		{
			if (!$closure($el))
				continue;
			
			$result[] = $el;
		}
		
		return new DomElementsCollection($result);
	}
	
	public function each(callable $closure): IDomElementsCollection
	{
		foreach ($this->collection as $el)
		{
			$closure($el);
		}
		
		return $this;
	}
	
	public function isEmpty(): bool
	{
		return !$this->collection;
	}
	
	public function count(): int
	{
		return count($this->collection);
	}
	
	public function isOne(): bool
	{
		return count($this->collection) == 1;
	}
	
	public function hasAny(): bool
	{
		return count($this->collection) > 0;
	}
	
	public function first(): ?IDomElement
	{
		return $this->collection ? Arrays::first($this->collection) : null;
	}
	
	public function last(): ?IDomElement
	{
		return $this->collection ? Arrays::last($this->collection) : null;
	}
	
	/**
	 * @return IDomElement[]|[]
	 */
	public function get(): array
	{
		return $this->collection;
	}
}