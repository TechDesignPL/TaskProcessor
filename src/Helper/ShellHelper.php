<?php

namespace TechDesign\TaskProcessor\Helper;

class ShellHelper
{
	public static function execShell()
	{
		$args = func_get_args();

		$cmd = array_shift($args);

		// add exe prefix when on windows
		$cmd = self::detectOS($cmd);

		// move binary to tmp folder if exe resides inside stream such as phar
		if (preg_match('~\w+://~', $cmd)) {
			$cmd = self::copyBinToTemp($cmd);
		}

		foreach ($args as $arg) {
			$cmd .= ' ' . escapeshellarg($arg);
		}

		return shell_exec($cmd);
	}

	public static function copyBinToTemp($binFile)
	{
		$tempFilename = sys_get_temp_dir() . '/' .  basename($binFile);
		if (file_exists($tempFilename)) {
			return $tempFilename;
		}

		$readHandle = fopen($binFile, 'r');
		$writeHandle = fopen($tempFilename, 'w');

		while (!feof($readHandle)) {
			$result = fread($readHandle, 512);
			fwrite($writeHandle, $result);
		}

		fclose($readHandle);
		fclose($writeHandle);

		return $tempFilename;
	}

	public static function detectOS($binary)
	{
		if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
			return $binary . '.exe';
		} else {
			return $binary;
		}
	}
}