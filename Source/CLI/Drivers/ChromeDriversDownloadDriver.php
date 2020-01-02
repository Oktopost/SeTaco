<?php
namespace SeTaco\CLI\Drivers;


use FileSystem\Path;
use FileSystem\TempFile;
use SeTaco\CLI\PHPOS;
use SeTaco\Exceptions\CLIException;
use Structura\Random;
use Structura\Version;
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
	
	private static function unzip(TempFile $zipFile, Path $toFile): void
	{
		$extractFolder = new Path($zipFile->path()->get() . '.extracted');
		
		$archive = new \ZipArchive();
		$result = $archive->open($zipFile);
		
		if ($result !== true)
		{
			throw new CLIException("Failed to unzip file '$zipFile'. Code $result");
		}
		
		$driverName = $archive->getNameIndex(0);
		
		if (!$archive->extractTo($extractFolder->get(), $driverName))
		{
			throw new CLIException("Failed to extract from archive '$zipFile' to '$extractFolder'");
		}
		
		$archive->close();
		$extractedFile = $extractFolder->append($driverName);
		$extractedFile->moveFile($toFile);
		
		$extractFolder->delete();
	}
	
	
	public static function clearCache(): void
	{
		self::$dataCache = null;
	}
	
	public static function getLatestForVersion(Version $v): Version
	{
		$path = self::URL . self::LATEST_RELEASE_FILE_PREFIX . $v->format('M.m.b');
		
		error_clear_last();
		$result = @file_get_contents($path);
		CLIException::throwIfLastErrorNotEmpty("Failed to fetch latest version. URL '$path' is not accessible");
		
		return new Version($result);
	}
	
	public static function downloadVersion(Version $version, Path $targetFile, ?string $os = null): void
	{
		$zipPath = $targetFile->back()->append(Random::string(32) . '.' . $version->format() . '.zip');
		$tempZipFile = new TempFile($zipPath);
		
		$fileName = self::getFileNameForOS($os);
		$path = self::URL . "{$version->format()}/$fileName";
		
		error_clear_last();
		$driver = @file_get_contents($path);
		CLIException::throwIfLastErrorNotEmpty("Failed to download driver '$path'");
		
		// Store zip
		@file_put_contents($tempZipFile->path()->get(), $driver);
		CLIException::throwIfLastErrorNotEmpty(
			"Failed to store zipped driver to temporary file '{$tempZipFile->path()}'");
		
		self::unzip($tempZipFile, $targetFile);
	}
}