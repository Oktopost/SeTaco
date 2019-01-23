<?php
namespace SeTaco;


use SeTaco\Session\IOpenBrowserHandler;

interface IBrowserSession
{
	public function setOpenBrowserHandler(IOpenBrowserHandler $handler): void;
	
	public function openBrowser(string $name): IBrowser;
	public function getBrowser(string $name): ?IBrowser;
	public function hasBrowser(string $name): bool;
	
	public function hasBrowsers(): bool;
	public function current(): ?IBrowser;
	public function select(string $name): IBrowser;
	
	public function closeUnused(): void;
	public function close(?string $name = null): void;
	
	public function config(): DriverConfig;
}