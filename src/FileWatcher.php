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
	protected $name;

	public $callback;
	public $dataToPass;

	/** @var \Composer\Autoload\ClassLoader */
	protected $classLoader;

	public function __construct($name, $fileMask, $tasksToExecute, $interval = 500)
	{
		$this->name = $name;
		$this->fileMask = $fileMask;
		$this->tasksToExecute = $tasksToExecute;
		$this->interval = $interval;
	}

	public function run()
	{
		$this->awake = true;

		if (!class_exists('Printer')) {
			$this->classLoader->register();
		}

		Printer::prnt(sprintf('Watcher \'%s\' started.' ,  $this->name), Printer::FG_CYAN);

		$resolved = FileResolver::resolveFiles($this->fileMask);

		$filesToWatch = [];
		foreach ($resolved as $file) {
			$f = new \stdClass();
			$f->file = $file;
			$f->mtime = filemtime($file);
			$filesToWatch[] = $f;
		}

		while (true) {
			if ($this->checkUpdates($filesToWatch)) {
				Printer::prnt('Change detected!');
				$this->synchronized(function($thread){
					$thread->callback = 'run';
					$thread->dataToPass = json_encode($thread->tasksToExecute);
					$thread->waiting = true;
					$thread->wait();
				}, $this);
			}

			usleep($this->interval * 1000);
		}

		$this->awake = false;
		return true;
	}

	public function checkUpdates($filesToWatch)
	{
		$changeDetected = false;

		foreach ($filesToWatch as $file) {
			$mTime = filemtime($file->file);
			if ($mTime > $file->mtime) {
				$file->mtime = $mTime;
				$changeDetected = true;
			}
		}
		clearstatcache();
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

	/**
	 * @param mixed $callback
	 * @return $this
	 */
	public function setCallback($callback)
	{
		$this->callback = $callback;
		return $this;
	}
}