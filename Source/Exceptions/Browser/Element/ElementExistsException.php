<?php
namespace SeTaco\Exceptions\Browser\Element;



class ElementExistsException extends ElementException
{
	public function __construct(string $cssSelector, float $timeout)
	{
		parent::__construct("Element $cssSelector still exists after waiting for $timeout seconds");
	}
}