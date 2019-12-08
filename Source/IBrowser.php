<?php
namespace SeTaco;


use SeTaco\Config\TargetConfig;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteKeyboard;
use Facebook\WebDriver\Remote\RemoteWebDriver;


interface IBrowser extends IQuery
{
	public function getRemoteWebDriver(): RemoteWebDriver;
	public function getTargetName(): ?string;
	public function getTargetConfig(): TargetConfig;
	public function getBrowserName(): string;
	public function goto(string $url): IBrowser;
	public function formInput(array $keywordValuePairs, ?string $submit = null, ?float $timeout = null): IBrowser;
	
	public function compareURL(string $url): bool;
	public function waitForURL(string $url, ?float $timeout = null): void;
	
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
	
	public function deleteCookie(string $named): void;
	public function deleteCookies(): void;
	
	
	// Keyboard
	public function press(string $key): void;
	public function pressEsc(): void;
	public function pressEnter(): void;
	public function keyboard(): RemoteKeyboard;
}