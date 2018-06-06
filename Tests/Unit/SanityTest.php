<?php
namespace SeTaco\Unit;


use Unitest\Wrappers\PHPUnit\UnitestCase;

use SeTaco\DriverConfig;
use SeTaco\Config\ServerSetup;
use SeTaco\Config\HomepageConfig;


class SanityTest extends UnitestCase
{
	public function test_Objects()
	{
		new HomepageConfig();
		new ServerSetup();
		new DriverConfig();
	}
}