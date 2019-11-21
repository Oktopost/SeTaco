<?php
use SeTaco\Utils\SeleniumConnector;


require_once __DIR__ . '/vendor/autoload.php';


if (array_search('tail-log', $argv) !== false)
{
	SeleniumConnector::listen();
	return;
}
else if (array_search('stop', $argv) !== false)
{
	SeleniumConnector::stopSelenium();
	return;
}

echo "Checking if selenium instance already running...\n";

$result = SeleniumConnector::isRunning();

if ($result)
{
	echo "Selenium already running\n";
	return;
}

echo "Starting selenium...\n";
SeleniumConnector::startSelenium();
echo "Running\n";