<?php
namespace SeTaco\Exceptions;


use Facebook\WebDriver\Exception\InvalidElementStateException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Exception\UnknownServerException;
use Facebook\WebDriver\Exception\ElementNotVisibleException;

use SeTaco\Exceptions\Element\DomElementNotVisibleException;

use SeTaco\Exceptions\Element\ElementNotEditableException;
use SeTaco\Exceptions\Element\ElementObstructedException;
use Structura\Strings;
use Traitor\TStaticClass;


class FacebookExceptionParser
{
	use TStaticClass;
	
	
	private static function handleUnknown(UnknownServerException $u)
	{
		if (Strings::contains($u->getMessage(), 'Other element would receive the click'))
		{
			$other = explode('Other element would receive the click: ', $u->getMessage())[1] ?? '';
			$other = trim(explode("\n", $other)[0] ?? '');
			
			throw new ElementObstructedException($other ?: null);
		}
		else
		{
			throw new SeTacoException('Unexpected UnknownServerException exception', 0, $u);
		}
	}
	
	private static function handleInvalidState(InvalidElementStateException $i)
	{
		if (Strings::contains($i->getMessage(), 'Element must be user-editable in order to clear it'))
		{
			throw new ElementNotEditableException();
		}
		else
		{
			throw new SeTacoException('Unexpected InvalidElementStateException exception', 0, $i);
		}
	}
	
	
	public static function parseElementException(WebDriverException $t)
	{
		if ($t instanceof ElementNotVisibleException)
		{
			throw new DomElementNotVisibleException($t);
		}
		else if ($t instanceof UnknownServerException)
		{
			self::handleUnknown($t);
		}
		else if ($t instanceof InvalidElementStateException)
		{
			self::handleInvalidState($t);
		}
		else
		{
			throw new SeTacoException('Unexpected WebDriver exception', 0, $t);
		}
	}
}