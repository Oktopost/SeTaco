<?php

namespace SeTaco\Config;


use Objection\LiteObject;
use Objection\LiteSetup;


/**
 * @property string $OS
 * @property string $Browser
 * @property string $ServerURL
 */
class ServerSetup extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'OS'			=> LiteSetup::createString(),
			'Browser'		=> LiteSetup::createString(),
			'ServerURL'		=> LiteSetup::createString()
		];
	}
}