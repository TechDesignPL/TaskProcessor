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

$processor->task('js', function ($pipe) {
	/** @var \TechDesign\TaskProcessor\Pipe $pipe */
	$pipe
		->schedule(new Action\SrcAction(['tests/t2/*.js']))
		->schedule(new Action\ConcatAction('app.js'))
		->schedule(new Action\MinifyAction())
		->schedule(new Action\DestAction('tests/t2/out/'))
	;
});
$processor->task('default', ['less', 'js']);
$processor->run('default');
//$processor->run('js');