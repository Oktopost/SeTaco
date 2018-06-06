<?php

namespace SeTaco\Decorators;


use Facebook\WebDriver\Remote\RemoteWebDriver;

use SeTaco\IBrowser;
use SeTaco\IDomElement;


class BrowserDecorator implements IBrowser
{
	/** @var callable */
	private $onUse;
	
	/** @var IBrowser */
	private $child;
	
	
	private function use(): IBrowser
	{
		$onUse = $this->onUse;
		$onUse();
		
		return $this->child;
	}
	
	
	public function __construct(IBrowser $child)
	{
		$this->child = $child;
	}
	
	public function getRemoteWebDriver(): RemoteWebDriver
	{
		return $this->child->getRemoteWebDriver();
	}
	
	public function goto(string $url): IBrowser
	{
		return $this->use()->goto($url);
	}
	
	public function click(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		return $this->use()->click($cssSelector, $timeout);
	}
	
	public function input(string $cssSelector, string $value, float $timeout = 2.5): IBrowser
	{
		return $this->use()->input($cssSelector, $timeout);
	}
	
	public function getElement(string $cssSelector, float $timeout = 2.5): IDomElement
	{
		$element = $this->use()->getElement($cssSelector, $timeout);
		return new DomElementDecorator($element, $this->onUse);
	}
	
	public function tryGetElement(string $selector, float $secToWait = 2.5): ?IDomElement
	{
		$element = $this->use()->tryGetElement($selector, $secToWait);
		return ($element ? new DomElementDecorator($element, $this->onUse) : null);
	}
	
	public function getTitle(): string
	{
		return $this->use()->getTitle();
	}
	
	public function getURL(): string
	{
		return $this->use()->getURL();
	}
	
	public function isDestroyed(): bool
	{
		return $this->child->isDestroyed();
	}
	
	public function destroy(): void
	{
		$this->child->destroy();
	}
	
	
	public function setCallback($getSetCurrentCallback)
	{
		$this->onUse = $getSetCurrentCallback;
	}
}