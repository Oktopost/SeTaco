<?php
namespace SeTaco;


use SeTaco\Session\IOpenBrowserHandler;


interface IBrowserSession
{
	public function setOpenBrowserHandler(IOpenBrowserHandler $handler): void;
	
	public function open(string $target, ?string $browserName = null): IBrowser;
	public function getBrowser(string $browserName): ?IBrowser;
	
	public function hasBrowser(string $browserName): bool;
	public function hasBrowsers(): bool;
	
	public function current(): ?IBrowser;
	
	/**
	 * @param string|IBrowser $browserName
	 * @return IBrowser
	 */
	public function select($browserName): IBrowser;
	
	public function closeUnused(): void;
	public function close(?string $browserName = null): void;
	
	public function config(): DriverConfig;
}