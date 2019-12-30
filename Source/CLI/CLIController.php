<?php
namespace SeTaco\CLI;


use SeTaco\CLI\Drivers\TempFolder;
use SeTaco\CLI\Drivers\DriversFolderDriver;
use SeTaco\CLI\Operations\ChromeDriverDownloadOperation;

use SeTaco\Exceptions\CLIException;


class CLIController
{
	private const TEMP_DIR			= 'tmp';
	private const CHROME_DRIVER_DIR	= 'drivers/chrome';
	private const DEFAULT_HOME		= '.ok-taco';
	
	
	private $homeDir;
	
	/** @var TempFolder */
	private $tempDir;
	
	
	private function createChromeDirectoryDriver(): DriversFolderDriver 
	{
		return new DriversFolderDriver($this->homeDir . DIRECTORY_SEPARATOR . self::CHROME_DRIVER_DIR);	
	}
	
	
	public function __construct(?string $dir = null)
	{
		if (!$dir)
		{
			if (!isset($_SERVER['HOME']))
			{
				throw new CLIException('Global HOME directory is not define. ' . 
					'Please specify path to home directory for SeTaco');
			}
			
			$dir = $_SERVER['HOME'] . '/' . self::DEFAULT_HOME;	
		}
		
		$this->homeDir = $dir;
		$this->tempDir = new TempFolder($dir . '/' . self::TEMP_DIR);
	}
	
	
	public function init(): void
	{
		if (!is_dir($this->homeDir))
		{
			@mkdir($this->homeDir);
			CLIException::throwIfLastErrorNotEmpty("Failed to create home directory '$this->homeDir'");
		}
		
		$this->tempDir->create();
		$this->tempDir->cleanup();
		
		$driverFolder = $this->createChromeDirectoryDriver();
		$driverFolder->init();
	}
	
	
	public function getDriverForChrome(): ?string
	{
		$driverFolder = $this->createChromeDirectoryDriver();
		return ChromeDriverDownloadOperation::checkAndDownload($this->tempDir, $driverFolder);
	}
	
	public function clearDriversFolder(): void
	{
		$this->createChromeDirectoryDriver()->clean();
	}
	
	
	public static function create(?string $dir = null): CLIController
	{
		$controller = new CLIController($dir);
		$controller->init();
		
		return $controller;
	}
}