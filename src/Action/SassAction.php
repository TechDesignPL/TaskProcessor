<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;
use TechDesign\TaskProcessor\Helper\ShellHelper;

class SassAction extends Action
{
	const BIN_PATH = __DIR__ . '/../bin/{os}/sassc';

	public function run($input)
	{
		$input = (array)$input;
		$result = [];

		foreach ($input as $file) {
			if (!$file instanceof FileInput) {
				$file = FileInput::fromPath($file, true);
			}

			$out = ShellHelper::execShell(self::BIN_PATH, $file->fullPath);

			$file->content = $out;
			$result[] = $file;
		}

		return $result;
	}
}