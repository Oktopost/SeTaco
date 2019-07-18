<?php
namespace SeTaco;


use SeTaco\IDomElement;
use SeTaco\IDomElementsCollection;


interface IQuery
{
	public function exists(string $query, ?float $timeout = null, bool $isCaseSensitive = false): bool;
	public function count(string $query, ?float $timeout = null, bool $isCaseSensitive = false): int;
	
	public function find(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function findFirst(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function findAll(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElementsCollection;
	
	public function tryFind(string $query, ?float $timeout = null, bool $isCaseSensitive = false): ?IDomElement;
	public function tryFindFirst(string $query, ?float $timeout = null, bool $isCaseSensitive = false): ?IDomElement;
	
	public function waitForElement(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function waitForElements(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function waitToDisappear(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function input(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function click(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function clickAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function hover(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function hoverAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function hoverAndClick(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function hoverAndClickAny(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
}