<?php
namespace SeTaco\Utils;


use Composer\Script\Event;


class Test
{
	private static function checkSelenium(): bool
	{
		echo "Checking selenium instance is running... ";
		
		$result = SeleniumConnector::isRunning();
		
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
		SeleniumConnector::startSelenium();
	}
	
	private static function stopSelenium(): void
	{
		echo "\nStopping selenium instance\n";
		SeleniumConnector::stopSelenium();
	}
	
	private static function combineArgs(Event $event, ?array $arguments = []): array
	{
		$evArgs = [implode(' ', $event->getArguments())];
		return array_filter(array_unique(array_merge(['--dont-report-useless-tests'], $evArgs, $arguments)));
	}
	
	
	public static function test(Event $event, ?array $arguments = []): void
	{
		$arguments = self::combineArgs($event, $arguments);
		
		if (!self::checkSelenium())
			self::startSelenium();
		
		$exec = './vendor/phpunit/phpunit/phpunit ' . implode(' ', $arguments) . ' 1>&2';
		
		shell_exec($exec);
		
		self::stopSelenium();
	}
	
	public static function testWithoutSelenium(Event $event): void
	{
		$arguments = self::combineArgs($event);
		
		$exec = './vendor/phpunit/phpunit/phpunit ' . implode(' ', $arguments) . ' 1>&2';
		
		shell_exec($exec);
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