<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 5/24/2017
 * Time: 10:05 AM
 */

namespace emPHPasis\Pipelines\Pipes;

use Exception;

/**
 * Class Pipe
 * @package emPHPasis\Pipelines\Pipes
 */
abstract class Pipe
{
    /**
     * @var string
     */
    protected $exceptionType = Exception::class;

    /**
     * Pipe constructor.
     *
     * @param $exceptionType
     */
    public function __construct($exceptionType)
    {
        $this->setExceptionType($exceptionType);
    }

    /**
     * @return string
     */
    public function getExceptionType()
    {
        return $this->exceptionType;
    }

    /**
     * @param string $exceptionType
     */
    public function setExceptionType($exceptionType)
    {
        $this->exceptionType = $exceptionType;
    }
}
