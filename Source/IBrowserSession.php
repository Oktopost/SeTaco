<?php
namespace SeTaco;


interface IBrowserSession
{
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