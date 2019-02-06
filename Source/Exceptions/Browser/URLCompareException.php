<?php
namespace SeTaco\Exceptions\Browser;



use SeTaco\Exceptions\Element\UnexpectedBrowserStateException;


class URLCompareException extends UnexpectedBrowserStateException
{
	public function __construct(string $pattern, string $subject, float $timeout)
	{
		parent::__construct("'$subject' does not match '$pattern' after $timeout seconds");
	}
}