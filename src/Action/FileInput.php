<?php

namespace TechDesign\TaskProcessor\Action;

class FileInput
{
	public $fullPath;
	public $baseName;
	public $content;

	public function __construct($fullPath = null, $baseName = null, $content = null)
	{
		$this->fullPath = $fullPath;
		$this->baseName = $baseName;
		$this->content = $content;

		if (!empty($fullPath) && empty($baseName)) {
			$this->baseName = basename($fullPath);
		}
	}

	public function setFullPath($fullPath)
	{
		$this->fullPath = $fullPath;
		if (!empty($fullPath)) {
			$this->baseName = basename($fullPath);
		}
	}

	public function getPathInfo()
	{
		return pathinfo($this->fullPath);
	}

	public function getExtension()
	{
		$info = $this->getPathInfo();
		return $info['extension'];
	}

	public function __toString()
	{
		return sprintf('FileInput: %s, content length: %d', $this->fullPath ?? $this->baseName, strlen($this->content));
	}

	public static function fromPath($path, $skipContentFetch = false)
	{
		return new FileInput($path, null, $skipContentFetch ? null : file_get_contents($path));
	}
}