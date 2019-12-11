<?php
namespace SeTaco\Repeat\Loops;


use Traitor\TStaticClass;


class ValueConverter
{
	use TStaticClass;
	
	
	public static function toString($value): string
	{
		if (!($value instanceof \stdClass) && is_object($value))
		{
			return (string)$value;
		}
		
		return json_encode($value);
	}
}