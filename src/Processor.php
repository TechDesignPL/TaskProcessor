<?php

namespace TechDesign\TaskProcessor;

class Processor
{
	/** @var Task[]  */
	public $tasks = [];

	public function run($taskName = 'default')
	{
		if (!isset($this->tasks[$taskName])) {
			printf('Task \'%s\' wasn\'t found in task list', $taskName);
		}

		$task = $this->tasks[$taskName];
		$task->run();
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