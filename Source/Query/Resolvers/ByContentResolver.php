<?php
namespace SeTaco\Query\Resolvers;


use SeTaco\IQueryResolver;
use SeTaco\Query\ISelector;


class ByContentResolver implements IQueryResolver
{
	/**
	 * @param string $query
	 * @param bool $isCaseSensitive
	 * @return string|ISelector|null
	 */
	public function resolve(string $query, bool $isCaseSensitive)
	{
		$text = 'text()'; 
		
		if (!$isCaseSensitive)
		{
			$text = AbstractResolveHelper::toLowercase($text);
			$query = strtolower($query);
		}
		
		return "//body//*[contains($text, '$query')][not(self::script)]";
	}
}