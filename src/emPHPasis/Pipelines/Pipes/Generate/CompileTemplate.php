<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Generate;

use Carbon\Carbon;
use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Generate\CompileTemplateException;
use emPHPasis\Pipelines\Passables\Generate;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use Pug\Pug;
use Throwable;

/**
 * Class CompileTemplate
 * @package emPHPasis\Pipelines\Pipes\Generate
 */
class CompileTemplate extends Pipe
{
    /**
     * CompileTemplate constructor.
     */
    public function __construct()
    {
        parent::__construct(CompileTemplateException::class);
    }

    /**
     * @param Generate $passable
     * @param Closure  $next
     *
     * @return mixed
     * @throws Exception
     */
    public function handle(Generate &$passable, Closure $next)
    {
        try {
            // grab the parameters from the passable
            $code = $passable->getCode();
            $result = $passable->getResult();

            // skip the pipe if the previous has failed
            if ($code == $passable::SUCCESS_CODE) {
                $decodedConfig = $passable->getDecodedConfig();

                // set the successful code and result
                $code = $passable::EXCEPTION_CODE;
                $result = ['errors' => ['No reports template found.']];

                $reportPath = $decodedConfig->configurations->report_path;
                $templatePath = $decodedConfig->configurations->template_path;

                // delete report directory if it exists
                if (file_exists($reportPath)) {
                    $this->rmdirRecursive($reportPath);
                }

                // create report directory
                mkdir($reportPath);

                // check if the template exists
                if (file_exists($templatePath)) {
                    // copy css
                    shell_exec('cp -R '. $templatePath . 'css ' . $reportPath . 'css');

                    // instantiate pug engine
                    $pugEngine = new Pug();

                    // compile index file
                    $this->compileIndex($pugEngine, $templatePath, $reportPath);

                    // compile maintainability file
                    //$this->compileIndex($pugEngine, $templatePath, $reportPath);

                    // compile testability file
                    $this->compileTestability($pugEngine, $templatePath, $reportPath);

                    // compile complexity file
                    //$this->compileIndex($pugEngine, $templatePath, $reportPath);

                    // compile scalability file
                    //$this->compileIndex($pugEngine, $templatePath, $reportPath);

                    $passable->getOutputInterface()
                        ->writeln(Carbon::now() . ' Compiled template: ' . $templatePath);

                    // set the successful code and result
                    $code = $passable::SUCCESS_CODE;
                    $result = ['message' => 'Success'];
                }
            }

            // set the code and result
            $passable->setCode($code);
            $passable->setResult($result);
        } catch (Throwable $e) {
            $exceptionType = $this->getExceptionType();

            throw new $exceptionType($e);
        }

        return $next($passable);
    }

    /**
     * @param $dir
     */
    public function rmdirRecursive($dir)
    {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            if (is_dir("$dir/$file")) {
                $this->rmdirRecursive("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }

        rmdir($dir);
    }

    /**
     * This is a wrapper function to aid in testing the pipe.
     * It writes the config contents to a file
     *
     * @param string $file
     * @param string $data
     *
     * @return bool|int
     */
    public function writeFile(string $file, string $data)
    {
        return file_put_contents(
            $file,
            $data
        );
    }

    /**
     * @param Pug    $pugEngine
     * @param string $templatePath
     * @param string $reportPath
     *
     * @throws Exception
     */
    public function compileIndex(Pug $pugEngine, string $templatePath, string $reportPath): void
    {
        $indexHtml = $pugEngine->renderFile(
            $templatePath . 'index.pug',
            [
                "route" => "index",
                "title" => "Dashboard",
                "subject" => "Summary of all the analysis."
            ]
        );
        $this->writeFile($reportPath . 'index.html', $indexHtml);
    }

    /**
     * @param Pug    $pugEngine
     * @param string $templatePath
     * @param string $reportPath
     *
     * @throws Exception
     */
    public function compileTestability(Pug $pugEngine, string $templatePath, string $reportPath): void
    {
        $indexHtml = $pugEngine->renderFile(
            $templatePath . 'testability.pug',
            [
                "route" => "testability",
                "title" => "Testability",
                "subject" => "Code coverage and test analysis."
            ]
        );
        $this->writeFile($reportPath . 'testability.html', $indexHtml);
    }
}
