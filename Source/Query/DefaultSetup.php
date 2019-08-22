<?php
namespace SeTaco\Query;


use SeTaco\Config\QueryConfig;
use SeTaco\Query\Resolvers\ByContentResolver;
use SeTaco\Query\Resolvers\ExactTextResolver;
use SeTaco\Query\Resolvers\ByAttributeResolver;
use SeTaco\Query\Resolvers\AbstractResolveHelper;

use Traitor\TStaticClass;


class DefaultSetup
{
	use TStaticClass;
	
	
	public static function setup(QueryConfig $config)
	{
		$config->addResolver('attr', new ByAttributeResolver());
		$config->addResolver(['txt', 'content'], new ByContentResolver());
		
		$config->addResolver(['etxt'], new ExactTextResolver());
		
		$config->addResolver('placeholder', function(string $query, bool $isCaseSensitive)
		{
			return AbstractResolveHelper::generateResolve('*', 'placeholder', $query, $isCaseSensitive);
		});
		
		$config->addResolver(['v', 'value'], function(string $query, bool $isCaseSensitive)
		{
			return AbstractResolveHelper::generateResolve('input', 'value', $query, $isCaseSensitive);
		});
		
		$config->addResolver('input', function(string $query, bool $isCaseSensitive)
		{
			return AbstractResolveHelper::generateResolve('input', 'type', $query, $isCaseSensitive);
		});
	}
}