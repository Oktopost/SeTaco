<?php

namespace SeTaco\Decorators;


use Facebook\WebDriver\Remote\RemoteWebElement;
use SeTaco\IDomElement;


class DomElementDecorator implements IDomElement
{
	/** @var callable */
	private $onUse;
	
	/** @var IDomElement */
	private $child;
	
	
	private function use(): IDomElement
	{
		$onUse = $this->onUse;
		$onUse();
		
		return $this->child;
	}
	
	
	public function __construct(IDomElement $child, callable $onUse)
	{
		$this->child = $child;
		$this->onUse = $onUse;
	}
	
	
	public function getRemoteWebElement(): RemoteWebElement
	{
		return $this->child->getRemoteWebElement();
	}
	
	public function click(): void
	{
		$this->use()->click();
	}
	
	public function input(string $input): void
	{
		$this->use()->input($input);
	}
	
	public function getName(bool $allowMissing = true): ?string
	{
		return $this->use()->getName($allowMissing);
	}
	
	public function getAttribute(string $name, bool $allowMissing = true): ?string
	{
		return $this->use()->getAttribute($name, $allowMissing);
	}
}