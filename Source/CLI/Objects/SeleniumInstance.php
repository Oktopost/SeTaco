<?php
namespace SeTaco\CLI\Objects;


use SeTaco\CLI\Drivers\SeleniumDriver;
use Traitor\TSingleton;


class SeleniumInstance
{
	use TSingleton;
	
	
	private $stop = false;
	
	
	public function __destruct()
	{
		if ($this->stop)
		{
			SeleniumDriver::stopSelenium();
		}
	}
	
	
	public function stopOnShutdown(): void 
	{
		$this->stop = true;
	}
	
	public function doNothingOnShutdown(): void
	{
		$this->stop = false;
	}
}