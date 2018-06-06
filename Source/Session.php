<?php
namespace SeTaco;


use SeTaco\Decorators\BrowserDecorator;
use SeTaco\Exceptions\SeTacoException;

class Session
{
	/** @var DriverConfig */
	private $config;
	
	/** @var Browser[] */
	private $browsers = [];
	
	/** @var Browser */
	private $current = null;
	
	
	private function getSetCurrentCallback(IBrowser $browser): callable
	{
		return function () use ($browser) { $this->current = $browser; };
	}
	
	
	public function __construct(DriverConfig $config)
	{
		$this->config = $config;
	}
	
	public function __destruct()
	{
		$this->clear();
	}
	
	
	public function openBrowser(): IBrowser
	{
		$driver = $this->config()->createDriver();
		$browser = new Browser($driver, $this->config);
		
		$decorator = new BrowserDecorator($browser);
		$decorator->setCallback($this->getSetCurrentCallback($decorator));
		
		$this->current = $decorator;
		$this->browsers[] = $decorator;
		
		return $decorator;
	}
	
	public function config(): DriverConfig
	{
		return $this->config;
	}
	
	public function clear()
	{
		foreach ($this->browsers as $browser)
		{
			$browser->destroy();
		}
		
		$this->browsers = [];
	}
	
	public function current(): IBrowser
	{
		if (!$this->current || $this->current->isDestroyed())
			throw new SeTacoException('No active browser exist. ' . 
				'Make sure the browser was not closed before calling current');
		
		return $this->current;
	}
}