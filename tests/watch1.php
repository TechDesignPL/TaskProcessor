<?php

include_once __DIR__ . '/../vendor/autoload.php';

use TechDesign\TaskProcessor\Processor;
use TechDesign\TaskProcessor\Action;

$processor = new Processor();

$processor->task('less', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->schedule(new Action\SrcAction(['tests/t1/app.less']))
		->schedule(new Action\LessAction())
		->schedule(new Action\DestAction('tests/t1/'));
});

$processor->watch(['tests/t1/*.less'], ['less']);