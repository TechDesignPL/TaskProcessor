<?php

$processor->task('less', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/t1/app.less'])
		->less()
		->minify()
		->dest('tests/t1/');
});

$processor->run('less');