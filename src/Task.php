<?php

namespace TechDesign\TaskProcessor;

class Task
{
	protected $name;
	protected $pipe;
	protected $started;
	protected $ended;

	public function __construct($name, $callable)
	{
		$this->name = $name;
		$this->pipe = new Pipe();
		call_user_func($callable, $this->pipe);
	}

	public function run()
	{
		$this->started = microtime(true);
		printf('Task \'%s\' started.' . PHP_EOL, $this->name);
		$this->pipe->run(null);
		$this->ended = microtime(true);
		printf('Task \'%s\' ended, took: %fs' . PHP_EOL, $this->name, $this->getTimeSpent());
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return float
	 */
	public function getStarted()
	{
		return $this->started;
	}

	/**
	 * @return float
	 */
	public function getEnded()
	{
		return $this->ended;
	}

	public function getTimeSpent()
	{
		$spent = ($this->ended - $this->started);
		return $spent;
	}
}