<?php
namespace SeTaco;


use Objection\LiteObject;
use Objection\LiteSetup;
use SeTaco\Config\TargetConfig;


/**
 * @property IBrowser|null $Current
 * @property IBrowser[] $Browsers
 * @property TargetConfig $TargetConfig
 */
class BrowserSessionContainer extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Current'		=> LiteSetup::createInstanceOf(IBrowser::class),
			'Browsers'		=> LiteSetup::createInstanceArray(IBrowser::class),
			'TargetConfig'	=> LiteSetup::createInstanceOf(TargetConfig::class)
		];
	}
	
	
	public function setCurrent(IBrowser $browser): void
	{
		$this->Current = $browser;
	}
	
	public function getCurrent(): ?IBrowser
	{
		return $this->Current;
	}
	
	public function hasBrowser(string $name): bool
	{
		$browserNames = array_flip(array_keys($this->Browsers));
		return isset($browserNames[$name]);
	}
	
	public function getBrowser(string $name): ?IBrowser
	{
		return $this->Browsers[$name] ?? null;
	}
	
	public function hasBrowsers(): bool
	{
		return count($this->Browsers) > 0;
	}
}