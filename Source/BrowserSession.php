<?php
namespace SeTaco;


use SeTaco\Session\IOpenBrowserHandler;
use SeTaco\Exceptions\SeTacoException;


class BrowserSession implements IBrowserSession
{
	/** @var DriverConfig */
	private $config;
	
	/** @var Browser[] */
	private $browsers = [];
	
	/** @var IOpenBrowserHandler */
	private $handler;
	
	/** @var string|null */
	private $current = null;
	
	
	public function __construct(DriverConfig $config)
	{
		$this->config = $config;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	
	public function setOpenBrowserHandler(IOpenBrowserHandler $handler): void
	{
		$this->handler = $handler;
	}
	
	public function openBrowser(string $name): IBrowser
	{
		if (isset($this->browsers[$name]))
		{
			$this->browsers[$name]->close();
		}
		
		$driver = $this->config()->createDriver();
		$browser = new Browser($driver, $this->config);
		
		if ($this->handler)
		{
			$this->handler->onOpened($browser);
		}
		
		$this->current = $name;
		$this->browsers[$name] = $browser;
	}
	
	public function getBrowser(string $name): ?IBrowser
	{
		$browser = $this->browsers[$name] ?? null;
		
		if ($browser && $browser->isClosed())
		{
			unset($this->browsers[$name]);
			return null;
		}
		
		return $browser;
	}
	
	public function hasBrowser(string $name): bool
	{
		return isset($this->browsers[$name]);
	}
	
	public function hasBrowsers(): bool
	{
		return (bool)$this->browsers;
	}
	
	public function current(): ?IBrowser
	{
		return $this->current ?
			$this->browsers[$this->current] :
			null;
	}
	
	public function select(string $name): IBrowser
	{
		$browser = $this->browsers[$name] ?? null;
		
		if (!$browser || $browser->isClosed())
		{
			unset($this->browsers[$name]);
			throw new SeTacoException("Browser with name '$name' does not exist in this session!");
		}
		
		$this->current = $name;
		
		return $browser;
	}
	
	public function closeUnused(): void
	{
		foreach ($this->browsers as $name => $browser)
		{
			if ($this->current === $name)
				continue;
			
			$browser->close();
			unset($this->browsers[$name]);
		}
	}
	
	public function close(?string $name = null): void
	{
		if ($name)
		{
			$browser = $this->getBrowser($name);
			
			if (!$browser)
				return;
			
			unset($this->browsers[$name]);
			$browser->close();
			
			if ($name != $this->current)
				return;
			
			$this->current = null;
			
			foreach ($this->browsers as $name => $browser)
			{
				$browser = $this->getBrowser($name);
				
				if ($browser)
				{
					$this->current = $name;
					break;
				}
			}
		}
		else
		{
			foreach ($this->browsers as $browser)
			{
				$browser->close();
			}
			
			$this->browsers = [];
			$this->current = [];
		}
	}
	
	public function config(): DriverConfig
	{
		return $this->config;
	}
}