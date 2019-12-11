<?php
namespace SeTaco;


interface IQuery extends IQueryAction, IRepeater
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
	
	/**
	 * @return static
	 */
	public function waitForElement(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IQuery;
	
	/**
	 * @return static
	 */
	public function waitForAnyElements($query, ?float $timeout = null, bool $isCaseSensitive = false): IQuery;
	
	/**
	 * @return static
	 */
	public function waitForElements($query, ?float $timeout = null, bool $isCaseSensitive = false): IQuery;
	
	/**
	 * @return static
	 */
	public function waitToDisappear(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IQuery;
	
	/**
	 * @return static
	 */
	public function waitAllToDisappear($query, ?float $timeout = null, bool $isCaseSensitive = false): IQuery;
	
	/**
	 * @return static
	 */
	public function waitAnyToDisappear($query, ?float $timeout = null, bool $isCaseSensitive = false): IQuery;
}