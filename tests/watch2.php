<?php
/**
 * Multiple file watchers
 */
use TechDesign\TaskProcessor\FileWatcher;
use TechDesign\TaskProcessor\Action\RenameAction;

/** @var \TechDesign\TaskProcessor\Processor $processor */

$processor->task('less', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/t1/app.less'])
		->less()
		->rename(RenameAction::TYPE_EXTENSION, 'css')
		->print()
		->dest('tests/t1/');
});

$processor->task('minify', function($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/t1/app.css'])
		->minify()
		->rename(RenameAction::TYPE_PREEXTENSION, '.min')
		->dest('tests/t1/');
});

$processor->task('watchLess', function () {
	return new FileWatcher('lessWatcher', ['tests/t1/*.less'], ['less']);
}, true);

$processor->task('watchCss', function () {
	return new FileWatcher('cssWatcher', ['tests/t1/app.css'], ['minify']);
}, true);

$processor->run(['watchLess', 'watchCss']);