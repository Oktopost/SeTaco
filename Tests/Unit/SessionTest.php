<?php
namespace SeTaco\Unit;


use SeTaco\BrowserSession;
use SeTaco\BrowserAssert;
use SeTaco\DriverConfig;

use CosmicRay\Wrappers\PHPUnit\UnitestCase;


class SessionTest extends UnitestCase
{
	public function createConfig(array $data = []): DriverConfig
	{
		return DriverConfig::parse($data);
	}
	
	public function subject(DriverConfig $config): BrowserSession
	{
		return new BrowserSession($config);
	}
	
	
	public function test_config_ConfigReturned(DriverConfig $config): void
	{
		$session = $this->subject($config);
		self::assertSame($config, $session->config());
	}
	
	
	public function test_assert_AssertObjectReturned(BrowserSession $subject): void
	{
		self::assertInstanceOf(BrowserAssert::class, $subject->assert());
	}
	
	public function test_assert_SameObjectReturnedEachTime(BrowserSession $subject): void
	{
		self::assertSame($subject->assert(), $subject->assert());
	}
}