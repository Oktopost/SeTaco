<?php
namespace SeTaco\Exceptions\Query;


use SeTaco\Query\ISelector;
use SeTaco\Exceptions\QueryException;


class ElementStillExistsException extends QueryException
{
	public function __construct($selector, float $timeout)
	{
		parent::__construct($selector, "Element still exists after waiting for $timeout seconds");
	}
}