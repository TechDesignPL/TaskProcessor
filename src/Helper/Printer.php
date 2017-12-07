<?php

namespace TechDesign\TaskProcessor\Helper;

class Printer
{

	const FG_BLACK = '0;30';
	const FG_DARK_GRAY = '1;30';
	const FG_BLUE = '0;34';
	const FG_LIGHT_BLUE = '1;34';
	const FG_GREEN = '0;32';
	const FG_LIGHT_GREEN = '1;32';
	const FG_CYAN = '0;36';
	const FG_LIGHT_CYAN = '1;36';
	const FG_RED = '0;31';
	const FG_LIGHT_RED = '1;31';
	const FG_PURPLE = '0;35';
	const FG_LIGHT_PURPLE = '1;35';
	const FG_BROWN = '0;33';
	const FG_YELLOW = '1;33';
	const FG_LIGHT_GRAY = '0;37';
	const FG_WHITE = '1;37';

	const BG_BLACK      = '40';
	const BG_RED        = '41';
	const BG_GREEN      = '42';
	const BG_YELLOW     = '43';
	const BG_BLUE       = '44';
	const BG_MAGENTA    = '45';
	const BG_CYAN       = '46';
	const BG_LIGHT_GRAY = '47';


	public static function prnt($message, $fgColor = null, $bgColor = null)
	{
		$timestamp = self::getColoredString(date('H:i:s'), self::FG_LIGHT_BLUE);
		vprintf("[%s]: %s\n", [$timestamp, self::getColoredString($message, $fgColor, $bgColor)]);
	}

	public static function getColoredString($string, $fgColor = null, $bgColor = null)
	{
		$colored_string = "";
		// Check if given foreground color found
		if ($fgColor) {
			$colored_string .= "\033[" . $fgColor . "m";
		}

		// Check if given background color found
		if ($bgColor) {
			$colored_string .= "\033[" . $bgColor . "m";
		}

		// Add string and end coloring
		$colored_string .= $string . "\033[0m";
		return $colored_string;
	}
}