<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 5/23/2017
 * Time: 9:42 AM
 */

namespace emPHPasis\Pipelines\Passables;

use emPHPasis\Interfaces;

/**
 * Class Passable
 * @package emPHPasis\Pipelines\Passables
 */
abstract class Passable implements Interfaces\Passable
{
    /** @var int SUCCESS_CODE */
    const SUCCESS_CODE = 200;

    /** @var int EXCEPTION_CODE */
    const EXCEPTION_CODE = 500;

    /** @var int $code */
    protected $code;

    /** @var $result */
    protected $result;

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }
}
