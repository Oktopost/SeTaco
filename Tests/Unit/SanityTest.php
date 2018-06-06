<?php
namespace SeTaco\Unit;


use PHPUnit\Framework\TestCase;

use SeTaco\DriverConfig;
use SeTaco\Config\ServerSetup;
use SeTaco\Config\HomepageConfig;


class SanityTest extends TestCase
{
	public function test_Objects()
	{
		$a = new HomepageConfig();
		$b = new ServerSetup();
		$c = new DriverConfig();
	}
}