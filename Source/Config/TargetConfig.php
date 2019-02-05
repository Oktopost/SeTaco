<?php
namespace SeTaco\Config;


use Objection\LiteObject;
use Objection\LiteSetup;
use Structura\URL;


/**
 * @property string	$URL
 * @property int	$Port
 */
class TargetConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'URL'	=> LiteSetup::createString(''),
			'Port'	=> LiteSetup::createInt()
		];
	}
	
	
	public function getURL(string $for): string
	{
		if ((new URL($for))->Scheme)
			return $for;
		
		$parsed = new URL($this->URL);
		
		if ($this->Port)
			$parsed->Port = $this->Port;
		
		$parsed->Path = $for;
		
		return $parsed->url();
	}
}