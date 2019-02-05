<?php
namespace SeTaco;


use SeTaco\Session\IOpenBrowserHandler;

interface IBrowserSession
{
	public function setOpenBrowserHandler(IOpenBrowserHandler $handler): void;
	
	public function open(string $target, ?string $browserName = null): IBrowser;
	public function getBrowser(string $targetName, ?string $browserName = null): ?IBrowser;
	
	public function hasBrowser(string $targetName, string $browserName = null): bool;
	public function hasBrowsers(?string $targetName = null): bool;
	
	public function current(?string $targetName = null): ?IBrowser;
	public function select(string $targetName, ?string $browserName = null): IBrowser;
	
	public function closeUnused(?string $targetName = null): void;
	public function close(?string $targetName = null, ?string $browserName = null): void;
	
	public function config(): DriverConfig;
}