<?php
namespace SeTaco;


use PHPUnit\Runner\Exception;
use SeTaco\Exceptions\SeTacoException;


class BrowserAssert implements IBrowserAssert
{
	/** @var IBrowser|null */
	private $browser = null;
	
	/** @var IBrowserSession */
	private $session;
	
	
	private function browser(): IBrowser
	{
		if ($this->browser)
		{
			$b = $this->browser;
			$this->browser = null;
		}
		else 
		{
			$b = $this->session->current();
		}
		
		if (is_null($b) || $b->isClosed())
			throw new SeTacoException('No open browser to assert on found');
		
		return $b;
	}
	
	
	public function __construct(IBrowserSession $session)
	{
		$this->session = $session;
	}
	
	public function __invoke(IBrowser $browser): BrowserAssert
	{
		$this->browser = $browser;
		return $this;
	}
	
	
	public function URL(string $match, float $timeout = 0.0): void
	{
		$actual = $this->browser()->getURL();
		$startTime = microtime(true);
		
		while ($timeout >= 0)
		{
			if (fnmatch($match, $actual))
				return;
			
			usleep(1000);
			
			$endTime = microtime(true);
			$timeout -= $endTime - $startTime;
			$startTime = $endTime;
			
			$actual = $this->browser()->getURL();
		}
		
		throw new Exception("Expected URL to match '$match' but got '$actual'");
	}
	
	
	public function elementExists(string $selector, float $secToWait = 2.5): void
	{
		$this->browser()->getElement($selector, $secToWait);
	}
	
	public function elementValue(string $expected, string $selector, float $secToWait = 2.5): void
	{
		$this->elementAttribute($expected, $selector, 'value', $secToWait);
	}
	
	public function elementAttribute(string $expected, string $selector, string $name, float $secToWait = 2.5): void
	{
		$element = $this->browser()->getElement($selector, $secToWait);
		$value = $element->getAttribute($name);
		
		if ($expected !== $value)
		{
			$result = (is_null($value) ? 'null' : "\"$value\"");
			
			throw new SeTacoException("The element's <$selector> attribute \"$name\" " . 
				"doesn't match expected value \"$expected\". Got $result instead");
		}
	} 
}