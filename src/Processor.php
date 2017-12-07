<?php

namespace TechDesign\TaskProcessor;

use TechDesign\TaskProcessor\Helper\FileResolver;
use TechDesign\TaskProcessor\Helper\Printer;

class Processor
{
	/** @var Task[]  */
	public $tasks = [];
	protected $classLoader;

	public function __construct($classLoader)
	{
		$this->classLoader = $classLoader;
	}

	public function run($taskNames = 'default')
	{
		if (!is_array($taskNames)) {
			$taskNames = (array)$taskNames;
		}

		foreach ($taskNames as $taskName) {
			if (!isset($this->tasks[$taskName])) {
				Printer::prnt(sprintf('Task \'%s\' wasn\'t found in task list' , $taskName));
				continue;
			}

			$task = $this->tasks[$taskName];
			$task->setClassLoader($this->classLoader);
			$task->start(PTHREADS_INHERIT_ALL);
		}

		foreach ($taskNames as $taskName) {
			$task = $this->tasks[$taskName];
			$task->join();
		}
	}

	public function watch($files, $tasks)
	{
		$watcher = new FileWatcher(
			FileResolver::resolveFiles($files),
			array($this, 'run'),
			$tasks
		);
		$watcher->watch();
	}

	/**
	 * @param string $name
	 * @param callable|Task|array $callable
	 * @throws \Exception
	 */
	public function task($name, $callable)
	{
		if (is_callable($callable)) {
			$task = new Task($name, $callable);
		} elseif ($callable instanceof Task) {
			$task = $callable;
		} elseif(is_array($callable)) {
			$task = new TaskChain($this, $callable);
		} else {
			throw new \Exception('Unsupported task type');
		}
		$this->tasks[$name] = $task;
	}

}