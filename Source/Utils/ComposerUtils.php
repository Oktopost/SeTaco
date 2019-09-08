<?php
namespace SeTaco\Utils;


use Composer\Script\Event;


class ComposerUtils
{
	private const SELENIUM_URL = 'https://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.59.jar';
	private const CHROME_LINUX = 'https://chromedriver.storage.googleapis.com/76.0.3809.68/chromedriver_linux64.zip';
	private const CHROME_DARWIN = 'https://chromedriver.storage.googleapis.com/76.0.3809.68/chromedriver_mac64.zip';
	
	
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
		echo "\n\nDownloading Selenium\n";
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
		
		echo "\n\nDownloading ChromeDriver\n";
		
		$filename = $binPath . '/chrome.zip';
		shell_exec("curl " . $url . " -o " . $filename);
		shell_exec("unzip -u " . $filename . ' -d ' . $binPath);
		shell_exec("rm " . $filename);
	}
	
	private static function copySeleniumSH(string $binPath): void
	{
		shell_exec('cp Source/Utils/selenium.sh ' . $binPath);
		shell_exec('chmod +x ' . $binPath . '/selenium.sh');
	}
	
	
	public static function composerPostInstallHook(Event $event): void
	{
		$path = self::getBinDirPath($event);
		self::downloadSelenium($path);
		self::downloadChromeDriver($path);
		self::copySeleniumSH($path);
	}
}