<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Traitor\TEnum;


class BrowserType
{
	use TEnum;
	
	
	public const FIREFOX		= WebDriverBrowserType::FIREFOX;
	public const FIREFOX_PROXY	= WebDriverBrowserType::FIREFOX_PROXY;
	public const FIREFOX_CHROME	= WebDriverBrowserType::FIREFOX_CHROME;
	public const GOOGLE_CHROME	= WebDriverBrowserType::GOOGLECHROME;
	public const SAFARI			= WebDriverBrowserType::SAFARI;
	public const SAFARI_PROXY	= WebDriverBrowserType::SAFARI_PROXY;
	public const OPERA			= WebDriverBrowserType::OPERA;
	public const MICROSOFT_EDGE	= WebDriverBrowserType::MICROSOFT_EDGE;
	public const IEXPLORE		= WebDriverBrowserType::IEXPLORE;
	public const IEXPLORE_PROXY	= WebDriverBrowserType::IEXPLORE_PROXY;
	public const CHROME			= WebDriverBrowserType::CHROME;
	public const KONQUEROR		= WebDriverBrowserType::KONQUEROR;
	public const MOCK			= WebDriverBrowserType::MOCK;
	public const IE_HTA			= WebDriverBrowserType::IE_HTA;
	public const ANDROID		= WebDriverBrowserType::ANDROID;
	public const HTML_UNIT		= WebDriverBrowserType::HTMLUNIT;
	public const IE				= WebDriverBrowserType::IE;
	public const IPHONE			= WebDriverBrowserType::IPHONE;
	public const IPAD			= WebDriverBrowserType::IPAD;
	public const PHANTOM_JS		= WebDriverBrowserType::PHANTOMJS;
}