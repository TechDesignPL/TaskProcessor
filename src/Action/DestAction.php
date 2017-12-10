<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class DestAction extends Action
{
	protected $dest;

	public function __construct($dest)
	{
		$this->dest = rtrim($dest, '\\/');
		if (!is_dir($dest)) {
			mkdir($dest);
		}
	}

	public function run($input)
	{
		$input = (array)$input;
		foreach ($input as $item) {
			if (!$item instanceof FileInput) {
				throw new \Exception('Dest action requires FileInput to operate');
			}

			file_put_contents("{$this->dest}/{$item->baseName}", $item->content);
		}

		//pass input as it is
		return $input;
	}
}