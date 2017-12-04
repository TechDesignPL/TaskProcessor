<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class SrcAction extends Action
{
	protected $srcFiles = [];

	public function __construct($files)
	{
		$this->srcFiles = (array)$files;
	}

	public function run($input)
	{
		$result = [];
		foreach ($this->srcFiles as $item) {

			$result = array_merge($result, $this->unmask($item));
		}
		return $result;
	}

	public function unmask($file)
	{
		if (file_exists($file)) {
			return (array)$file;
		}

		return glob($file);
	}
}