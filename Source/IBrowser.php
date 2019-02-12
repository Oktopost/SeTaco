<?php
namespace SeTaco;


use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use SeTaco\Config\TargetConfig;
use SeTaco\Session\IDomElement;
use SeTaco\Session\IDomElementsCollection;


interface IBrowser
{
	public function getRemoteWebDriver(): RemoteWebDriver;
	public function getTargetName(): ?string;
	public function getTargetConfig(): TargetConfig;
	public function getBrowserName(): string;
	public function goto(string $url): IBrowser;
	public function click(string $keyword, float $timeout = 2.5): IBrowser;
	public function hover(string $keyword, float $timeout = 2.5): IBrowser;
	public function hoverAndClick(string $keyword, float $timeout = 2.5): IBrowser;
	public function formInput(array $keywordValuePairs, ?string $submit = null, float $timeout = 2.5): IBrowser;
	public function input(string $keyword, string $value, float $timeout = 2.5): IBrowser;
	
	public function waitForElementToDisappear(string $keyword, float $timeout = 2.5): void;
	public function waitForElement(string $keyword, float $timeout = 2.5): void;
	public function hasElement(string $keyword, float $timeout = 2.5): bool;
	public function getElement(string $keyword, float $timeout = 2.5): IDomElement;
	public function getElements(string $keyword, float $timeout = 2.5): IDomElementsCollection;
	public function tryGetElement(string $keyword, float $timeout = 2.5): ?IDomElement;
	public function compareURL(string $url): bool;
	public function waitForURL(string $url, float $timeout = 2.5): void;
	
	public function getTitle(): string;
	public function getURL(): string;
	public function refresh(): void;
	public function isClosed(): bool;
	public function close(): void;
	
	/**
	 * @param array|string $data If string, used as cookie name. 
	 * @param null|string $value If $data is string and $value is null, delete the cookie.
	 */
	public function setCookie($data, ?string $value = null): void;
	
	/**
	 * @return Cookie[]
	 */
	public function cookies(): array;
}