<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class LessAction extends Action
{
	public function run($input)
	{
		$input = (array)$input;

		$result = [];
		$less = new \Less_Parser();
		foreach ($input as $file) {
			if (!$file instanceof FileInput) {
				$file = FileInput::fromPath($file);
			}

			$content = $less->parseFile($file->fullPath);
			$file->content = $content->getCss();
			$result[] = $file;
		}

		return $result;
	}
}