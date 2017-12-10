<?php

use TechDesign\TaskProcessor\Action\RenameAction;

$processor->task('less', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/t1/app.less'])
		->print()
		->less()
		->dest('tests/t1/');
});

$jsTask = new \TechDesign\TaskProcessor\Task('js');
$jsTask
	->src(['tests/t2/*.js'])
	->print()
	->concat('app.js')
	->minify()
	->dest('tests/t2/out/');
$processor->task('js', $jsTask);

$processor->task('rename', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	function replaceFn($input) {
		return '.jpg';
	}
	$task
		->src(['tests/**/*.*'])
		->rename(
			RenameAction::TYPE_CUSTOM,
			'/\.(less|css)/',
			'replaceFn'
		)
		->log()
		->print();
});

$processor->task('default', ['less', 'js']);
$processor->run(['js', 'less', 'rename']);
//$processor->run('js');