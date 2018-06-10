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
	 * @return ServerSetup
	 */
	public static function mapServerSetup(array $data): ServerSetup 
	{
		$obj = new ServerSetup();
		
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
	 * @return HomepageConfig
	 */
	public static function mapHomepageConfig(array $data): HomepageConfig 
	{
		$obj = new HomepageConfig();
		
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
			if (strtolower($key) == 'homepage')
			{
				$object->Homepage = $c->map()->from($value)->into(HomepageConfig::class);
			}
			else if (strtolower($key) == 'server')
			{
				$object->Server = $c->map()->from($value)->into(ServerSetup::class);
			}
		}
		
		if ($object->Homepage == null) $object->Homepage = new HomepageConfig();
		if ($object->Server == null) $object->Server = new ServerSetup();
		
		return $object;
	}
	
	
	public static function map(array $data): DriverConfig
	{
		return self::cartograph()->map()
			->from($data)
			->into(DriverConfig::class);
	}
}