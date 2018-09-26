<?php

namespace SeTaco\Decorators;


use Facebook\WebDriver\Cookie;
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
		$this->use()->goto($url);
		return $this;
	}
	
	public function click(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		$this->use()->click($cssSelector, $timeout);
		return $this;
	}
	
	public function hover(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		$this->use()->hover($cssSelector. $timeout);
		return $this;
	}
	
	public function hoverAndClick(string $cssSelector, float $timeout = 2.5): IBrowser
	{
		$this->use()->hoverAndClick($cssSelector, $timeout);
		return $this;
	}
	
	public function input(string $cssSelector, string $value, float $timeout = 2.5): IBrowser
	{
		$this->use()->input($cssSelector, $value, $timeout);
		return $this;
	}
	
	public function formInput(array $elements, ?string $submit = null, float $timeout = 2.5): IBrowser
	{
		$this->use()->formInput($elements, $submit, $timeout);
		return $this;
	}
	
	public function waitForElementToDisappear(string $cssSelector, float $timeout = 2.5): void
	{
		$this->use()->waitForElementToDisappear($cssSelector, $timeout);
	}
	
	public function waitForElement(string $cssSelector, float $timeout = 2.5): void
	{
		$this->use()->waitForElement($cssSelector, $timeout);
	}
	
	public function hasElement(string $cssSelector, float $timeout = 2.5): bool
	{
		return $this->use()->hasElement($cssSelector, $timeout);
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
	
	/**
	 * @return Cookie[]
	 */
	public function cookies(): array
	{
		return $this->use()->cookies();
	}
	
	public function setCookie($data, ?string $value = null): void
	{
		$this->use()->setCookie($data, $value);
	}
	
	
	public function setCallback($getSetCurrentCallback)
	{
		$this->onUse = $getSetCurrentCallback;
	}
}