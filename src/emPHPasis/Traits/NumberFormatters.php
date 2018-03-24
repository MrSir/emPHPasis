<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/23/2018
 * Time: 11:48 PM
 */

namespace emPHPasis\Traits;

trait NumberFormatters
{
    /**
     * This function computes a ratio and formats it to 2 decimals
     *
     * @param      $numerator
     * @param      $denominator
     * @param bool $percentage
     *
     * @return float|string
     */
    private function formatRatio($numerator, $denominator, $percentage = false)
    {
        if ($denominator === 0) {
            return 0.00;
        }

        $ratio = $numerator / $denominator;

        if ($percentage) {
            $ratio *= 100;
        }

        return number_format($ratio, 2);
    }

    /**
     * This function coverts seconds into an hours minutes string
     *
     * @param $seconds
     *
     * @return string
     */
    private function convertSecondsToHours($seconds): string
    {
        $minutes = $seconds / 60;

        $hours = number_format((($minutes - ($minutes % 60)) / 60),0);

        return $hours . ' hours ' . ($minutes % 60) . ' minutes';
    }
}
