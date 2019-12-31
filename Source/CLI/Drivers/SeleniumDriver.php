<?php
namespace SeTaco\CLI\Drivers;


use SeTaco\CLI\Objects\RunConfig;
use SeTaco\Exceptions\FatalSeTacoException;

use Traitor\TStaticClass;
use FileSystem\Path;


class SeleniumDriver
{
	use TStaticClass;
	
	
	private const PATH_TO_SELENIUM_SH = __DIR__ . '/../../../Scripts/selenium.sh';
	
	
	private static function getSHCommand(string ...$args): string
	{
		$path = new Path(self::PATH_TO_SELENIUM_SH);
		$path = $path->resolve();
		
		if (!$path->exists())
			throw new FatalSeTacoException("$path not found");
		else if (!$path->isExecutable())
			throw new FatalSeTacoException("$path is not executable");
		
		$command = $path->get();
		
		if ($args)
		{
			$command .= ' ' . implode(' ', $args);
		}
		
		return $command;
	}
	
	
	public static function startSelenium(RunConfig $config): void
	{
		shell_exec(self::getSHCommand(
			'start', 
			$config->ChromeDriverPath->get(), 
			$config->SeleniumPath->get()
		));
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