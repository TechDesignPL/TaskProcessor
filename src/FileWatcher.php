<?php

namespace TechDesign\TaskProcessor;

use TechDesign\TaskProcessor\Helper\FileResolver;
use TechDesign\TaskProcessor\Helper\Printer;

class FileWatcher extends ThreadProxy implements TaskInterface
{
	protected $fileMask;
	protected $filesToWatch = [];
	protected $tasksToExecute;
	protected $interval;

	/** @var Processor */
	protected $processor;
	/** @var \Composer\Autoload\ClassLoader */
	protected $classLoader;

	public function __construct($fileMask, $tasksToExecute, $interval = 500)
	{
		$this->fileMask = $fileMask;
		$this->tasksToExecute = $tasksToExecute;
		$this->interval = $interval;
	}

	public function run()
	{
		if (!class_exists('Printer')) {
			$this->classLoader->register();
		}

		$resolved = FileResolver::resolveFiles($this->fileMask);

		foreach ($resolved as $file) {
			$f = new \stdClass();
			$f->file = $file;
			$f->mtime = filemtime($file);
			$this->filesToWatch[] = $f;
		}

		while (true) {
			if ($this->checkUpdates()) {
				Printer::prnt('Change detected!');
				call_user_func([$this->processor, 'run'], $this->tasksToExecute);
			}

			clearstatcache();
			usleep($this->interval * 1000);
		}
	}

	public function checkUpdates()
	{
		$changeDetected = false;

		foreach ($this->filesToWatch as $file) {
			$mTime = filemtime($file->file);
			if ($mTime > $file->mtime) {
				$file->mtime = $mTime;
				$changeDetected = true;
			}
		}

		return $changeDetected;
	}

	/**
	 * If multithreading is enabled then we need to have composer autoloader class inside child threads
	 *
	 * @param $classLoader
	 */
	public function setClassLoader($classLoader)
	{
		$this->classLoader = $classLoader;
	}

	public function setProcessor($processor)
	{
		$this->processor = $processor;
	}
}