<?php
namespace SeTaco\CLI\Drivers;


use FileSystem\FS;
use FileSystem\Path;

use SeTaco\BrowserType;


class HomeDirectoryDriver
{
	public const DEFAULT_HOME_DIR	= '.ok-taco';
	public const TEMP_DIRECTORY		= 'tmp';
	public const SELENIUM_DIRECTORY	= 'selenium';
	public const DRIVERS_DIR		= 'drivers';
	
	
	/** @var Path */
	private $path;
	
	
	private function getDriverDirNameByBrowserType(string $type): string
	{
		return $type;
	}
	
	
	public function __construct(string $root = '~/' . self::DEFAULT_HOME_DIR)
	{
		$this->path = Path::getPathObject($root)->resolve();
	}
	
	
	public function getPath(): Path
	{
		return new Path($this->path);
	}
	
	public function getTempDirectory(): Path
	{
		return $this->path->append(self::TEMP_DIRECTORY);
	}
	
	public function getSeleniumDirectory(): Path
	{
		return $this->path->append(self::SELENIUM_DIRECTORY);
	}
	
	public function getDriversDirectory($browserType = BrowserType::CHROME): Path
	{
		$name = $this->getDriverDirNameByBrowserType($browserType);
		return $this->path->append(self::DRIVERS_DIR, $name);
	}
	
	public function getDriversDirectoryDriver($browserType = BrowserType::CHROME): DriversFolderDriver
	{
		$path = $this->getDriversDirectory($browserType);
		return new DriversFolderDriver($path, $browserType);
	}
	
	
	public function initialize(): void
	{
		FS::create(
			$this->path,
			[
				self::TEMP_DIRECTORY,
				self::DRIVERS_DIR,
				self::SELENIUM_DIRECTORY
			]
		);
		
		$this->getTempDirectory()->cleanDirectory();
	}
}