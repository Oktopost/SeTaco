<?php
namespace SeTaco\Exceptions\Element;


use SeTaco\Exceptions\SeTacoException;


class ElementNotFoundException extends SeTacoException
{
	public function __construct($cssSelector)
	{
		parent::__construct("Could not find an element for the selector '$cssSelector'");
	}
}