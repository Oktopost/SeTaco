<?php
namespace SeTaco\CLI\Operations;


use SeTaco\CLI\Dialog;
use SeTaco\CLI\Drivers\ChromeDriversDownloadDriver;
use SeTaco\CLI\Drivers\ChromeVersionDriver;
use SeTaco\CLI\Drivers\TempFolder;
use SeTaco\CLI\Drivers\DriversFolderDriver;

use Traitor\TStaticClass;


class ChromeDriverDownloadOperation
{
	use TStaticClass;
	
	
	public static function checkAndDownload(TempFolder $tempFolder, DriversFolderDriver $driverFolder): ?string
	{
		$version = ChromeVersionDriver::current();
		
		$driverFile = $driverFolder->getForMajorVersion($version->Major);
		
		if ($driverFile)
			return $driverFile;
		
		Dialog::printLn("Currently installed chrome version is " . $version);
		Dialog::printLn("You don't have driver installed for this version.");
		
		if (!Dialog::askYesNo("Download drivers?"))
		{
			return null;
		}
		
		$tempFile = $tempFolder->getTempFile();
		
		$latestVersion = ChromeDriversDownloadDriver::getLatestForVersion($version->Major);
		ChromeDriversDownloadDriver::downloadVersion($latestVersion, $tempFile);
		
		return $driverFolder->store($tempFile, (string)$version, true);
	}
}