<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class RenameAction extends Action
{
	const TYPE_PREFIX = 1;
	const TYPE_SUFFIX = 2;
	const TYPE_EXTENSION = 3;
	const TYPE_PREEXTENSION = 4;
	const TYPE_CUSTOM = 5;

	protected $changeType;
	protected $string;
	protected $callback;

	public function __construct($changeType, $string, $callback = null)
	{
		$this->changeType = $changeType;
		$this->string = $string;
		$this->callback = $callback;
	}

	public function run($input)
	{
		$input = (array)$input;
		$result = [];
		foreach ($input as $file) {
			$result[] = $this->rename($file);
		}
		return $result;
	}

	public function rename($file)
	{
		$parts = pathinfo($file);


		switch ($this->changeType) {
			case self::TYPE_PREFIX:
				return $parts['dirname'] . '/' . $this->string . $parts['basename'];
			case self::TYPE_SUFFIX:
				return $file . $this->string;
			case self::TYPE_EXTENSION:
				return $parts['dirname'] . '/' . $parts['filename'] . '.' . $this->string;
			case self::TYPE_PREEXTENSION:
				return $parts['dirname'] . '/' . $parts['filename'] . $this->string . '.' . $parts['extension'];
			case self::TYPE_CUSTOM:
				return preg_replace_callback($this->string, $this->callback, $file);
				break;
			default:
				throw new \Exception('Unrecognized change type');
				break;
		}
	}
}