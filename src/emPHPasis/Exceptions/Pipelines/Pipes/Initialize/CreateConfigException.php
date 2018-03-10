<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 5/23/2017
 * Time: 2:31 PM
 */

namespace emPHPasis\Exceptions\Pipelines\Pipes\Initialize;

use Exception;
use Throwable;

/**
 * Class CreateConfigException
 * @package emPHPasis\Exceptions\Pipelines\Pipes\Initialize
 */
class CreateConfigException extends Exception
{
    /**
     * Update constructor.
     *
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            'Creating config failed.',
            500,
            $previous
        );
    }
}
