<?php
namespace SeTaco\Downloads;


use Composer\Script\Event;


class ComposerUtils
{
	private const SELENIUM_URL = 'https://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.59.jar';
	private const CHROME_LINUX = 'https://chromedriver.storage.googleapis.com/2.46/chromedriver_linux64.zip';
	private const CHROME_DARWIN = 'https://chromedriver.storage.googleapis.com/2.46/chromedriver_mac64.zip';
	
	
	private static function getVendorDir(Event $event): string
	{
		return $event->getComposer()->getConfig()->get('vendor-dir');
	}
	
	private static function getBinDirPath(Event $event): string
	{
		$result = self::getVendorDir($event) . '/bin';
		
		if (!is_dir($result))
		{
			mkdir($result);
		}
		
		return $result;
	}
	
	private static function downloadSelenium(string $binPath): void
	{
		echo "Downloading Selenium\n";
		$url = self::SELENIUM_URL;
		$filename = $binPath . '/selenium.jar';
		shell_exec("curl " . $url . " -o " . $filename);
	}
	
	private static function downloadChromeDriver(string $binPath): void
	{
		switch (PHP_OS)
		{
			case "Linux":
				$url = self::CHROME_LINUX;
				break;
			case "Darwin":
				$url = self::CHROME_DARWIN;
				break;
			default:
				throw new \Exception('Failed to download chrome driver for current OS');
		}
		
		echo "Downloading ChromeDriver\n";
		
		$filename = $binPath . '/chrome.zip';
		shell_exec("curl " . $url . " -o " . $filename);
		shell_exec("unzip -f " . $filename . ' -d ' . $binPath);
		shell_exec("rm " . $filename);
	}
	
	
	public static function composerPostInstallHook(Event $event): void
	{
		$path = self::getBinDirPath($event);
		self::downloadSelenium($path);
		self::downloadChromeDriver($path);
	}
	
	public static function composerPostPackageInstallHook(Event $event): void
	{
		$path = self::getBinDirPath($event);
		self::downloadSelenium($path);
		self::downloadChromeDriver($path);
	}
}