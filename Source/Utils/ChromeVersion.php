<?php
namespace SeTaco\Utils;


use Objection\LiteObject;
use Objection\LiteSetup;
use SeTaco\Exceptions\SeTacoException;
use Structura\Arrays;


/**
 * @property int $Major
 * @property int $Minor
 */
class ChromeVersion extends LiteObject
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
	}
	
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Major' => LiteSetup::createInt(),
			'Minor' => LiteSetup::createInt()
		];
	}
	
	
	public function __construct()
	{
		parent::__construct();
		$this->setVersion();
	}
	
	
	public function __toString()
	{
		return $this->Major . '.' . $this->Minor;
	}
}