<?php
namespace SeTaco\Config;


use Objection\LiteObject;
use Objection\LiteSetup;


/**
 * @property string	$URL
 * @property int	$Port
 */
class HomepageConfig extends LiteObject
{
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'URL'	=> LiteSetup::createString('http://localhost'),
			'Port'	=> LiteSetup::createInt(80)
		];
	}
}