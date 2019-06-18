<?php
namespace SeTaco\Session;


use Facebook\WebDriver\Remote\RemoteWebElement;


interface IDomElement
{
	public function getRemoteWebElement(): RemoteWebElement;
	public function click(bool $hover = false): void;
	public function hover(): void;
	
	public function input(string $input): void;
	public function appendInput(string $input): void;
	public function clear(): void;
	
	public function getName(bool $allowMissing = true): ?string;
	public function getAttribute(string $name, bool $allowMissing = true): ?string;
}