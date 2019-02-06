<?php
namespace SeTaco\Exceptions\Browser\Element;


use SeTaco\Exceptions\Element\UnexpectedBrowserStateException;


class ElementException extends UnexpectedBrowserStateException
{
	public function __construct(?string $message = null)
	{
		parent::__construct($message);
	}
}