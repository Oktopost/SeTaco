<?php
namespace SeTaco\CLI\Operations;


use SeTaco\CLI\Dialog;
use SeTaco\CLI\Drivers\HomeDirectoryDriver;
use SeTaco\CLI\Drivers\SeleniumDownloadDriver;

use Traitor\TStaticClass;


class SeleniumDownloadOperation
{
	use TStaticClass;
	
	
	public static function downloadSelenium(HomeDirectoryDriver $home): void
	{
		$home->getSeleniumDirectoryDriver()->cleanup(1);
		
		Dialog::printLn("Selenium not found. Downloading latest version...");
		
		$latestVersion = SeleniumDownloadDriver::download($home->getSeleniumDirectory());
		Dialog::printLn("Dowloaded: {$latestVersion->name()}, into {$latestVersion->back()}");
	}
}