<?php
namespace SeTaco\Utils;


use SeTaco\Browser;
use Traitor\TStaticClass;


class ShutdownFallback
{
	use TStaticClass;
	
	
	private static $isRegistered = false;
	
	/** @var Browser[] */
	private static $browsers = [];
	
	
	public static function addBrowser(Browser $browser): void
	{
		foreach (self::$browsers as $existingBrowser)
		{
			if ($existingBrowser === $browser)
			{
				return;
			}
		}
		
		self::$browsers[] = $browser;
		
		if (!self::$isRegistered)
		{
			register_shutdown_function(function ()
			{
				foreach (self::$browsers as $browser)
				{
					$browser->close();
				}
				
				self::$browsers = [];
			});
		}
	}
	
	public static function removeBrowser(Browser $browser): void
	{
		$browsers = [];
		
		foreach (self::$browsers as $existingBrowser)
		{
			if ($existingBrowser !== $browser)
			{
				$browsers[] = $existingBrowser;
			}
		}
		
		self::$browsers = $browsers;
	}
}