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
			if (is_string($file)) {
				$file = [
					'content' => file_get_contents($file),
					'path' => $file
				];
			}
			$parts = pathinfo($file['path']);

			$minifier = strtolower($parts['extension']) == 'css' ? new CSS() : new JS();
			$minifier->add($file['content']);
			$content = $minifier->minify();

			$result[] = [
				'path'    => $parts['dirname'] . '/' . $parts['filename'] . '.min.' . $parts['extension'],
				'content' => $content
			];
		}

		return $result;
	}
}