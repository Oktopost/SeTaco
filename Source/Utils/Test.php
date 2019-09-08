<?php
namespace SeTaco\Utils;


use Composer\Script\Event;


class Test
{
	private static function checkSelenium(): bool
	{
		echo "Checking selenium instance is running... ";
		
		$result = shell_exec('./vendor/bin/selenium.sh status');
		
		if ($result)
		{
			echo "found one\n";
			return true;
		}
		else
		{
			echo "not found\n";
			return false;
		}
	}
	
	private static  function startSelenium(): void
	{
		echo "\nStarting new selenium instance\n";
		shell_exec('./vendor/bin/selenium.sh start');
	}
	
	private static  function stopSelenium(): void
	{
		echo "\nStopping selenium instance\n";
		shell_exec('./vendor/bin/selenium.sh stop');
	}
	
	
	public static function test(Event $event, ?array $arguments = []): void
	{
		$evArgs = [implode(' ', $event->getArguments())];
		
		$arguments = array_filter(array_unique(array_merge(['--dont-report-useless-tests'], $evArgs, $arguments)));
		
		if (!self::checkSelenium())
			self::startSelenium();
		
		$exec = './vendor/phpunit/phpunit/phpunit ' . implode(' ', $arguments);
		
		echo $exec;
		
		shell_exec($exec);
		
		self::stopSelenium();
	}
	
	public static function unitTest(Event $event): void
	{
		self::test($event, ['--group unit']);
	}
	
	public static function integrationTest(Event $event): void
	{
		self::test($event, ['--group integration']);
	}	

	public static function testCover(Event $event): void
	{
		self::test($event, ['--coverage-html ./build/cover']);
	}
	
	public static function unitTestCover(Event $event): void
	{
		self::test($event, ['--coverage-html ./build/cover --group unit']);
	}
	
	public static function integrationTestCover(Event $event): void
	{
		self::test($event, ['--coverage-html ./build/cover --group integration']);
	}
}