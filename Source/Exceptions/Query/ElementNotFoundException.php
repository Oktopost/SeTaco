<?php
namespace SeTaco\Exceptions\Query;


use SeTaco\Query\ISelector;
use SeTaco\Exceptions\QueryException;


class ElementNotFoundException extends QueryException
{
	public function __construct(ISelector $selector)
	{
		parent::__construct($selector, 'Element not found');
	}
}