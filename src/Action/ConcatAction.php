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
			$content .= $item instanceof FileInput ? $item->content : file_get_contents($item);
		}
		return [new FileInput(null, $this->name, $content)];
	}
}