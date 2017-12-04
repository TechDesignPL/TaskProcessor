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
			$content = $less->parseFile($file);
			$result[] = [
				'path' => substr_replace($file , 'css', strrpos($file , '.') +1 ),
				'content' => $content->getCss()
			];
		}

		return $result;
	}
}