<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;
use TechDesign\TaskProcessor\Helper\FileResolver;

class SrcAction extends Action
{
	protected $srcFiles = [];

	public function __construct($files)
	{
		$this->srcFiles = (array)$files;
	}

	public function run($input)
	{
		return FileResolver::resolveFiles($this->srcFiles);
	}
}