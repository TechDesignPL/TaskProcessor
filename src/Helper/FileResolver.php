<?php

namespace TechDesign\TaskProcessor\Helper;

class FileResolver
{
	/**
	 * @param array $files
	 * @return array
	 */
	public static function resolveFiles($files)
	{
		$result = [];

		foreach ($files as $file) {
			if (file_exists($file)) {
				$result[] = $file;
			} else {
				$result = array_merge($result, self::globstar($file));
			}
		}

		return $result;
	}

	public static function globstar($pattern, $flags = 0)
	{
		if (stripos($pattern, '**') === false) {
			$files = glob($pattern, $flags);
		} else {
			$position = stripos($pattern, '**');
			$rootPattern = substr($pattern, 0, $position - 1);
			$restPattern = substr($pattern, $position + 2);
			$patterns = array($rootPattern . $restPattern);
			$rootPattern .= '/*';
			while ($dirs = glob($rootPattern, GLOB_ONLYDIR)) {
				$rootPattern .= '/*';
				foreach ($dirs as $dir) {
					$patterns[] = $dir . $restPattern;
				}
			}
			$files = array();
			foreach ($patterns as $pat) {
				$files = array_merge($files, self::globstar($pat, $flags));
			}
		}
		$files = array_unique($files);
		sort($files);
		return $files;
	}
}