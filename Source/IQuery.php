<?php
namespace SeTaco;


interface IQuery
{
	public function exists(string $query, ?float $timeout = null, bool $isCaseSensitive = false): bool;
	public function existsAny(array $query, ?float $timeout = null, bool $isCaseSensitive = false): bool;
	public function existsAll(array $query, ?float $timeout = null, bool $isCaseSensitive = false): bool;
	
	public function text(string $query, ?float $timeout = null, bool $isCaseSensitive = false): string;
	
	public function count($query, ?float $timeout = null, bool $isCaseSensitive = false): int;
	
	public function find(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function findFirst($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function findAll($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElementsCollection;
	
	public function tryFind(string $query, ?float $timeout = null, bool $isCaseSensitive = false): ?IDomElement;
	public function tryFindFirst($query, ?float $timeout = null, bool $isCaseSensitive = false): ?IDomElement;
	
	public function waitForElement(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function waitForAnyElements($query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function waitForElements($query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function waitToDisappear(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function waitAllToDisappear($query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function waitAnyToDisappear($query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function input(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function clearAndInput(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function click(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function clickAny($query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function hover(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function hoverAny($query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	
	public function hoverAndClick(string $query, ?float $timeout = null, bool $isCaseSensitive = false): void;
	public function hoverAndClickAny($query, ?float $timeout = null, bool $isCaseSensitive = false): void;
}