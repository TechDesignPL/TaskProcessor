<?php

namespace TechDesign\TaskProcessor;

use TechDesign\TaskProcessor\Helper\Printer;

class Processor
{
	const VERSION = '1.0.0';

	/** @var TaskInterface[]  */
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
			$task->start(PTHREADS_INHERIT_ALL);
		}

		foreach ($taskNames as $taskName) {
			$task = $this->tasks[$taskName];
			$task->join();
		}
	}

	public function watch($files, $tasks)
	{
		$watcher = new FileWatcher($files, $tasks);
		$watcher->setProcessor($this);
		$watcher->setClassLoader($this->classLoader);
		$watcher->start();
		$watcher->join();
	}

	/**
	 * @param string $name
	 * @param callable|TaskInterface|array $task
	 * @throws \Exception
	 */
	public function task($name, $task)
	{
		if (is_callable($task)) {
			$task = new Task($name, $task);
		} elseif ($task instanceof TaskInterface) {
			$task->setClassLoader($this->classLoader);
			$task->setProcessor($this);
		} elseif(is_array($task)) {
			$task = new TaskChain($this, $task);
		} else {
			throw new \Exception('Unsupported task type');
		}
		$this->tasks[$name] = $task;
	}

}