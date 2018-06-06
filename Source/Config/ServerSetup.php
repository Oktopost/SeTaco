<?php
namespace SeTaco\Config;


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
			'OS'			=> LiteSetup::createEnum(OSType::class, OSType::ANY),
			'Browser'		=> LiteSetup::createEnum(BrowserType::class, BrowserType::CHROME),
			'ServerURL'		=> LiteSetup::createString('http://127.0.0.1:4444/wd/hub')
		];
	}
}