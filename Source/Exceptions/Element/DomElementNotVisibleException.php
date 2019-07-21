<?php
namespace SeTaco\Exceptions\Element;


use Facebook\WebDriver\Exception\ElementNotVisibleException;


class DomElementNotVisibleException extends ElementException
{
	public function __construct(?ElementNotVisibleException $e = null)
	{
		parent::__construct('Element is not clickable', 0, $e);
	}
}