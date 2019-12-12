<?php
namespace SeTaco\Query\Resolvers;


class AbstractResolveHelper
{
	public static function generateResolve(string $type, string $tag, string $value, bool $isCaseSensitive = false): string
	{
		$tag = "@$tag";
		
		if (!$isCaseSensitive)
		{
			$tag = self::toLowercase("$tag");
			$value = strtolower($value);
		}
		
		
		return "//body//{$type}[{$tag}=\"$value\"][not(self::script)]";
	}
	
	public static function toLowercase(string $item): string
	{
		return "translate($item, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')";
	}
}