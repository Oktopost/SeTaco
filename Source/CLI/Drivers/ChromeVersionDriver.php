<?php
namespace SeTaco\CLI\Drivers;


use Objection\LiteSetup;
use Objection\LiteObject;

use Structura\Arrays;
use SeTaco\Exceptions\SeTacoException;


/**
 * @property int $Major
 * @property int $Minor
 * @property int $Build
 * @property int $Patch
 */
class ChromeVersionDriver extends LiteObject
{
	private function getVersion(): string
	{
		$result = shell_exec('google-chrome --version | grep -iE "[0-9.]{10,20}"');
		$result = explode(' ', trim($result));
		
		return Arrays::last($result);
	}
	
	private function setVersion(): void
	{
		$version = $this->getVersion();
		
		if (!$version)
			throw new SeTacoException('Google chrome not found');
		
		$parts = explode('.', $version);
		
		$this->Major = $parts[0];
		$this->Minor = $parts[1];
		$this->Build = $parts[2] ?? 0;
		$this->Patch = $parts[3] ?? 0;
	}
	
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Major' => LiteSetup::createInt(),
			'Minor' => LiteSetup::createInt(),
			'Build' => LiteSetup::createInt(),
			'Patch' => LiteSetup::createInt()
		];
	}
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function __toString()
	{
		return "{$this->Major}.{$this->Minor}.{$this->Build}.{$this->Patch}";
	}
	
	
	public static function current(): ChromeVersionDriver
	{
		$version = new ChromeVersionDriver();
		$version->setVersion();
		
		return $version;
	}
}