<?php
namespace SeTaco;


interface IBrowserAssert
{
	public function __invoke(IBrowser $browser): BrowserAssert;
	
	public function URL(string $match): void;
	
	public function elementExists(string $selector, float $secToWait = 2.5): void;
	public function elementValue(string $expected, string $selector, float $secToWait = 2.5): void;
	public function elementAttribute(string $expected, string $selector, string $name, float $secToWait = 2.5): void;
}