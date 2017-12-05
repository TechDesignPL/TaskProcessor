<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class PrintAction extends Action
{
	public function run($input)
	{
		foreach ((array)$input as $value) {
			printf($value . PHP_EOL);
		}
		return $input;
	}
}