<?php
/**
 * Simple example of file watcher
 */

use TechDesign\TaskProcessor\FileWatcher;
/** @var \TechDesign\TaskProcessor\Processor $processor */

$processor->task('less', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/t1/app.less'])
		->less()
		->dest('tests/t1/');
});

$processor->task('watch', function () {
	return new FileWatcher('lessWatcher', ['tests/t1/*.less'], ['less']);
}, true);

$processor->run('watch');