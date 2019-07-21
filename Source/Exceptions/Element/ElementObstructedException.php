<?php
namespace SeTaco\Exceptions\Element;


class ElementObstructedException extends ElementException
{
	public function __construct(?string $by = null)
	{
		parent::__construct('Element is not clickable. Other element would receive the click. ' . ($by ?? ''));
	}
}