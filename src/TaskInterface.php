<?php

namespace TechDesign\TaskProcessor;

interface TaskInterface
{
	public function start(int $options = NULL);
	public function join();
	public function setClassLoader($classLoader);
//	public function synchronized(\Closure $block, $...);
}