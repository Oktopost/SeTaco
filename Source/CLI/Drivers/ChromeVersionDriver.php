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
		$result = shell_exec('google-chrome --version | grep -iE "[0-9.]{10,20}"');
		$result = explode(' ', trim($result));
		
		return new Version(Arrays::last($result));
	}
}