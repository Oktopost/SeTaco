<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebDriver;


interface IBrowser
{
	public function getRemoteWebDriver(): RemoteWebDriver;
	public function goto(string $url): IBrowser;
	public function click(string $cssSelector, float $timeout = 2.5): IBrowser;
	public function formInput(array $elements, ?string $submit = null, float $timeout = 2.5): IBrowser;
	public function input(string $cssSelector, string $value, float $timeout = 2.5): IBrowser;
	
	public function waitForElementToDisappear(string $cssSelector, float $timeout = 2.5): void;
	public function waitForElement(string $cssSelector, float $timeout = 2.5): void;
	public function hasElement(string $cssSelector, float $timeout = 2.5): bool;
	public function getElement(string $cssSelector, float $timeout = 2.5): IDomElement;
	public function tryGetElement(string $selector, float $secToWait = 2.5): ?IDomElement;
	
	public function getTitle(): string;
	public function getURL(): string;
	public function isDestroyed(): bool;
	public function destroy(): void;
}