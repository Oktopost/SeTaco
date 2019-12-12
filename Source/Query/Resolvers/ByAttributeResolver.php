<?php
namespace SeTaco\Query\Resolvers;


use SeTaco\IQueryResolver;
use SeTaco\Query\ISelector;
use SeTaco\Exceptions\SeTacoException;
use Structura\Strings;


class ByAttributeResolver implements IQueryResolver
{
	/**
	 * @param string $query
	 * @param bool $isCaseSensitive
	 * @return string|ISelector|null
	 */
	public function resolve(string $query, bool $isCaseSensitive = false)
	{
		if (!Strings::contains($query, '='))
			throw new SeTacoException('Attribute resolver must contain the = sign, "Name=Value". Got ' . $query);
		
		[$key, $value] = explode('=', $query, 2);
		$input = '*';
		
		if (Strings::contains($key, ' '))
		{
			[$input, $key] = explode(' ', $key, 2);
		}
		
		return AbstractResolveHelper::generateResolve($input, $key, $value, $isCaseSensitive);
	}
}