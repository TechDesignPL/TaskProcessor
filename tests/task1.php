<?php

include_once __DIR__ . '/../vendor/autoload.php';

use TechDesign\TaskProcessor\Processor;
use TechDesign\TaskProcessor\Action;

$processor = new Processor();

$processor->task('less', function ($pipe) {
	/** @var \TechDesign\TaskProcessor\Pipe $pipe */
	$pipe
		->schedule(new Action\SrcAction(['tests/t1/app.less']))
		->schedule(new Action\LessAction())
		->schedule(new Action\DestAction('tests/t1/'))
	;
});

$processor->run('less');