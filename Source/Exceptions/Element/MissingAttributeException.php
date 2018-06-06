<?php
namespace SeTaco\Exceptions\Element;


use SeTaco\Exceptions\SeTacoException;


class MissingAttributeException extends SeTacoException
{
	public function __construct($attrName)
	{
		parent::__construct("Element is missing the requested attribute '$attrName'");
	}
}