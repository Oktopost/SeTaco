<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebElement;


interface IDomElement
{
	public function getRemoteWebElement(): RemoteWebElement;
	public function click(bool $hover = false): IDomElement;
	public function hover(): IDomElement;
	public function query(): IQuery;
	
	public function input(string $input, bool $clear = false): IDomElement;
	public function clear(): IDomElement; 
	
	public function getText(): string;
	public function getName(bool $allowMissing = true): ?string;
	public function getAttribute(string $name, bool $allowMissing = true): ?string;
}