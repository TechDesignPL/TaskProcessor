<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;
use TechDesign\TaskProcessor\Helper\ShellHelper;

class ImageOptimizeAction extends Action
{
	const JPEGOPTIM_PATH = __DIR__ . '/../bin/{os}/jpegoptim';
	const PNGQUANT_PATH = __DIR__ . '/../bin/{os}/pngquant';
	const GIFSICLE_PATH = __DIR__ . '/../bin/{os}/gifsicle';

	public function run($input)
	{
		foreach ((array)$input as $file) {
			if (!$file instanceof FileInput) {
				$file = FileInput::fromPath($file, true);
			}

			//reading extension is faster than fetching while mime type
			$ext = strtolower($file->getExtension());

			switch ($ext) {
				case 'jpg':
				case 'jpeg':
					ShellHelper::execShell(self::JPEGOPTIM_PATH, '--strip-all', '--all-progressive', $file->fullPath);
					break;
				case 'gif':
					ShellHelper::execShell(self::GIFSICLE_PATH, '-O3', $file->fullPath, '-o', $file->fullPath);
					break;
				case 'png':
					ShellHelper::execShell(self::PNGQUANT_PATH, '--quality 65-80 --skip-if-larger -f --ext .png', $file->fullPath);
					break;
			}
		}

		return $input;
	}
}