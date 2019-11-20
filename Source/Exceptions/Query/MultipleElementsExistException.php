<?php
namespace SeTaco\Exceptions\Query;


use SeTaco\Query\ISelector;
use SeTaco\Exceptions\QueryException;


class MultipleElementsExistException extends QueryException
{
	public function __construct($selector)
	{
		parent::__construct(
			$selector, 
			'Multiple elements were found for the provided query, however only one was expected');
	}
}