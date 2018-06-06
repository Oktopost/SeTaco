<?php
namespace SeTaco;


use Facebook\WebDriver\WebDriverPlatform;
use Traitor\TEnum;


class OSType
{
	use TEnum;
	
	
	public const ANY		= WebDriverPlatform::ANY;
	public const LINUX		= WebDriverPlatform::LINUX;
	public const UNIX		= WebDriverPlatform::UNIX;
	public const MAC		= WebDriverPlatform::MAC;
	public const ANDROID	= WebDriverPlatform::ANDROID;
	public const WINDOWS	= WebDriverPlatform::WINDOWS;
}