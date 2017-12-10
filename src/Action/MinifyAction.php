<?php

namespace TechDesign\TaskProcessor\Action;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use TechDesign\TaskProcessor\Action;

class MinifyAction extends Action
{
	public function run($input)
	{
		$input = (array)$input;

		$result = [];

		foreach ($input as $file) {
			if (!$file instanceof FileInput) {
				$file = FileInput::fromPath($file);
			}

			$parts = pathinfo($file->fullPath);

			$minifier = strtolower($parts['extension']) == 'css' ? new CSS() : new JS();
			$minifier->add($file->content);
			$content = $minifier->minify();
			$file->content = $content;

			$result[] = $file;
		}

		return $result;
	}
}