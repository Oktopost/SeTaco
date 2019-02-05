<?php
namespace SeTaco\Config;


use Cartograph\Base\IMapper;
use Cartograph\Cartograph;
use SeTaco\DriverConfig;


class Mapper implements IMapper
{
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
	 * @return DriverConfig
	 */
	public static function mapDriverConfig(array $data, Cartograph $c): DriverConfig 
	{
		$object = new DriverConfig();
		
		foreach ($data as $key => $value)
		{
			if (strtolower($key) == 'targets')
			{
				foreach ($value as $targetName => $targetData)
				{
					$target = $c->map()->from($targetData)->into(TargetConfig::class);
					$object->Targets[$targetName] = $target;
				}
			}
			else if (strtolower($key) == 'server')
			{
				$object->Server = $c->map()->from($value)->into(ServerConfig::class);
			}
		}
		
		if (!$object->Targets) $object->Targets = ['default' => new TargetConfig()];
		if (!$object->Server) $object->Server = new ServerConfig();
		
		return $object;
	}
	
	
	public static function map(array $data): DriverConfig
	{
		return self::cartograph()->map()
			->from($data)
			->into(DriverConfig::class);
	}
}