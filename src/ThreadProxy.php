<?php

namespace TechDesign\TaskProcessor;

if (class_exists('Thread')) {
	class ThreadProxy extends \Thread
	{
		public $awake;
		public $waiting;
	}
} else {
	class ThreadProxy
	{
		public $awake;
		public $waiting;

		public function run()
		{
			return true;
		}

		public function start(int $options = null)
		{
			return $this->run();
		}

		public function join()
		{
		}

		public function isJoined()
		{
			return true;
		}

		public function synchronized($callable, $context)
		{

		}
	}
}
