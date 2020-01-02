<?php
namespace SeTaco\CLI\Drivers;


use SeTaco\CLI\PHPOS;
use Structura\Arrays;
use Structura\Version;

use Traitor\TStaticClass;


class ChromeVersionDriver
{
	use TStaticClass;
	
	
	public static function getVersion(): string
	{
		if (PHPOS::isMac())
		{
			$exec = '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome';
		}
		else
		{
			$exec = 'google-chrome';
		}

		$result = shell_exec($exec . ' --version | grep -iE "[0-9.]{10,20}"');
		$result = explode(' ', trim($result));
		
		return new Version(Arrays::last($result));
	}
}