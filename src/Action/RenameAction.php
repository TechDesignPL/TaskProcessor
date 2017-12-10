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
			if (!$file instanceof FileInput) {
				$file = FileInput::fromPath($file);
			}
			$result[] = $this->rename($file);
		}
		return $result;
	}

	public function rename(FileInput $file)
	{
		$parts = pathinfo($file->fullPath);

		switch ($this->changeType) {
			case self::TYPE_PREFIX:
				$file->setFullPath($parts['dirname'] . '/' . $this->string . $parts['basename']);
				break;
			case self::TYPE_SUFFIX:
				$file->setFullPath($file . $this->string);
				break;
			case self::TYPE_EXTENSION:
				$file->setFullPath($parts['dirname'] . '/' . $parts['filename'] . '.' . $this->string);
				break;
			case self::TYPE_PREEXTENSION:
				$file->setFullPath($parts['dirname'] . '/' . $parts['filename'] . $this->string . '.' . $parts['extension']);
				break;
			case self::TYPE_CUSTOM:
				$file->setFullPath(preg_replace_callback($this->string, $this->callback, $file));
				break;
			default:
				throw new \Exception('Unrecognized change type');
				break;
		}
		return $file;
	}
}