<?php
namespace SeTaco\CLI;


use SeTaco\BrowserType;
use SeTaco\CLI\Objects\RunConfig;
use SeTaco\CLI\Objects\SeleniumInstance;
use SeTaco\CLI\Drivers\SeleniumDriver;
use SeTaco\CLI\Drivers\ChromeVersionDriver;
use SeTaco\CLI\Drivers\HomeDirectoryDriver;
use SeTaco\CLI\Operations\ChromeDriverDownloadOperation;
use SeTaco\CLI\Operations\SeleniumDownloadOperation;
use SeTaco\Exceptions\CLIException;
use SeTaco\Exceptions\SeTacoException;

use Structura\Version;


class CLIController
{
	/** @var HomeDirectoryDriver */
	private $home;
	
	
	public function __construct(?string $dir = null)
	{
		if (!$dir)
		{
			if (!isset($_SERVER['HOME']))
			{
				throw new CLIException('Global HOME directory is not define. ' . 
					'Please specify path to home directory for SeTaco');
			}
			
			$dir = '~/' . HomeDirectoryDriver::DEFAULT_HOME_DIR;	
		}
		
		$this->home = new HomeDirectoryDriver($dir);
		$this->home->initialize();
	}
	
	
	public function validateChromeDriverVersion(): ?Version
	{
		$drivers = $this->home->getDriversDirectoryDriver(BrowserType::CHROME);
		$version = ChromeVersionDriver::getVersion();
		
		$bestMatch = $drivers->getBestMatch($version);
		
		if ($bestMatch)
			return $bestMatch;
		
		return ChromeDriverDownloadOperation::checkAndDownload($this->home);
	}
	
	public function validateSeleniumVersion(): void
	{
		if (SeleniumDriver::isRunning())
		{
			return;
		}
		
		$seleniumDirDriver = $this->home->getSeleniumDirectoryDriver();
		
		if ($seleniumDirDriver->getLatestVersion())
		{
			return;
		}
		
		SeleniumDownloadOperation::downloadSelenium($this->home);
	}
	
	public function initializeRunConfig(): ?RunConfig
	{
		$config = new RunConfig();
		
		$chromeVersion = self::validateChromeDriverVersion();
		self::validateSeleniumVersion();
		
		if (!$chromeVersion)
		{
			throw new SeTacoException('Could not load appropriate chrome driver');
		}
		
		$config->ChromeDriverVersion = $chromeVersion;
		$config->ChromeDriverPath = $this->home->getDriversDirectoryDriver()->get($chromeVersion);
		
		if (!SeleniumDriver::isRunning())
		{
			$seleniumDirDriver = $this->home->getSeleniumDirectoryDriver();
			
			$config->SeleniumVersion = $seleniumDirDriver->getLatestVersion();
			$config->SeleniumPath = $seleniumDirDriver->getLatest(); 
		}
		
		return $config;
	}
	
	public function runSelenium(): void
	{
		if (SeleniumDriver::isRunning())
		{
			SeleniumInstance::instance()->doNothingOnShutdown();
			Dialog::printLn("Selenium already running...");
			return;
		}
		
		Dialog::printLn("Starting selenium...");
		
		$config = $this->initializeRunConfig();
		
		Dialog::printLn("");
		Dialog::printLn("With Config:");
		Dialog::printLn("------------");
		Dialog::printLn("\tChrome:   " . $config->ChromeDriverVersion . " at " . $config->ChromeDriverPath);
		Dialog::printLn("\tSelenium: " . $config->SeleniumVersion. " at " . $config->SeleniumPath);
		Dialog::printLn("");
		
		SeleniumDriver::startSelenium($config);
		
		// Wait for selenium to start (0.5 sec)
		usleep(500 * 1000);
		
		Dialog::printLn("Selenium running...");
		
		SeleniumInstance::instance()->stopOnShutdown();
	}
	
	public function homeDriver(): HomeDirectoryDriver
	{
		return $this->home;
	}
}