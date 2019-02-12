<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebDriver;

use Objection\LiteSetup;
use Objection\LiteObject;

use SeTaco\Config\Mapper;
use SeTaco\Config\ServerConfig;
use SeTaco\Config\TargetConfig;


/**
 * @property ServerConfig	$Server
 * @property TargetConfig[]	$Targets
 */
class TacoConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Server'	=> LiteSetup::createInstanceOf(ServerConfig::class),
			'Targets'	=> LiteSetup::createInstanceArray(TargetConfig::class)
		];
	}
	
	
	public function createDriver(): RemoteWebDriver
	{
		if (!$this->Server)
		{
			$this->Server = new ServerConfig();
		}
		
		return $this->Server->createDriver();
	}
	
	
	public static function parse(array $data): TacoConfig
	{
		return Mapper::map($data);
	}
}