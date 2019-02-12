<?php
namespace SeTaco;

use PHPUnit\Framework\TestCase;
use SeTaco\Session\IOpenBrowserHandler;


class BrowserSessionTest extends TestCase 
{
	private $driverConfig;
	
	
	private function getPlainConfig(): array
	{
		$path = str_replace('Source', 'html', __DIR__);
		
		return [
			'targets' => [
				'file1'	=> [
					'URL'	=> 'file://' . $path . '/file1.html'
				],
				'file2' => [
					'URL'	=> 'file://' . $path . '/file2.html'
				]
			]
		];
	}
	
	private function getDriverConfig(): TacoConfig
	{
		if ($this->driverConfig)
			return $this->driverConfig;
		
		
		$this->driverConfig = TacoConfig::parse($this->getPlainConfig());
		
		return $this->getDriverConfig();
	}
	
	
	public function test_openBrowserHandler()
	{
		$handler = new class implements IOpenBrowserHandler 
		{
			private $success = false;
			
			
			public function onOpened(IBrowser $browser): void
			{
				$this->success = true;
			}
			
			public function isSuccess(): bool
			{
				return $this->success;
			}
		};
		
		
		$session = new BrowserSession($this->getDriverConfig());
		$session->setOpenBrowserHandler($handler);
		$session->open('file1');
		
		self::assertTrue($handler->isSuccess());
	}
	
	public function test_getBrowser()
	{
		$browserName = "weirdRandomName";
		$session = new BrowserSession($this->getDriverConfig());
		$session->open('file1', $browserName);
		
		$browser = $session->getBrowser($browserName);
		self::assertSame($browserName, $browser->getBrowserName());
	}
	
	public function test_hasBrowser()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$session->open('file1', 'a');
		
		self::assertTrue($session->hasBrowser('a'));
		
		$session->open('file1', 'b');
		self::assertTrue($session->hasBrowser('a'));
		self::assertTrue($session->hasBrowser('b'));
		
	}
	
	public function test_hasBrowsers()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$session->open('file1', 'a');
		
		self::assertTrue($session->hasBrowsers());
		
		$session->open('file1', 'b');
		
		self::assertTrue($session->hasBrowsers());		
	}
	
	public function test_openMultipleSessions()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$browser1 = $session->open('file1', 'a');
		$browser2 = $session->open('file2', 'b');
		
		self::assertSame([
			$browser1->getURL(),
			$browser2->getURL()
		], [
			$this->getPlainConfig()['targets']['file1']['URL'],
			$this->getPlainConfig()['targets']['file2']['URL']
		]);
	}
	
	public function test_currentBrowserSelection()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$browser1 = $session->open('file1', 'a');
		
		self::assertSame($browser1, $session->current());
		
		$browser2 = $session->open('file1', 'b');
		
		self::assertSame($browser2, $session->current());
	}
	
	public function test_specificBrowsersSelection()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$browser1 = $session->open('file1', 'a');
		$browser2 = $session->open('file1', 'b');
		
		$session->select($browser1);
		self::assertSame($browser1, $session->current());
		
		$session->select('b');
		self::assertSame($browser2, $session->current());
	}
	
	public function test_closeUnused()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$browser1 = $session->open('file1', 'a');
		$browser2 = $session->open('file1', 'b');
		$browser3 = $session->open('file1', 'c');
		
		$session->closeUnused();
		self::assertTrue($browser1->isClosed());
		self::assertTrue($browser2->isClosed());
		self::assertFalse($browser3->isClosed());
	}
	
	public function test_closeByReference()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$browser1 = $session->open('file1', 'a');
		$session->close($browser1);
		
		self::assertTrue($browser1->isClosed());
	}
	
	public function test_closeByName()
	{
		$session = new BrowserSession($this->getDriverConfig());
		$browser1 = $session->open('file1', 'a');
		$session->close('a');
		
		self::assertTrue($browser1->isClosed());
	}
}