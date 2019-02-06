<?php
namespace SeTaco\Exceptions\Browser\Element;


class MissingAttributeException extends ElementException
{
	public function __construct($attrName)
	{
		parent::__construct("Element is missing the requested attribute '$attrName'");
	}
}