<?php

namespace TechDesign\TaskProcessor;

class Processor
{
	protected $tasks = [];

	public function run($taskName = 'default')
	{
		if (!isset($this->tasks[$taskName])) {
			printf('Task \'%s\' wasn\'t found in task list', $taskName);
		}

		$task = $this->tasks[$taskName];
		$task->run();
	}

	public function task($name, $callable)
	{
		$task = new Task($name, $callable);
		$this->tasks[$name] = $task;
	}

}