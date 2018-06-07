<?php
namespace SeTaco;


use SeTaco\Exceptions\SeTacoException;
use SeTaco\Decorators\BrowserDecorator;


class Session implements ISession
{
	/** @var DriverConfig */
	private $config;
	
	/** @var Browser[] */
	private $browsers = [];
	
	/** @var Browser */
	private $current = null;
	
	/** @var BrowserAssert|null */
	private $assert = null;
	
	
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
	
	public function clear(): void
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
	
	public function assert(): IBrowserAssert
	{
		if (!$this->assert)
			$this->assert = new BrowserAssert($this);
		
		return $this->assert;
	}
}