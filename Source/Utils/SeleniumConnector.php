<?php
namespace SeTaco\Utils;


use SeTaco\Exceptions\FatalSeTacoException;
use Traitor\TStaticClass;


class SeleniumConnector
{
	use TStaticClass;
	
	
	private const PATH_TO_SELENIUM_SH = __DIR__ . '/selenium.sh';
	
	
	private static function getSHCommand(string ...$args): string
	{
		$path = realpath(self::PATH_TO_SELENIUM_SH);
		
		if (!$path || !file_exists($path) || !is_executable($path))
			throw new FatalSeTacoException('Missing selenium.sh in ./vendor/bin/ or selenium.sh not executable');
		
		$command = $path . ($args ? (' ' . implode(' ', $args)) : '');
		
		return $command;
	}
	
	
	public static function startSelenium(): void
	{
		shell_exec(self::getSHCommand('start'));
	}
	
	public static function stopSelenium(): void
	{
		shell_exec(self::getSHCommand('stop'));
	}
	
	public static function isRunning(): bool 
	{
		return (bool)(shell_exec(self::getSHCommand('status')));
	}
	
	public static function listen(): void 
	{
		system(self::getSHCommand('listen', '2>&1'));
	}
}