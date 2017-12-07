<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use TechDesign\TaskProcessor\Processor;
use TechDesign\TaskProcessor\Action;

$processor = new Processor($loader);

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
	->schedule(new Action\SrcAction(['tests/t2/*.js']))
	->schedule(new Action\PrintAction())
	->schedule(new Action\ConcatAction('app.js'))
	->schedule(new Action\MinifyAction())
	->schedule(new Action\DestAction('tests/t2/out/'));
$processor->task('js', $jsTask);

$processor->task('rename', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	function replaceFn($input) {
		return '.jpg';
	}
	$task
		->schedule(new Action\SrcAction(['tests/**/*.*']))
		->schedule(new Action\RenameAction(
			Action\RenameAction::TYPE_CUSTOM,
			'/\.(less|css)/',
			'replaceFn'
		))
		->schedule(new Action\PrintAction());
});
$processor->task('default', ['less', 'js']);
$processor->run(['js', 'less', 'rename']);
//$processor->run('js');