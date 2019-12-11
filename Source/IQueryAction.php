<?php
namespace SeTaco;


interface IQueryAction
{
	public function input(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function clearAndInput(string $query, string $value, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function click(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function clickAny($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function hover(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function hoverAny($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function hoverAndClick(string $query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
	public function hoverAndClickAny($query, ?float $timeout = null, bool $isCaseSensitive = false): IDomElement;
}