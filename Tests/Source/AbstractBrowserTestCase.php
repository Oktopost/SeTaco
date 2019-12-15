<?php
use SeTaco\BrowserSession;
use SeTaco\IBrowser;
use SeTaco\TacoConfig;
use Structura\Strings;


abstract class AbstractBrowserTestCase extends \PHPUnit\Framework\TestCase
{
	private static $path = __DIR__ . '/../html';
	
	/** @var BrowserSession */
	private static $session = null;
	
	
	protected function getBrowserToFile(string $path): IBrowser
	{
		if (self::$session)
		{
			self::$session->close();
		}
		
		self::$session = new BrowserSession(TacoConfig::parse([]));
		
		$browser = self::$session->open('default');
		
		$path = Strings::startWith($path, '/');
		$path = Strings::endWith($path, '.html');
		$path = realpath(self::$path . $path);
		
		$browser->goto("file://$path");
		
		return $browser;
	}
	
	protected function getBrowser(string $content): IBrowser
	{
		$tmpFile = self::$path . '/tmp.html';
		$templateFile = self::$path . '/_template_.html';
		
		if (file_exists($tmpFile))
			unlink($tmpFile);
		
		$template = file_get_contents($templateFile);
		$data = str_replace('{data}', $content, $template);
		
		file_put_contents($tmpFile, $data);
		
		return $this->getBrowserToFile('tmp.html');
	}
	
	
	public static function setBasePath(string $path): void
	{
		self::$path = $path;
	}
}