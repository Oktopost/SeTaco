<?php
namespace SeTaco\Exceptions\Browser\Element;



class MultipleElementExistsException extends ElementException
{
	public function __construct(string $cssSelector, float $timeout)
	{
		parent::__construct("Found multiple elements for selector '$cssSelector' after waiting for $timeout seconds");
	}
}