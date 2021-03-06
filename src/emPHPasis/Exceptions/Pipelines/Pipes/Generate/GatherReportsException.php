<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 5/23/2017
 * Time: 2:31 PM
 */

namespace emPHPasis\Exceptions\Pipelines\Pipes\Generate;

use Exception;
use Throwable;

/**
 * Class GatherReportsException
 * @package emPHPasis\Exceptions\Pipelines\Pipes\Generate
 */
class GatherReportsException extends Exception
{
    /**
     * Update constructor.
     *
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            'Gathering reports failed.',
            500,
            $previous
        );
    }
}
