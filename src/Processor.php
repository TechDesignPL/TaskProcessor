<?php

namespace TechDesign\TaskProcessor;

use TechDesign\TaskProcessor\Helper\Printer;

class Processor
{
	const VERSION = '1.0.0';

	/** @var array[] */
	public $taskDefinitions = [];
	/** @var TaskInterface|ThreadProxy[] */
	public $tasks = [];


	protected $classLoader;

	public function __construct($classLoader)
	{
		$this->classLoader = $classLoader;
	}

	/**
	 * @param string $taskNames
	 */
	public function run($taskNames = 'default')
	{
		if (!is_array($taskNames)) {
			$taskNames = (array)$taskNames;
		}

		$taskNames = $this->resolveChain($taskNames);

		foreach ($taskNames as $taskName) {
			$task = $this->spawnTask($taskName);

			//this is probably not needed
//			if ($task->isRunning()) {
//				$this->killTask($taskName);
//			}

			$this->tasks[$taskName] = $task;
			$task->start(PTHREADS_INHERIT_ALL);

			//wait for task to actually start
			while (!$task->awake) {
				usleep(10);
			}
		}

		$allTasksDone = false;
		while (!$allTasksDone) {
			$allTasksDone = true;
			foreach ($taskNames as $taskName) {
				if (!isset($this->tasks[$taskName])) {
					//task has ended
					continue;
				}
				$task = $this->tasks[$taskName];
				if ($task->awake) {
					$allTasksDone = false;
				} else {
					//remove done task
					$this->killTask($taskName);
				}

				//handle callbacks
				$task->synchronized(function ($task) {
					if ($task->waiting) {
						if (isset($task->callback)) {
							call_user_func([$this, $task->callback], json_decode($task->dataToPass, true));
							$task->callback = null;
							$task->dataToPass = null;
						}
						$task->waiting = false;
						$task->notify();
					}
				}, $task);
			}

			usleep(1000);
		}
	}

	public function wait()
	{
		foreach ($this->tasks as $task) {
			if ($task->isRunning()) {
				$task->join();
			}
		}
	}

	public function watch($name, $fileMask, $tasks)
	{
		$watcher = new FileWatcher($name, $fileMask, $tasks);
		$watcher->setClassLoader($this->classLoader);
		$watcher->start();
		$watcher->join();
	}

	/**
	 * Add task to execution array
	 *
	 * @param string $name
	 * @param callable|TaskInterface|array $task
	 * @param bool $isCustom
	 * @throws \Exception
	 */
	public function task($name, $task, $isCustom = false)
	{
		if (is_callable($task) && $isCustom) {
			$this->taskDefinitions[$name] = [
				'type'     => 'custom',
				'callback' => $task
			];
		} elseif (is_callable($task)) {
			//typical task
			$this->taskDefinitions[$name] = [
				'type'     => 'task',
				'callback' => $task
			];
		} elseif (is_array($task)) {
			$this->taskDefinitions[$name] = [
				'type'  => 'taskChain',
				'chain' => $task
			];
		} else {
			throw new \Exception('Unsupported task type');
		}
	}

	/**
	 * Fetch task definition and spawn instance
	 *
	 * @param $taskName
	 * @return mixed
	 * @throws \Exception
	 */
	public function spawnTask($taskName)
	{
		if (!isset($this->taskDefinitions[$taskName])) {
			Printer::prnt(sprintf('Task \'%s\' wasn\'t found in task list', $taskName));
			exit;
		}
		$definition = $this->taskDefinitions[$taskName];

		switch ($definition['type']) {
			case 'task':
				$task = new Task($taskName, $definition['callback']);
				break;
			case 'custom':
				$task = $definition['callback']();
				break;
			case 'taskChain':
			default:
				throw new \Exception('Unsupported task type: ' . $definition['type']);
		}

		if (is_object($task)) {
			$task->setClassLoader($this->classLoader);
		}

		return $task;
	}

	public function killTask($taskName)
	{
		$task = $this->tasks[$taskName];
		if ($task->awake) {
			$task->kill();
		}
		unset($this->tasks[$taskName]);
	}

	public function resolveChain($taskNames)
	{
		$result = [];
		//check if it is task chain
		foreach ($taskNames as $taskName) {
			$definition = $this->taskDefinitions[$taskName];
			if (is_array($definition) && $definition['type'] == 'taskChain') {
				//this is task chain
				$chain = $definition['chain'];
				$result = array_merge($result, $this->resolveChain($chain));
			} else {
				$result[] = $taskName;
			}
		}

		return $result;
	}

}