<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebElement;


interface IDomElement
{
	public function getRemoteWebElement(): RemoteWebElement;
	public function click(): void;
	public function input(string $input): void;
	public function getName(bool $allowMissing = true): ?string;
	public function getAttribute(string $name, bool $allowMissing = true): ?string;
}