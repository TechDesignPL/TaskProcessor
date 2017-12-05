<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class ConcatAction extends Action
{
	protected $name;

	public function __construct($newName)
	{
		$this->name = $newName;
	}

	public function run($input)
	{
		$input = (array)$input;

		$content = '';
		foreach ($input as $item) {
			$content .= is_string($item) ? file_get_contents($item) : $item['content'];
		}
		return [[
			'path' => $this->name,
			'content' => $content
		]];
	}
}