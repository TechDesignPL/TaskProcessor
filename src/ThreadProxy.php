<?php

namespace TechDesign\TaskProcessor;

if (class_exists('Thread')) {
	class ThreadProxy extends \Thread
	{
	}
} else {
	class ThreadProxy
	{
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
	}
}
