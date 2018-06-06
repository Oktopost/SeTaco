<?php
namespace SeTaco\Unit;

	
use PHPUnit\Framework\TestCase;
use SeTaco\BrowserType;
use SeTaco\DriverConfig;
use SeTaco\OSType;


class DriverConfigTest extends TestCase
{
	public function test_PassEmptyArray_DefaultValuesReturned()
	{
		$object = DriverConfig::parse([]);
		
		self::assertEquals(BrowserType::CHROME,				$object->Server->Browser);
		self::assertEquals(OSType::ANY,						$object->Server->OS);
		self::assertEquals('http://127.0.0.1:4444/wd/hub',	$object->Server->ServerURL);
		
		self::assertEquals(80,					$object->Homepage->Port);
		self::assertEquals('http://localhost',	$object->Homepage->URL);
	}
	
	public function test_PassData_DataUsed()
	{
		$object = DriverConfig::parse([
			'server' => 
			[
				'browser'	=> BrowserType::ANDROID,
				'os'		=> OSType::LINUX,
				'url'		=> 'http://example.com:434/wa/wa'
			],
			'Homepage' => 
			[
				'port'	=> 192,
				'url'	=> 'http://oktopost.com'
			]
		]);
		
		self::assertEquals(BrowserType::ANDROID,			$object->Server->Browser);
		self::assertEquals(OSType::LINUX,					$object->Server->OS);
		self::assertEquals('http://example.com:434/wa/wa',	$object->Server->ServerURL);
		
		self::assertEquals(192,						$object->Homepage->Port);
		self::assertEquals('http://oktopost.com',	$object->Homepage->URL);
	}
}