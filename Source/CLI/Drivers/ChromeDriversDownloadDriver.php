<?php
namespace SeTaco\CLI\Drivers;


use SeTaco\CLI\PHPOS;
use SeTaco\Exceptions\CLIException;
use Traitor\TStaticClass;


class ChromeDriversDownloadDriver
{
	use TStaticClass;
	
	
	private const URL = "https://chromedriver.storage.googleapis.com/";
	private const LATEST_RELEASE_FILE_PREFIX = "LATEST_RELEASE_";
	
	
	private const FILE_NAME_BY_OS = 
	[
		PHPOS::LINUX_OS	=> 'linux64',
		PHPOS::MAC_OS	=> 'mac64'
	];
	
	private static $dataCache = null;
	
	
	private static function getFileNameForOS(?string $os = null): string
	{
		if ($os == null)
		{
			$os = PHPOS::get();
		}
		
		if (!isset(self::FILE_NAME_BY_OS[$os]))
		{
			throw new CLIException("Unknown or unsupported os $os");
		}
		
		return 'chromedriver_' . self::FILE_NAME_BY_OS[$os] . '.zip';
	}
	
	private static function unzip(string $zipFile, string $toFile): void
	{
		$extractFolder = $toFile . '.extracted';
		
		$archive = new \ZipArchive();
		$result = $archive->open($zipFile);
		
		if ($result !== true)
		{
			throw new CLIException("Failed to unzip file '$zipFile'. Code $result");
		}
		
		$driverName = $archive->getNameIndex(0);
		
		if (!$archive->extractTo($extractFolder, $driverName))
		{
			throw new CLIException("Failed to extract from archive '$zipFile' to '$extractFolder'");
		}
		
		$archive->close();
		$extractedFile = "$extractFolder/$driverName";
		@rename($extractedFile, $toFile);
		
		CLIException::throwIfLastErrorNotEmpty("Failed to move driver after unzip. From '$extractedFile' to '$toFile'");
		
		@rmdir($extractFolder);
		CLIException::throwIfLastErrorNotEmpty("Failed to delete temporary directory '$extractFolder'");
	}
	
	
	public static function clearCache(): void
	{
		self::$dataCache = null;
	}
	
	public static function getLatestForVersion(string $major): string
	{
		$path = self::URL . self::LATEST_RELEASE_FILE_PREFIX . $major;
		
		error_clear_last();
		$result = @file_get_contents(self::URL . self::LATEST_RELEASE_FILE_PREFIX . $major);
		CLIException::throwIfLastErrorNotEmpty("Failed to fetch latest version. URL '$path' is not accessible");
		
		return $result;
	}
	
	public static function downloadVersion(string $version, string $targetFile, ?string $os = null): void
	{
		$tempZipFile = $targetFile . '.' . mt_rand() . '.zip';
		
		$fileName = self::getFileNameForOS($os);
		$path = self::URL . "$version/$fileName";
		
		error_clear_last();
		$driver = @file_get_contents($path);
		CLIException::throwIfLastErrorNotEmpty("Failed to download driver '$path'");
		
		// Store zip
		@file_put_contents($tempZipFile, $driver);
		CLIException::throwIfLastErrorNotEmpty("Failed to store zipped driver to temporary file '$tempZipFile'");
		
		// Unzip
		self::unzip($tempZipFile, $targetFile);
		
		// Unlink temp zip file
		@unlink($tempZipFile);
		CLIException::throwIfLastErrorNotEmpty("Failed to delete temporary zipped driver at '$tempZipFile'");
	}
}