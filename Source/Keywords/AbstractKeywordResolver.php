<?php
namespace SeTaco\Keywords;


use SeTaco\IKeywordResolver;
use Structura\Strings;


abstract class AbstractKeywordResolver implements IKeywordResolver
{
	/** @var string */
	private $prefix;
	
	/** @var array */
	private $map;
	
	
	protected abstract function getTemplate(string $keyword): string;
	
	
	protected function getMap(): array
	{
		return $this->map;
	}
	
	protected function canResolve(string $keyword): bool
	{
		return isset($this->map[$keyword]);
	}
	
	protected function getPrefix(): ?string
	{
		return $this->prefix;
	}
	
	
	public function resolve(string $keyword): ?string
	{
		if ($this->canResolve($keyword))
			return $this->getTemplate($this->map[$keyword]);
		
		if ($this->prefix && Strings::isStartsWith($keyword, $this->prefix))
		{
			$keyword = Strings::replace($keyword, $this->prefix, '');
			
			if ($this->canResolve($keyword))
				return $this->getTemplate($this->map[$keyword]);
		}
		
		return null;
	}
	
	
	/**
	 * @param string|array $prefix
	 * @param array|null $map
	 */
	public function __construct($prefix, $map = null)
	{
		if(is_array($prefix))
		{
			$map = $prefix;
			$prefix = null;
		}
		
		$this->prefix = $prefix;
		$this->map = $map;
	}
}