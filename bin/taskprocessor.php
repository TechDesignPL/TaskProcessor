<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use TechDesign\TaskProcessor\Processor;
use TechDesign\TaskProcessor\Helper\Printer;

$processor = new Processor($loader);

function usage()
{
	$version = Processor::VERSION;
	$entryLine = Printer::getColoredString('php taskprocessor.phar [help] youInputScript.php [...args]', Printer::FG_LIGHT_BLUE);
	$logo = <<<EDT
    ___       ___   
   /\  \     /\  \  
   \:\  \   /::\  \ 
   /::\__\ /::\:\__\
  /:/\/__/ \/\::/  /
  \/__/       \/__/ 
EDT;
	$logo = Printer::getColoredString($logo, Printer::FG_LIGHT_GREEN);
	$subLogo = Printer::getColoredString("TaskProcessor v{$version}", Printer::FG_GREEN);
	echo <<<EDT
{$logo}    {$subLogo}

Usage:
{$entryLine}
- help                      shows this usage
- youInputScript.php        input script where your tasks are defined
- ...args                   optional, task names that are suppose to be run

EDT;

}

if ($argc < 2) {
	Printer::error('You must specify entry file!');
	exit;
}

$entryScript = $argv[1];
if (trim(strtolower($entryScript)) == 'help') {
	usage();
	exit;
}
if (!file_exists($entryScript)) {
	Printer::error('Entry file does not exist!');
	exit;
}

$tasksToRun = $argc > 2 ? array_slice($argv, 2) : [];

include_once $entryScript;

if (!empty($tasksToRun)) {
	$processor->run($tasksToRun);
}
$processor->wait();