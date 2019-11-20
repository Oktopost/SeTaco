<?php
namespace SeTaco\Exceptions\Query;


use SeTaco\Query\ISelector;
use SeTaco\Exceptions\QueryException;


class QueriedElementNotClickableException extends QueryException
{
	public function __construct($selector, string $message)
	{
		parent::__construct($selector, "Element is not clickable because: $message");
	}
}