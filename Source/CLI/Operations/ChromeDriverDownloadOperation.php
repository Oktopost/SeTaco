<?php
namespace SeTaco\CLI\Operations;


use SeTaco\BrowserType;
use SeTaco\CLI\Dialog;
use SeTaco\CLI\Drivers\ChromeVersionDriver;
use SeTaco\CLI\Drivers\HomeDirectoryDriver;
use SeTaco\CLI\Drivers\ChromeDriversDownloadDriver;

use Traitor\TStaticClass;
use Structura\Version;


class ChromeDriverDownloadOperation
{
	use TStaticClass;
	
	
	public static function checkAndDownload(HomeDirectoryDriver $home): ?Version
	{
		$version = new Version(ChromeVersionDriver::getVersion());
		$tempFile = $home->getTempFile();
		$driverFolder = $home->getDriversDirectoryDriver(BrowserType::CHROME);
		
		Dialog::printLn("Currently installed chrome version is " . $version->format());
		Dialog::printLn("You don't have driver installed for this version. New driver will be downloaded...");
		
		$latestVersion = ChromeDriversDownloadDriver::getLatestForVersion($version);
		
		Dialog::printLn("Latest driver available: {$latestVersion->format()}. Downloading...");
		
		ChromeDriversDownloadDriver::downloadVersion($latestVersion, $tempFile->path());
		$driverFolder->store($tempFile->path(), $latestVersion);
		
		Dialog::printLn("Download complete...");
		$driverFolder->cleanup($latestVersion);
		
		return new Version($latestVersion);
	}
}