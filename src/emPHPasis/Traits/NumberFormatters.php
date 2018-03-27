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
     * @param int $precision
     * @param bool $percentage
     *
     * @return float|string
     */
    private function formatRatio($numerator, $denominator,int $precision = 2, $percentage = false)
    {
        if ($denominator === 0) {
            return 0.00;
        }

        $ratio = $numerator / $denominator;

        if ($percentage) {
            $ratio *= 100;
        }

        return number_format($ratio, $precision);
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

        $hours = number_format((($minutes - ($minutes % 60)) / 60), 0);

        return $hours . ' hr ' . ($minutes % 60) . ' min';
    }

    /**
     * This function compute the ratio versus the high range and normalizes it to 100%
     *
     * @param float $value
     * @param int   $highRange
     *
     * @return string
     */
    private function normalize(float $value, int $highRange)
    {
        $result = $this->formatRatio($value, $highRange,4);

        if ($result > 1) {
            $result = 1;
        }

        return number_format($result, 4);
    }

    /**
     * This function computes the index for a given page
     *
     * @param array $values
     *
     * @return float|string
     */
    private function computeIndex(array $values)
    {
        $sum = 0;
        $count = count($values);

        foreach ($values as $value) {
            $sum += (float)$value;
        }

        return $this->formatRatio($sum, $count, 2, true);
    }
}
