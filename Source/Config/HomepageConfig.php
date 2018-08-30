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
	
	
	public function getURL(string $for): string
	{
		if (substr($for, 0, 4) == 'http')
			return $for;
		
		$url = $this->URL;
		
		if ($for && $url && $url[strlen($url) - 1] != '/')
			$url .= '/';
		
		if ($for && $for[0] == '/')
			$for = substr($for, 1);
		
		$url = $url . $for;
		
		if ($url && $this->Port != 80)
		{
			$parts = parse_url($url);
			
			$search = 
				(isset($parts['scheme']) ? $parts['scheme'] . '://' : '') . 
				($parts['host'] ?? '') .
				(isset($parts['port']) ? ':' . $parts['port'] : '');
			
			$replace = 
				(isset($parts['scheme']) ? $parts['scheme'] . '://' : '') . 
				($parts['host'] ?? '') .
				':' . $this->Port;
			
			if ($search)
			{
				$url = $replace . substr($url, strlen($search));
			}
		}
		
		return $url;
	}
}