<?php
namespace SeTaco\Exceptions\Browser\Element;



class ElementNotFoundException extends ElementException
{
	public function __construct($cssSelector)
	{
		parent::__construct("Could not find an element for the selector '$cssSelector'");
	}
}