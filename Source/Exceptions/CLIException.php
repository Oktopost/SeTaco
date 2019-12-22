<?php
namespace SeTaco\Exceptions;


class CLIException extends SeTacoException
{
	public static function throwIfLastErrorNotEmpty(?string $message = null): void
	{
		if (!error_get_last())
			return;
		
		$message = $message ? "$message: " : '';
		$message .= "`" . error_get_last()['message'] . "`";
		
		throw new CLIException($message);
	}
}