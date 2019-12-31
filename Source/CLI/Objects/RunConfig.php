<?php
namespace SeTaco\CLI\Objects;


use FileSystem\Path;
use Objection\LiteSetup;
use Structura\Version;
use Objection\LiteObject;


/**
 * @property Version		$ChromeDriverVersion
 * @property Path			$ChromeDriverPath
 * @property Version|null	$SeleniumVersion
 * @property Path|null		$SeleniumPath
 */
class RunConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			 'ChromeDriverVersion' 	=> LiteSetup::createInstanceOf(Version::class),
			 'ChromeDriverPath' 	=> LiteSetup::createInstanceOf(Path::class),
			 'SeleniumVersion' 		=> LiteSetup::createInstanceOf(Version::class),
			 'SeleniumPath' 		=> LiteSetup::createInstanceOf(Path::class)
		];
	}
}