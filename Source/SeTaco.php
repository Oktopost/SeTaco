<?php
namespace SeTaco;


use SeTaco\CLI\CLIController;


class SeTaco
{
	public static function startSeleniumIfNotRunning_CLI()
	{
		$controller = new CLIController();
		$controller->runSelenium();
	}
}