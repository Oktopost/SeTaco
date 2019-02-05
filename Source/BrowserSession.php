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
	
	/** @var BrowserSessionContainer[]  */
	private $containers = [];
	
	
	private function createOrGetContainer(string $name, TargetConfig $targetConfig): BrowserSessionContainer
	{
		if (!isset($this->containers[$name]))
			$this->containers[$name] = new BrowserSessionContainer();
		
		$this->containers[$name]->TargetConfig = $targetConfig;
		
		return $this->containers[$name];
	}
	
	private function getContainerNames(): array
	{
		return array_keys($this->containers);
	}
	
	private function prepareContainers(): void
	{
		foreach ($this->config->Targets as $targetName => $target)
		{
			$this->createOrGetContainer($targetName, $target);
		}
	}
	
	private function hasContainer(string $name): bool
	{
		return isset($this->containers[$name]);
	}
	
	private function getContainer(string $name): ?BrowserSessionContainer
	{
		return $this->hasContainer($name) ? $this->containers[$name] : null;
	}
	
	private function openBrowser(BrowserSessionContainer $container, string $name): IBrowser
	{
		if ($container->hasBrowser($name))
		{
			$container->getBrowser($name)->close();
			unset($container->Browsers[$name]);
		}
		
		$driver = $this->config()->createDriver();
		
		$browser = new Browser($driver, $container->TargetConfig);
		
		if ($this->handler)
			$this->handler->onOpened($browser);
		
		$this->current = $name;
		$container->Current = $browser;
		
		return $browser;
	}
	
	private function openBrowserFromURL(string $url, string $name): IBrowser
	{
		$parsedUrl = new URL($url);
		
		if (!$parsedUrl->Scheme)
		{
			if ($this->current)
				return $this->current->goto($url);
			
			throw new SeTacoException('Failed to parse target and no current browser is selected');
		}
		
		$targetConfig = new TargetConfig();
		$targetConfig->URL = $parsedUrl->Scheme . '://' . $parsedUrl->Host;
		
		if ($parsedUrl->Port)
			$targetConfig->Port = $parsedUrl->Port;
		
		$container = $this->createOrGetContainer($targetConfig->URL, $targetConfig);
		$container->TargetConfig = $targetConfig;
		
		$browser = $this->openBrowser($container, $name);
		
		return $browser->goto($parsedUrl->url());
	}
	
	
	public function __construct(DriverConfig $config)
	{
		$this->config = $config;
		$this->prepareContainers();
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
		
		if (!$this->hasContainer($target))
			return $this->openBrowserFromURL($target, $browserName);
		
		$container = $this->getContainer($target);
		
		if ($container->hasBrowser($browserName))
		{
			$this->close($target, $browserName);
			return $this->open($target, $browserName);
		}
		
		return $this->openBrowser($container, $browserName)->goto($container->TargetConfig->URL);
	}
	
	public function getBrowser(string $targetName, ?string $browserName = null): ?IBrowser
	{
		$container = $this->getContainer($targetName);
		
		if (!$container)
			return null;
		
		if ($browserName)
		{
			return isset($container->Browsers[$browserName]) ? $container->Browsers[$browserName] : null;
		}
		
		return $container->Current;
	}
	
	public function hasBrowser(string $targetName, string $browserName = null): bool
	{
		return isset($this->getContainer($targetName)->Browsers[$browserName]);
	}
	
	public function hasBrowsers(?string $targetName = null): bool
	{
		if (!$targetName)
		{
			foreach ($this->getContainerNames() as $containerName)
			{
				if ($this->hasBrowsers($containerName))
					return true;
			}
			
			return false;
		}
		
		return $this->getContainer($targetName)->hasBrowsers();
	}
	
	public function current(?string $targetName = null): ?IBrowser
	{
		if ($targetName)
			return $this->getContainer($targetName)->Current;
		
		return $this->current;
	}
	
	public function select(string $targetName, ?string $browserName = null): IBrowser
	{
		$container = $this->getContainer($targetName);
		$browser = $container->Browsers[$browserName] ?? null;
		
		if (!$browser || $browser->isClosed())
		{
			unset($container->Browsers[$browserName]);
			throw new SeTacoException("Browser with name '$browserName' does not exist in this session!");
		}
		
		$this->current = $browser;
		$container->Current = $browser;
		
		return $browser;
	}
	
	public function closeUnused(?string $targetName = null): void
	{
		if (!$targetName)
		{
			foreach ($this->getContainerNames() as $container)
			{
				$this->closeUnused($container);
			}
			
			return;
		}
		
		$container = $this->getContainer($targetName);
		
		foreach ($container->Browsers as $browsername => $browser)
		{
			/** @noinspection PhpNonStrictObjectEqualityInspection */
			if ($browser != $container->Current)
				$browser->close();
		}
	}
	
	public function close(?string $targetName = null, ?string $browserName = null): void
	{
		if (!$targetName)
		{
			foreach ($this->getContainerNames() as $targetName)
			{
				$this->close($targetName, $browserName);
			}
			
			return;
		}
		
		$container = $this->getContainer($targetName);
		
		if ($browserName && isset($container->Browsers[$browserName]))
		{
			$browser = $container->Browsers[$browserName];
			
			/** @noinspection PhpNonStrictObjectEqualityInspection */
			if ($browser == $container->Current)
				unset($container->Current);
			
			$browser->close();
			unset($container->Browsers[$browserName]);
		}
		else if (!$browserName)
		{
			foreach ($container->Browsers as $browserName => $browser)
			{
				$browser->close();
				unset($container->Browsers[$browserName]);
			}
			
			unset($container->Current);
		}
	}
	
	public function config(): DriverConfig
	{
		return $this->config;
	}
}