<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class DestAction extends Action
{
	protected $dest;

	public function __construct($dest)
	{
		$this->dest = $dest;
		if (!is_dir($dest)) {
			mkdir($dest);
		}
	}

	public function run($input)
	{
		$input = (array)$input;
		foreach ($input as $item) {
			$baseName = basename($item['path']);
			file_put_contents($this->dest . $baseName, $item['content']);
		}
		return $input;
	}
}