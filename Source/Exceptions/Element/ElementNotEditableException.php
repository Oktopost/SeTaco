<?php
namespace SeTaco\Exceptions\Element;


class ElementNotEditableException extends ElementException
{
	public function __construct()
	{
		parent::__construct('Element must be user-editable to be cleared');
	}
}