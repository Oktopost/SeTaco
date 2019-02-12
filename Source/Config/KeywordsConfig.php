<?php
namespace SeTaco\Config;


use Objection\LiteSetup;
use Objection\LiteObject;

use SeTaco\IKeywordResolver;
use SeTaco\Keywords\CallbackKeywordResolver;
use SeTaco\Keywords\ConstKeywordResolver;


/**
 * @property IKeywordResolver[] $KeywordResolvers
 */
class KeywordsConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'KeywordResolvers'	=> LiteSetup::createInstanceArray(IKeywordResolver::class)
		];
	}
	
	
	/**
	 * @param IKeywordResolver|callable|array|string $resolver
	 * @param array|string|null $selector
	 */
	public function addResolver($resolver, $selector = null): void
	{
		if ($resolver instanceof IKeywordResolver)
		{
			$this->KeywordResolvers[] = $resolver;
			return;
		}
		
		if (is_callable($resolver))
		{
			$this->KeywordResolvers[] = new CallbackKeywordResolver($resolver);
			return;
		}
	
		if (is_string($resolver))
		{
			if (is_array($selector))
			{
				$this->KeywordResolvers[] = new ConstKeywordResolver($resolver, $selector);
			}
			else if (is_string($selector))
			{
				$this->KeywordResolvers[] = new ConstKeywordResolver([$resolver => $selector]);
			}
			
			return;
		}
		
		if (is_array($resolver))
		{
			$this->KeywordResolvers[] = new ConstKeywordResolver($resolver);
			return;
		}
		
		throw new \Exception('Failed to add keywords resolver');
	}
}