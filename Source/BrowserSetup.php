<?php
namespace SeTaco;


use Objection\LiteSetup;
use Objection\LiteObject;
use SeTaco\Config\TargetConfig;
use Facebook\WebDriver\Remote\RemoteWebDriver;


/**
 * @property RemoteWebDriver $RemoteWebDriver
 * @property TargetConfig $TargetConfig
 * @property string|null $TargetName
 * @property string $BrowserName
 */
class BrowserSetup extends LiteObject
{
	protected function _setup()
	{
		return [
			'RemoteWebDriver' => LiteSetup::createInstanceOf(RemoteWebDriver::class),
			'TargetConfig'    => LiteSetup::createInstanceOf(TargetConfig::class),
			'TargetName'      => LiteSetup::createString(null),
			'BrowserName'     => LiteSetup::createString()
		];
	}
}