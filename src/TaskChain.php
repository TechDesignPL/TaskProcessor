<?php

namespace TechDesign\TaskProcessor;

class TaskChain
{
	protected $processor;
	protected $chain;
	public function __construct(Processor $processor, $chain)
	{
		$this->processor = $processor;
		$this->chain = $chain;
	}
	
	public function run() 
	{
		foreach ($this->chain as $taskName)  {
			$this->processor->tasks[$taskName]->run();
		}
	}
}