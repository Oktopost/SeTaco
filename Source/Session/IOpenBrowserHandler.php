<?php
namespace SeTaco\Session;


use SeTaco\IBrowser;


interface IOpenBrowserHandler
{
	public function onOpened(IBrowser $browser): void;
}