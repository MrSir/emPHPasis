<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:16 PM
 */

namespace emPHPasis\Interfaces;

/**
 * Interface Passable
 * @package emPHPasis\Interfaces
 */
interface Passable
{
    /**
     * @return int
     */
    public function getCode() : int;

    /**
     * @param int $code
     */
    public function setCode(int $code) : void;

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param $result
     */
    public function setResult($result) : void;
}
