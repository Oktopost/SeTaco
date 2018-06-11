<?php
namespace SeTaco\Unit;


use SeTaco\Session;
use SeTaco\BrowserAssert;
use SeTaco\DriverConfig;

use CosmicRay\Wrappers\PHPUnit\UnitestCase;


class SessionTest extends UnitestCase
{
	public function createConfig(array $data = []): DriverConfig
	{
		return DriverConfig::parse($data);
	}
	
	public function subject(DriverConfig $config): Session
	{
		return new Session($config);
	}
	
	
	public function test_config_ConfigReturned(DriverConfig $config): void
	{
		$session = $this->subject($config);
		self::assertSame($config, $session->config());
	}
	
	
	public function test_assert_AssertObjectReturned(Session $subject): void
	{
		self::assertInstanceOf(BrowserAssert::class, $subject->assert());
	}
	
	public function test_assert_SameObjectReturnedEachTime(Session $subject): void
	{
		self::assertSame($subject->assert(), $subject->assert());
	}
}