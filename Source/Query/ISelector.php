<?php
namespace SeTaco\Query;


use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverSearchContext;
use SeTaco\IQueryResolver;
use Facebook\WebDriver\WebDriverBy;


interface ISelector
{
	public function type(): string;
	public function query(): string;
	public function originalQuery(): string;
	public function resolver(): ?IQueryResolver;
	public function setOriginal(string $original): void;
	public function getDriverSelector(): WebDriverBy;
	
	/**
	 * @param WebDriverSearchContext $context
	 * @return WebDriverElement[]
	 */
	public function searchIn(WebDriverSearchContext $context): array;
}