<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class LogAction extends Action
{
	protected $logFile;
	protected $timestampFormat;

	public function __construct($logFile, $timestampFormat = 'Y-m-d H:i:s')
	{
		$this->logFile = $logFile;
		$this->timestampFormat = $timestampFormat;
	}

	public function logVariable($var)
	{
		$timestamp = empty($this->timestampFormat) ? '' : '[' . date($this->timestampFormat) . '] ';
		$encoded = @json_encode($var, JSON_PRETTY_PRINT);
		if ($encoded === null) {
			$encoded = var_export($var, true);
		}

		return $timestamp . $encoded . PHP_EOL;
	}

	public function run($input)
	{

		$handle = fopen($this->logFile, 'a');
		foreach ((array)$input as $value) {
			fwrite($handle, $this->logVariable($value));
		}
		fclose($handle);
		return $input;
	}
}