<?php
namespace SeTaco;


use Objection\LiteSetup;
use Objection\LiteObject;

use SeTaco\Config\Mapper;
use SeTaco\Config\ServerSetup;
use SeTaco\Config\HomepageConfig;


/**
 * @property ServerSetup	$Server
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
	
	
	public static function parse(array $data): DriverConfig
	{
		return Mapper::map($data);
	}
}