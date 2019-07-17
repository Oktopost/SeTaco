<?php
namespace SeTaco\Query;


use SeTaco\IQueryResolver;
use Facebook\WebDriver\WebDriverBy;


interface ISelector
{
	public function type(): string;
	public function query(): string;
	public function originalQuery(): string;
	public function resolver(): ?IQueryResolver;
	public function getDriverSelector(): WebDriverBy;
}