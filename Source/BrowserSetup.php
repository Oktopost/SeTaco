<?php
namespace SeTaco;


use Objection\LiteSetup;
use Objection\LiteObject;
use SeTaco\Config\KeywordsConfig;
use SeTaco\Config\TargetConfig;
use Facebook\WebDriver\Remote\RemoteWebDriver;


/**
 * @property RemoteWebDriver $RemoteWebDriver
 * @property KeywordsConfig $KeywordsConfig
 * @property TargetConfig $TargetConfig
 * @property string|null $TargetName
 * @property string $BrowserName
 */
class BrowserSetup extends LiteObject
{
	protected function _setup()
	{
		return [
			'RemoteWebDriver' 	=> LiteSetup::createInstanceOf(RemoteWebDriver::class),
			'KeywordsConfig'	=> LiteSetup::createInstanceOf(KeywordsConfig::class),
			'TargetConfig'    	=> LiteSetup::createInstanceOf(TargetConfig::class),
			'TargetName'      	=> LiteSetup::createString(null),
			'BrowserName'     	=> LiteSetup::createString()
		];
	}
}