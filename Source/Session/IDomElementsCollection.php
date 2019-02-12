<?php
namespace SeTaco\Session;



interface IDomElementsCollection
{
	public function find(string $selector): IDomElementsCollection;
	public function findMany(array $selectors): IDomElementsCollection;
	
	public function filter(callable $closure): IDomElementsCollection;
	public function each(callable $closure): IDomElementsCollection;
	
	public function isEmpty(): bool;
	public function count(): int;
	
	public function first(): ?IDomElement;
	public function last(): ?IDomElement;
	
	/**
	 * @return IDomElement[]|[]
	 */
	public function get(): array;
	
	public function click(bool $hover = false): void;
	public function input(string $input): void;
	public function getAttribute(string $name, bool $allowMissing = true): array;
}