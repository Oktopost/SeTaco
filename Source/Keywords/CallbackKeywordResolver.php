<?php
namespace SeTaco\Keywords;


use SeTaco\IKeywordResolver;


class CallbackKeywordResolver implements IKeywordResolver
{
	/** @var string|null */
	private $prefix;
	
	/** @var callable  */
	private $callable;
	
	
	/**
	 * @param string|callable $prefix
	 * @param callable|null $closure
	 */
	public function __construct($prefix, $closure = null)
	{
		if (is_callable($prefix))
		{
			$closure = $prefix;
			$prefix = null;
		}
		
		$this->prefix = $prefix;
		$this->callable = $closure;
	}
	
	
	public function resolve(string $keyword): ?string
	{
		return call_user_func($this->callable, $keyword, $this->prefix) ?? null;
	}
}