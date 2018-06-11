<?php
namespace SeTaco\Unit\Config;


use SeTaco\Config\HomepageConfig;
use CosmicRay\Wrappers\PHPUnit\UnitestCase;


class HomepageConfigTest extends UnitestCase
{
	public function subject(array $data = []): HomepageConfig
	{
		return (new HomepageConfig())->fromArray($data);
	}
	
	
	public function test_getURL_FullURLHttpPassed_SameURLReturned(HomepageConfig $config): void
	{
		self::assertEquals('http://www.oktopost.com', $config->getURL('http://www.oktopost.com'));
	}
	
	public function test_getURL_FullURLHttpsPassed_SameURLReturned(HomepageConfig $config): void
	{
		self::assertEquals('https://www.oktopost.com', $config->getURL('https://www.oktopost.com'));
	}
	
	public function test_getURL_PassEmptyString_ReturnConfigURL()
	{
		self::assertEquals(
			'https://www.oktopost.com/main', 
			$this->subject(
				[
					'URL' => 'https://www.oktopost.com/main'
				])
				->getURL('')
		);
	}
	
	public function test_getURL_URIPassed_URIAddedToURL()
	{
		self::assertEquals(
			'https://www.oktopost.com/main/path', 
			$this->subject(
				[
					'URL' => 'https://www.oktopost.com/main'
				])
				->getURL('path')
		);
	}
	
	public function test_getURL_URIWithSlashPassed_URIAddedToURL()
	{
		self::assertEquals(
			'https://www.oktopost.com/main/path', 
			$this->subject(
				[
					'URL' => 'https://www.oktopost.com/main'
				])
				->getURL('/path')
		);
	}
	
	public function test_getURL_URLHaveSlash_URIAddedToURL()
	{
		self::assertEquals(
			'https://www.oktopost.com/main/path', 
			$this->subject(
				[
					'URL' => 'https://www.oktopost.com/main/'
				])
				->getURL('path')
		);
	}
	
	public function test_getURL_BothHAveSlash_OnlyOneSlashUsed()
	{
		self::assertEquals(
			'https://www.oktopost.com/main/path', 
			$this->subject(
				[
					'URL' => 'https://www.oktopost.com/main/'
				])
				->getURL('/path')
		);
	}
	
	public function test_getURL_PortIsNot80_PortAppened()
	{
		self::assertEquals(
			'https://www.oktopost.com:8080/main/path', 
			$this->subject(
				[
					'URL' 	=> 'https://www.oktopost.com:90/main/',
					'Port'	=> 8080
				])
				->getURL('/path')
		);
	}
}