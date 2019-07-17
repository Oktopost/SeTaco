<?php
namespace SeTaco\Query;


use SeTaco\IQueryResolver;
use SeTaco\Exceptions\FatalSeTacoException;
use Facebook\WebDriver\WebDriverBy;


class Selector implements ISelector
{
	private $type;
	private $query;
	private $originalQuery;
	
	/** @var IQueryResolver|null */
	private $resolver = null;
	
	
	public function __construct(string $type, string $query, ?string $original = null)
	{
		$this->type = $type;
		$this->query = $query;
		$this->originalQuery = $original ?? $query;
	}
	
	
	public function setResolver(IQueryResolver $resolver)
	{
		$this->resolver = $resolver;
	}
	
	
	public function type(): string
	{
		return $this->type;
	}
	
	public function query(): string
	{
		return $this->query;
	}
	
	public function originalQuery(): string
	{
		return $this->originalQuery;
	}
	
	public function isSameAsOriginal(): bool
	{
		return $this->query == $this->originalQuery;
	}
	
	public function setOriginal(string $original): void
	{
		$this->originalQuery = $original;
	}
	
	public function resolver(): ?IQueryResolver
	{
		return $this->resolver;
	}
	
	
	public function getDriverSelector(): WebDriverBy
	{
		switch ($this->type)
		{
			case SelectorType::ID:
				return WebDriverBy::id($this->query);
				
			case SelectorType::NAME:
				return WebDriverBy::name($this->query);
				
			case SelectorType::CSS:
				return WebDriverBy::cssSelector($this->query);
				
			case SelectorType::XPATH:
				return WebDriverBy::xpath($this->query);
				
			case SelectorType::TAG_NAME:
				return WebDriverBy::tagName($this->query);
				
			default:
				throw new FatalSeTacoException("Unexpected selector type: {$this->type}");
		}
	}
	
	
	public static function byCSS(string $query, ?string $original = null): Selector
	{
		return new Selector(SelectorType::CSS, $query, $original);
	}
	
	public static function byXPath(string $query, ?string $original = null): Selector
	{
		return new Selector(SelectorType::XPATH, $query, $original);
	}
	
	public static function byID(string $query, ?string $original = null): Selector
	{
		return new Selector(SelectorType::ID, $query, $original);
	}
	
	public static function byName(string $query, ?string $original = null): Selector
	{
		return new Selector(SelectorType::NAME, $query, $original);
	}
	
	public static function byTag(string $query, ?string $original = null): Selector
	{
		return new Selector(SelectorType::TAG_NAME, $query, $original);
	}
}