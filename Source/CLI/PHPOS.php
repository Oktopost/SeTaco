<?php
namespace SeTaco\CLI;


use Traitor\TStaticClass;

class PHPOS
{
	use TStaticClass;
	
	
	public const MAC_OS		= 'Darwin';
	public const LINUX_OS	= 'Linux';
	
	
	public static function isMac(): bool
	{
		return (PHP_OS == self::MAC_OS);
	}
	
	public static function isLinux(): bool
	{
		return (PHP_OS == self::LINUX_OS);
	}
	
	public static function get(): string
	{
		return PHP_OS;
	}
}