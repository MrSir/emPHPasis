<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 5/23/2017
 * Time: 8:18 AM
 */

namespace emPHPasis\Pipelines;

use emPHPasis\Pipelines\Pipes\Exception\Format as ExceptionFormat;
use emPHPasis\Pipelines\Pipes\Exception\Log as ExceptionLog;
use Closure;
use Exception;

/**
 * Class Pipeline
 * This class is heavily inspired by the Illuminate\Pipeline\Pipeline class part of the Laravel Framework
 * Credit goes to Tylor Otwell for the initial version.
 * @package emPHPasis\Pipelines
 */
abstract class Pipeline
{
    /**
     * The object being passed through the pipeline.
     * @var mixed
     */
    protected $passable;

    /**
     * The array of class pipes.
     * @var array
     */
    protected $pipes = [];

    /**
     * The method to call on each pipe.
     * @var string
     */
    protected $method = 'handle';

    /**
     * @var Closure
     */
    protected $burstClosure;

    /**
     * Pipeline constructor.
     */
    public function __construct()
    {
        // set the default burst closure
        $this->setBurstClosure(
            function (Exception $e) {
                return $this->burst($e);
            }
        );
    }

    /**
     * Set the object being sent through the pipeline.
     *
     * @param  mixed  $passable
     * @return $this
     */
    public function send($passable)
    {
        $this->passable = $passable;

        return $this;
    }

    /**
     * Set the array of pipes.
     *
     * @param  array $pipes
     * @return $this
     */
    public function through(array $pipes)
    {
        $this->pipes = $pipes;

        return $this;
    }

    /**
     * Overwriting the burst closure
     *
     * @param Closure $destination
     *
     * @return $this
     */
    public function onBurst(Closure $destination)
    {
        $this->setBurstClosure($destination);

        return $this;
    }

    /**
     * Wrap the then pipeline function with a burst handler
     *
     * @param Closure $destination
     *
     * @return array|mixed
     */
    public function then(Closure $destination)
    {
        try {
            $pipeline = array_reduce(
                array_reverse($this->pipes),
                $this->carry(),
                $this->prepareDestination($destination)
            );

            return $pipeline($this->passable);
        } catch (Exception $e) {
            return $this->getBurstClosure()($e);
        }
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (! is_object($pipe)) {
                    list($name, $parameters) = $this->parsePipeString($pipe);

                    // If the pipe is a string we will parse the string and resolve the class out
                    // of the dependency injection container. We can then build a callable and
                    // execute the pipe function giving in the parameters that are required.
                    $pipe = new $name();

                    $parameters = array_merge([$passable, $stack], $parameters);
                } else {
                    // If the pipe is already an object we'll just make a callable and pass it to
                    // the pipe as-is. There is no need to do any extra parsing and formatting
                    // since the object we're given was already a fully instantiated object.
                    $parameters = [$passable, $stack];
                }

                return $pipe->{$this->method}(...$parameters);
            };
        };
    }

    /**
     * Get the final piece of the Closure onion.
     *
     * @param  \Closure  $destination
     * @return \Closure
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    /**
     * This is the burst function, it handles the exceptions from the pipeline
     * This is the default function it can be overwritten by using onBurst();
     * Example:
     * $pipeline->send($passable)
     *      ->through(
     *          [
     *              Pipe1::class,
     *              Pipe2::class,
     *          ]
     *       )
     *      ->onBurst(
     *          function (Exception $e) {
     *              return [
     *                  'code' => 500,
     *                  'message' => 'overwritten burst'
     *              ];
     *          }
     *      )
     *      ->then(
     *          function ($passable) {
     *              return $passable;
     *          }
     *      );
     *
     * @param Exception $e
     *
     * @return array
     */
    public function burst(Exception $e)
    {
        return $this->send($e)
            ->through(
                [
                    ExceptionLog::class,
                    ExceptionFormat::class,
                ]
            )
            ->then(
                function ($response) {
                    return $response;
                }
            );
    }

    /**
     * @return mixed
     */
    public function getPassable()
    {
        return $this->passable;
    }

    /**
     * @param mixed $passable
     */
    public function setPassable($passable)
    {
        $this->passable = $passable;
    }

    /**
     * @return Closure
     */
    public function getBurstClosure()
    {
        return $this->burstClosure;
    }

    /**
     * @param Closure $burstClosure
     */
    public function setBurstClosure($burstClosure)
    {
        $this->burstClosure = $burstClosure;
    }

    /**
     * Parse full pipe string to get name and parameters.
     *
     * @param  string $pipe
     * @return array
     */
    protected function parsePipeString($pipe)
    {
        list($name, $parameters) = array_pad(explode(':', $pipe, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }
}
