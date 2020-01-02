<?php
namespace SeTaco\CLI\Drivers;


use Structura\Arrays;
use Structura\Version;

use Traitor\TStaticClass;


class ChromeVersionDriver
{
	use TStaticClass;
	
	
	public static function getVersion(): string
	{
		switch(PHP_OS)
		{
			case 'Darwin':
				$exec = '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome';
				break;
			default:
				$exec = 'google-chrome';
				break;
		}

		$result = shell_exec($exec . ' --version | grep -iE "[0-9.]{10,20}"');
		$result = explode(' ', trim($result));
		
		return new Version(Arrays::last($result));
	}
}