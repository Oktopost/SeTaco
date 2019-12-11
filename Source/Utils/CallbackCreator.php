<?php
namespace SeTaco\Utils;


use Traitor\TStaticClass;


class CallbackCreator
{
	use TStaticClass;
	
	
	public static function isNull(callable $callback): callable 
	{
		return function() use ($callback)
		{
			return is_null(call_user_func_array($callback, func_get_args()));
		};
	}
	
	public static function isEquals(callable $callback, $value): callable
	{
		return function() use ($callback, $value)
		{
			return $value == call_user_func_array($callback, func_get_args());
		};
	}
	
	public static function isNotEquals(callable $callback, $value): callable
	{
		return function() use ($callback, $value)
		{
			return $value != call_user_func_array($callback, func_get_args());
		};
	}
	
	public static function isSame(callable $callback, $value): callable
	{
		return function() use ($callback, $value)
		{
			return $value === call_user_func_array($callback, func_get_args());
		};
	}
	
	public static function isNotSame(callable $callback, $value): callable
	{
		return function() use ($callback, $value)
		{
			return $value !== call_user_func_array($callback, func_get_args());
		};
	}
}