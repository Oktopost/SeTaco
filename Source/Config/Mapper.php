<?php
namespace SeTaco\Config;


use SeTaco\TacoConfig;
use Cartograph\Cartograph;
use Cartograph\Base\IMapper;


class Mapper implements IMapper
{
	private const CONFIG_KEY_SERVER			= 'server';
	private const CONFIG_KEY_TARGETS		= 'targets';
	private const CONFIG_DEFAULT_TIMEOUT	= 'timeout';
	
	
	/** @var Cartograph */
	private static $cartograph = null;
	
	
	private static function cartograph(): Cartograph
	{
		if (!self::$cartograph)
		{
			self::$cartograph = new Cartograph();
			self::$cartograph->addClass(self::class);
		}
		
		return self::$cartograph;
	}
	
	
	/**
	 * @map
	 * @param array $data
	 * @return ServerConfig
	 */
	public static function mapServerConfig(array $data): ServerConfig 
	{
		$obj = new ServerConfig();
		
		foreach ($data as $key => $value)
		{
			switch (strtolower($key))
			{
				case 'os': 
					$obj->OS = strtoupper($value);
					break;
					
				case 'browser':
					$obj->Browser = strtolower($value);
					break;
				
				case 'url':
					$obj->ServerURL = $value;
					break;
			}
		}
		
		return $obj;
	}
	
	/**
	 * @map
	 * @param array $data
	 * @return TargetConfig
	 */
	public static function mapTargetConfig(array $data): TargetConfig 
	{
		$obj = new TargetConfig();
		
		foreach ($data as $key => $value)
		{
			switch (strtolower($key))
			{
				case 'port':
					$obj->Port = (int)$value;
					break;
				
				case 'url':
					$obj->URL = $value;
					break;
			}
		}
		
		return $obj;
	}
	
	/**
	 * @map
	 * @param array $data
	 * @param Cartograph $c
	 * @return TacoConfig
	 */
	public static function mapTacoConfig(array $data, Cartograph $c): TacoConfig 
	{
		$object = new TacoConfig();
		$object->Query = new QueryConfig();
		
		foreach ($data as $key => $value)
		{
			switch (strtolower($key))
			{
				case self::CONFIG_KEY_TARGETS:
					$object->Targets = $c->map()
						->fromArray($value)
						->keepIndexes()
						->into(TargetConfig::class);
					break;
					
				case self::CONFIG_DEFAULT_TIMEOUT:
					$object->Query->setDefaultTimeout((float)$value);
					break;
					
				case self::CONFIG_KEY_SERVER:
					$object->Server = $c->map()->from($value)->into(ServerConfig::class);
					break;
			}
		}
		
		if (!$object->Targets) $object->Targets = ['default' => new TargetConfig()];
		if (!$object->Server) $object->Server = new ServerConfig();
		
		return $object;
	}
	
	
	public static function map(array $data): TacoConfig
	{
		return self::cartograph()->map()
			->from($data)
			->into(TacoConfig::class);
	}
}