<?php

namespace TechDesign\TaskProcessor;

class Action
{
	protected $pipe;

	public function run($input)
	{
		return $input;
	}

	/**
	 * @return mixed
	 */
	public function getPipe()
	{
		return $this->pipe;
	}

	/**
	 * @param mixed $pipe
	 * @return $this
	 */
	public function setPipe($pipe)
	{
		$this->pipe = $pipe;
		return $this;
	}
}