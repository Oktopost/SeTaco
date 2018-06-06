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
		new HomepageConfig();
		new ServerSetup();
		new DriverConfig();
	}
}