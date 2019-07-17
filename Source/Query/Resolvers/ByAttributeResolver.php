<?php
namespace SeTaco\Query\Resolvers;


use SeTaco\IQueryResolver;
use SeTaco\Query\ISelector;
use SeTaco\Exceptions\SeTacoException;
use Structura\Strings;


class ByAttributeResolver implements IQueryResolver
{
	protected function generateResolve(string $type, string $tag, string $value, bool $isCaseSensitive): string
	{
		$tag = "@$tag";
		
		if (!$isCaseSensitive)
		{
			$tag = "lower-case($tag)";
			$value = strtolower($value);
		}
		
		return "//body//{$type}[{$tag}=\"$value\"][not(self::script)]";
	}
	
	
	/**
	 * @param string $query
	 * @param bool $isCaseSensitive
	 * @return string|ISelector|null
	 */
	public function resolve(string $query, bool $isCaseSensitive)
	{
		if (!Strings::contains('=', $query))
			throw new SeTacoException('Attribute resolver must contain the = sign, "Name=Value"');
		
		[$key, $value] = explode('=', $query, 2);
		$input = '*';
		
		if (Strings::contains(' ', $key))
		{
			[$input, $key] = explode(' ', $key, 2);
		}
		
		return AbstractResolveHelper::generateResolve($input, $key, $value, $isCaseSensitive);
	}
}