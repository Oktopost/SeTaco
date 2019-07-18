<?php
namespace SeTaco;



use SeTaco\IDomElement;

interface IDomElementsCollection
{
	public function filter(callable $closure): IDomElementsCollection;
	public function each(callable $closure): IDomElementsCollection;
	
	public function isEmpty(): bool;
	public function count(): int;
	public function isOne(): bool;
	public function hasAny(): bool;
	
	public function first(): ?IDomElement;
	public function last(): ?IDomElement;
	
	/**
	 * @return IDomElement[]
	 */
	public function get(): array;
}