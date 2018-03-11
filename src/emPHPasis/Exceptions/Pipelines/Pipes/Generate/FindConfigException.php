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
 * Class FindConfigException
 * @package emPHPasis\Exceptions\Pipelines\Pipes\Generate
 */
class FindConfigException extends Exception
{
    /**
     * Update constructor.
     *
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            'Finding config failed.',
            500,
            $previous
        );
    }
}
