<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebDriver;

use Objection\LiteSetup;
use Objection\LiteObject;

use SeTaco\Config\Mapper;
use SeTaco\Config\ServerConfig;
use SeTaco\Config\TargetConfig;
use SeTaco\Config\QueryConfig;


/**
 * @property ServerConfig	$Server
 * @property QueryConfig $Keywords
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
			'Keywords'	=> LiteSetup::createInstanceOf(QueryConfig::class),
			'Targets'	=> LiteSetup::createInstanceArray(TargetConfig::class),
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
	
	public function hasTarget(string $targetName): bool
	{
		return isset($this->Targets[$targetName]);
	}
	
	
	public static function parse(array $data): TacoConfig
	{
		return Mapper::map($data);
	}
}