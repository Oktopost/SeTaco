<?php
namespace SeTaco;


use Objection\LiteSetup;
use Objection\LiteObject;

use SeTaco\Config\ServerSetup;
use SeTaco\Config\HomepageConfig;


/**
 * @property ServerSetup	$Driver 
 * @property HomepageConfig	$Homepage
 */
class DriverConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Server'	=> LiteSetup::createInstanceOf(ServerSetup::class),
			'Homepage'	=> LiteSetup::createInstanceOf(HomepageConfig::class)
		];
	}
}