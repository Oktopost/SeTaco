<?php
namespace SeTaco;


interface ISession
{
	public function openBrowser(): IBrowser;
	public function config(): DriverConfig;
	public function clear(): void;
	public function hasCurrent(): bool;
	public function current(): IBrowser;
	public function assert(): IBrowserAssert;
}