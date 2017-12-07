<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;
use TechDesign\TaskProcessor\Helper\Printer;

class PrintAction extends Action
{
	public function run($input)
	{
		foreach ((array)$input as $value) {
			Printer::prnt($value);
		}
		return $input;
	}
}