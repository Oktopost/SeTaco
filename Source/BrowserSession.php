<?php
namespace SeTaco;


use SeTaco\Config\TargetConfig;
use SeTaco\Session\IOpenBrowserHandler;
use SeTaco\Exceptions\SeTacoException;
use Structura\URL;


class BrowserSession implements IBrowserSession
{
	private const DEFAULT_BROWSER_NAME = 'defaultBrowser';
	
	
	/** @var DriverConfig */
	private $config;
	
	/** @var IOpenBrowserHandler */
	private $handler;
	
	/** @var IBrowser|null */
	private $current = null;
	
	/** @var IBrowser[] */
	private $browsers = [];
	
	
	private function hasTarget(string $targetName): bool
	{
		return isset($this->config->Targets[$targetName]);
	}
	
	private function getTarget(string $targetName): ?TargetConfig
	{
		return $this->hasTarget($targetName) ? $this->config->Targets[$targetName] : null;
	}
	
	private function openBrowser(string $browserName, TargetConfig $targetConfig): IBrowser
	{
		if ($this->hasBrowser($browserName))
		{
			$this->close($browserName);
		}
		
		$driver = $this->config()->createDriver();
		
		$browser = new Browser($driver, $targetConfig);
		
		if ($this->handler)
			$this->handler->onOpened($browser);
		
		$this->browsers[$browserName] = $browser;
		$this->current = $browser;
		
		return $browser;
	}
	
	private function openBrowserForURL(string $url, string $browserName): IBrowser
	{
		$parsedUrl = new URL($url);
		
		if (!$parsedUrl->Scheme)
		{
			if ($this->current)
				return $this->current->goto($parsedUrl->url());
			
			throw new SeTacoException('Failed to parse target and no current browser is selected');
		}
		
		$targetConfig = new TargetConfig();
		$targetConfig->URL = $parsedUrl->Scheme . '://' . $parsedUrl->Host;
		
		if ($parsedUrl->Port)
			$targetConfig->Port = $parsedUrl->Port;
		
		$browser = $this->openBrowser($browserName, $targetConfig);
		
		return $browser->goto($parsedUrl->url());
	}
	
	
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
	
	public function open(string $target, ?string $browserName = null): IBrowser
	{
		if (!$browserName)
			$browserName = self::DEFAULT_BROWSER_NAME;
		
		if ($this->hasBrowser($browserName))
		{
			$this->close($browserName);
			return $this->open($target, $browserName);
		}
		
		if (!$this->hasTarget($target))
			return $this->openBrowserForURL($target, $browserName);
		
		$target = $this->getTarget($target);
		
		return $this->openBrowser($browserName, $target)->goto($target->URL);
	}
	
	public function getBrowser(string $browserName): ?IBrowser
	{
		if (!$this->hasBrowser($browserName))
			return null;

		$this->current = $this->browsers[$browserName];
		
		return $this->current;
	}
	
	public function hasBrowser(string $browserName): bool
	{
		return isset($this->browsers[$browserName]) && !$this->browsers[$browserName]->isClosed();
	}
	
	public function hasBrowsers(): bool
	{
		return (bool)$this->browsers;
	}
	
	public function current(): ?IBrowser
	{
		return $this->current;
	}
	
	public function select(string $browserName): IBrowser
	{
		$browser = $this->getBrowser($browserName);
		
		if (!$browser || $browser->isClosed())
		{
			unset($this->browsers[$browserName]);
			throw new SeTacoException("Browser with name '$browserName' does not exist in this session!");
		}
		
		$this->current = $browser;
		
		return $browser;
	}
	
	public function closeUnused(): void
	{
		foreach ($this->browsers as $browserName => $browser)
		{
			/** @noinspection PhpNonStrictObjectEqualityInspection */
			if ($browser != $this->current)
			{
				$browser->close();
				unset($this->browsers[$browserName]);
			}
		}
	}
	
	public function close(?string $browserName = null): void
	{
		if(!$browserName)
		{
			foreach ($this->browsers as $browserName => $browser)
			{
				$this->close($browserName);
			}
			
			return;
		}
		
		if(!$this->hasBrowser($browserName))
			return;
		
		$browser = $this->getBrowser($browserName);
		
		/** @noinspection PhpNonStrictObjectEqualityInspection */
		if ($browser == $this->current)
			unset($this->current);
		
		$browser->close();
		unset($this->browsers[$browserName]);
	}
	
	public function config(): DriverConfig
	{
		return $this->config;
	}
}