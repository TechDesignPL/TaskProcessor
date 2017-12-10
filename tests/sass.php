<?php
/**
 * Multiple file watchers
 */
use TechDesign\TaskProcessor\Action\RenameAction;

/** @var \TechDesign\TaskProcessor\Processor $processor */

$processor->task('sass', function ($task) {
	/** @var \TechDesign\TaskProcessor\Task $task */
	$task
		->src(['tests/t3/scss/bootstrap.scss'])
		->sass()
		->rename(RenameAction::TYPE_EXTENSION, 'css')
		->print()
		->dest('tests/t3/');
});

$processor->run(['sass']);