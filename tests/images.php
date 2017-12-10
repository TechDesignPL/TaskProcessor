<?php

$processor->task('images', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/images/*'])
		->imageOptimize()
		->print()
	;
});

$processor->run('images');