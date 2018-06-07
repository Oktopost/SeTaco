<?php
namespace SeTaco;


use SeTaco\Exceptions\SeTacoException;


class BrowserAssert implements IBrowserAssert
{
	/** @var IBrowser|null */
	private $browser = null;
	
	/** @var ISession */
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
		
		if (is_null($b) || $b->isDestroyed())
			throw new SeTacoException('No open browser to assert on found');
		
		return $b;
	}
	
	
	public function __construct(ISession $session)
	{
		$this->session = $session;
	}
	
	public function __invoke(IBrowser $browser): BrowserAssert
	{
		$this->browser = $browser;
		return $this;
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