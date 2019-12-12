<?php
namespace SeTaco\Query\Resolvers;


use SeTaco\IQueryResolver;
use SeTaco\Query\ISelector;


class CallbackQueryResolver implements IQueryResolver
{
	/** @var callable  */
	private $callable;
	
	
	/**
	 * @param callable|null $closure
	 */
	public function __construct($closure = null)
	{
		$this->callable = $closure;
	}
	
	
	/**
	 * @param string $query
	 * @param bool $isCaseSensitive
	 * @return string|ISelector|null
	 */
	public function resolve(string $query, bool $isCaseSensitive = false): ?string
	{
		return call_user_func($this->callable, $query, $isCaseSensitive) ?? null;
	}
}