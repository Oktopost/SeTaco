<?php
namespace SeTaco\Query\Resolvers;


class AbstractResolveHelper
{
	public static function generateResolve(string $type, string $tag, string $value, bool $isCaseSensitive): string
	{
		$tag = "@$tag";
		
		if (!$isCaseSensitive)
		{
			$tag = "lower-case($tag)";
			$value = strtolower($value);
		}
		
		
		return "//body//{$type}[{$tag}=\"$value\"][not(self::script)]";
	}
}