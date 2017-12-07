<?php

namespace TechDesign\TaskProcessor;

use TechDesign\TaskProcessor\Helper\Printer;

class FileWatcher
{
	protected $filesToWatch = [];
	protected $dataToPass;
	protected $callback;
	protected $interval;

	public function __construct($filesToWatch, $callback, $dataToPass, $interval = 1000)
	{
		foreach ($filesToWatch as $file) {
			$this->filesToWatch[] = [
				'path'  => $file,
				'mtime' => filemtime($file)
			];
		}

		$this->dataToPass = $dataToPass;
		$this->callback = $callback;
		$this->interval = $interval;
	}

	public function watch()
	{
		while (true) {
			if ($this->checkUpdates()) {
				Printer::prnt('Change detected!');
				call_user_func($this->callback, $this->dataToPass);
			}

			clearstatcache();
			usleep($this->interval * 1000);
		}
	}

	public function checkUpdates()
	{
		$changeDetected = false;

		foreach ($this->filesToWatch as &$file) {
			$mtime = filemtime($file['path']);
			if ($mtime > $file['mtime']) {
				$file['mtime'] = $mtime;
				$changeDetected = true;
			}
		}

		return $changeDetected;
	}
}