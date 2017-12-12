<?php

class ProgressBar
{

    public static function progress($done, $total, $size = 30)
    {

        static $startTime;

        // if we go over our bound, just ignore it
        if ($done > $total) return;

        if (empty($startTime)) {
            $startTime = time();
        }

        $now = time();
        $perc = (double)($done / $total);
        $bar = floor($perc * $size);

        $progressBar = "\r[";
        $progressBar .= str_repeat("=", $bar);
        if ($bar < $size) {
            $progressBar .= ">";
            $progressBar .= str_repeat(" ", $size - $bar);
        } else {
            $progressBar .= "=";
        }

        $disp = number_format($perc * 100, 0);

        $progressBar .= "] $disp%  $done/$total";

        $rate = ($now - $startTime) / $done;
        $left = $total - $done;
        $eta = round($rate * $left, 2);

        $elapsed = $now - $startTime;

        $progressBar .= " remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

        echo "$progressBar  ";

        flush();

        // when done, send a newline
        if ($done == $total) {
            echo "\n";
        }

    }

}