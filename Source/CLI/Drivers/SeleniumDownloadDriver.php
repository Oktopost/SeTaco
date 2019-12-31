<?php
namespace SeTaco\CLI\Drivers;


use FileSystem\Path;
use SeTaco\Exceptions\CLIException;
use Traitor\TStaticClass;


class SeleniumDownloadDriver
{
	use TStaticClass;
	
	
	private const PATH_TO_SELENIUM = 'https://selenium-release.storage.googleapis.com/3.9/selenium-server-standalone-3.9.1.jar';
	
	
	public static function download(Path $into): Path
	{
		error_clear_last();
		$seleniumJar = @file_get_contents(self::PATH_TO_SELENIUM);
		CLIException::throwIfLastErrorNotEmpty("Failed to download selenium from '" . self::PATH_TO_SELENIUM . "'");
		
		$to = $into->append('selenium.3.9.1.0.jar');
		
		if ($to->isFile())
			$to->unlink();
		
		$to->touch();
		
		// Store zip
		@file_put_contents($to->get(), $seleniumJar);
		CLIException::throwIfLastErrorNotEmpty(
			"Failed to save selenium standalone into '$to'");
		
		return $to;
	}
}