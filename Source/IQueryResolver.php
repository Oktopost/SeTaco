<?php
namespace SeTaco;


use SeTaco\Query\ISelector;


interface IQueryResolver
{
	/**
	 * @param string $query
	 * @param bool $isCaseSensitive
	 * @return string|ISelector|null
	 */
	public function resolve(string $query, bool $isCaseSensitive);
}