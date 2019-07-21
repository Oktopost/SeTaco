<?php
namespace SeTaco\Exceptions\Query;


use SeTaco\Query\ISelector;
use SeTaco\Exceptions\QueryException;


class QueriedElementNotEditableException extends QueryException
{
	public function __construct(ISelector $selector, string $message)
	{
		parent::__construct($selector, "Element is not editable because: $message");
	}
}