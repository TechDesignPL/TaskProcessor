<?php

namespace TechDesign\TaskProcessor;

class TaskChain extends ThreadProxy implements TaskInterface
{
	protected $chain;
	/** @var \Composer\Autoload\ClassLoader */
	protected $classLoader;
	/** @var Processor */
	protected $processor;

	public function __construct(Processor $processor, $chain)
	{
		$this->processor = $processor;
		$this->chain = $chain;
	}
	
	public function run()
	{
		if (!class_exists('Printer')) {
			$this->classLoader->register();
		}

		foreach ($this->chain as $taskName)  {
			$this->processor->tasks[$taskName]->run();
		}
	}

	public function setClassLoader($classLoader)
	{
		$this->classLoader = $classLoader;
	}

	public function setProcessor($processor)
	{
		$this->processor = $processor;
	}
}