<?php
namespace SeTaco\CLI;


use Traitor\TStaticClass;


class Dialog
{
	use TStaticClass;
	
	
	private static $isSilent = false;
	
	
	public static function silent(): void
	{
		self::$isSilent = true;
	}
	
	public static function ask(string $question): string
	{
		return readline($question);
	}
	
	public static function askYesNo(string $question, bool $defaultYes = true): bool 
	{
		$yes = $defaultYes ? 'Y' : 'y';
		$no = $defaultYes ? 'n' : 'N';
		
		$result = null;
		$question = $question . " [$yes/$no]: ";
		
		while (!$result)
		{
			$result = self::ask($question);
			$result = trim(strtolower($result));
			
			if (!$result)
			{
				$result = $defaultYes ? 'y' : 'n';
			}
			else if ($result != 'y' && $result != 'n')
			{
				$result = null;
			}
		}
		
		return $result == 'y';
	}
	
	public static function printLn(string $text): void
	{
		if (self::$isSilent)
			return;
		
		echo $text . PHP_EOL;
	}
	
	public static function printErrorLn(string $error): void
	{
		if (self::$isSilent)
			return;
		
		echo "\033[91mError: $error\033[0m" . PHP_EOL;
	}
}