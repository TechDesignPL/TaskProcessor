<?php

namespace TechDesign\TaskProcessor;

class Pipe
{
	/** @var Action[]|callable[] */
	protected $callableChain = [];

	public function schedule($action)
	{
		$this->callableChain[] = $action;
		return $this;
	}

	public function run()
	{
		$input = func_get_args();

		foreach ($this->callableChain as $action) {
			if (is_callable($action)) {
				$result = $action(isset($result) ? $result : $input);
			} elseif ($action instanceof Action) {
				$result = $action->run(isset($result) ? $result : $input);
			} else {
				throw new \Exception('Task must be a function or instance of Action!');
			}

		}

		return isset($result) ? $result : null;
	}
}