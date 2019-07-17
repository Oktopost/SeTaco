<?php
namespace SeTaco\Config;


use SeTaco\IQueryResolver;
use SeTaco\Exceptions\SeTacoException;
use SeTaco\Exceptions\FatalSeTacoException;

use SeTaco\Query\ISelector;
use SeTaco\Query\Resolvers\CallbackQueryResolver;

use SeTaco\Query\Selector;
use Structura\Strings;


class QueryConfig
{
	/** @var IQueryResolver[] */
	private $queryResolvers = [];
	
	/** @var IQueryResolver[] */
	private $genericResolvers = [];
	
	
	private function toSelector($selector, ?IQueryResolver $resolver = null): ISelector
	{
		if ($selector instanceof ISelector || is_null($selector))
		{
			$result = $selector;
		}
		else if (!is_string($selector))
		{
			throw new FatalSeTacoException('Resolver must return string or ISelector object');
		}
		else if (Strings::isStartsWith($selector, '//'))
		{
			$result = Selector::byXPath($selector);
		}
		else
		{
			$result = Selector::byCSS($selector);
		}
		
		if ($resolver)
		{
			$result->setResolver($resolver);
		}
		
		return $result;
	}
	
	private function runSingleResolver(IQueryResolver $resolver, string $query, bool $isCaseSensitive): ?ISelector
	{
		$result = $resolver->resolve($query, $isCaseSensitive);
		
		if (!is_null($result))
		{
			return $this->toSelector($result);
		}
		else
		{
			return null;
		}
	}
	
	private function resolveByKeyword(string $query, bool $isCaseSensitive): ?ISelector
	{
		if (!Strings::contains($query, ':'))
			return null;
		
		[$key, $queryString] = explode(':', $query, 2);
		$resolver = $this->queryResolvers[$key] ?? null;
		
		if (!$resolver)
			return null;
		
		return $this->runSingleResolver($resolver, $queryString, $isCaseSensitive);
	}
	
	private function resolveGenerics(string $query, bool $isCaseSensitive): ?ISelector
	{
		foreach ($this->genericResolvers as $resolver)
		{
			$result = $this->runSingleResolver($resolver, $query, $isCaseSensitive);
			
			if (!is_null($result))
			{
				return $result;
			}
		}
		
		return null;
	}
	
	
	private function getResolver($resolver): IQueryResolver
	{
		if ($resolver instanceof IQueryResolver)
		{
			return $resolver;
		}
		else if (is_string($resolver))
		{
			return new $resolver();
		}
		else if (is_callable($resolver))
		{
			return new CallbackQueryResolver($resolver);
		}
		else
		{
			throw new FatalSeTacoException('Unexpected type for resolver');
		}
	}
	
	
	/**
	 * @param string $key
	 * @param IQueryResolver|callable|string $resolver
	 */
	public function addResolver(string $key, $resolver): void
	{
		if (isset($this->queryResolvers[$key]))
			throw new SeTacoException("Resolver for the key $key, is already defined");
		
		$resolver = $this->getResolver($resolver);
		$this->queryResolvers[$key] = $resolver;
	}
	
	/**
	 * @param IQueryResolver|callable|string $resolver
	 */
	public function addGenericResolver($resolver): void
	{
		$this->genericResolvers[] = $this->getResolver($resolver);
	}
	
	
	public function resolve(string $query, bool $isCaseSensitive = false): ISelector
	{
		$selector = $this->resolveByKeyword($query, $isCaseSensitive);
		
		if (!$selector)
		{
			$this->resolveGenerics($query, $isCaseSensitive);
		}
		
		if (!$selector)
		{
			$this->toSelector($query);
		}
			
		return $selector;
	}
}