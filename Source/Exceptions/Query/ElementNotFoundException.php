<?php
namespace SeTaco\Exceptions\Query;


use SeTaco\Exceptions\QueryException;


class ElementNotFoundException extends QueryException
{
	public function __construct($selector)
	{
		parent::__construct($selector, 'Element not found');
	}
}