<?php
use TechDesign\TaskProcessor\FileWatcher;
/** @var \TechDesign\TaskProcessor\Processor $processor */

$processor->task('less', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/t1/app.less'])
		->less()
		->dest('tests/t1/');
});

$processor->task('watch', new FileWatcher(['tests/t1/*.less'], ['less']));

$processor->run('watch');