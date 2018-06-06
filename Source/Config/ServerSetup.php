<?php
namespace SeTaco\Config;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

use Objection\LiteSetup;
use Objection\LiteObject;

use SeTaco\OSType;
use SeTaco\BrowserType;


/**
 * @property string $OS
 * @property string $Browser
 * @property string $ServerURL
 */
class ServerSetup extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'OS'		=> LiteSetup::createEnum(OSType::class, OSType::ANY),
			'Browser'	=> LiteSetup::createEnum(BrowserType::class, BrowserType::CHROME),
			'ServerURL'	=> LiteSetup::createString('http://127.0.0.1:4444/wd/hub')
		];
	}
	
	
	public function getDesiredCapabilities(): DesiredCapabilities
	{
		return new DesiredCapabilities([
			WebDriverCapabilityType::BROWSER_NAME	=> $this->Browser,
			WebDriverCapabilityType::PLATFORM		=> $this->OS,
		]);
	}
	
	public function createDriver(): RemoteWebDriver
	{
		return RemoteWebDriver::create($this->ServerURL, $this->getDesiredCapabilities());
	}
}