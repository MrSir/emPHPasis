<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:24 PM
 */

namespace emPHPasis\Pipelines\Passables;

use stdClass;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Generate
 * @package emPHPasis\Pipelines\Passables
 */
class Generate extends Passable
{
    /** @var string DEFAULT_CONFIG_PATH */
    const DEFAULT_PATH = "./build/emPHPasis/";

    /** @var string DEFAULT_CONFIG_PATH */
    const DEFAULT_CONFIG_PATH = "./emPHPasis.json";

    /** @var string $path */
    protected $path = '';

    /** @var string $config */
    protected $config = '';

    /** @var stdClass $decodedConfig */
    protected $decodedConfig = null;

    /** @var OutputInterface $outputInterface */
    protected $outputInterface;

    /** @var bool $sourceReport */
    protected $sourceReport = false;

    /** @var bool $cloverReport */
    protected $cloverReport = false;

    /** @var bool $coverageXMLReport */
    protected $coverageXMLReport = false;

    /** @var bool $coverageCRAP4JReport */
    protected $coverageCRAP4JReport = false;

    /** @var bool $junitReport */
    protected $junitReport = false;

    /** @var bool $pmdReport */
    protected $pmdReport = false;

    /** @var bool $checkstyleReport */
    protected $checkstyleReport = false;

    /** @var bool $pmdCPDReport */
    protected $pmdCPDReport = false;

    /** @var bool $jdependXMLReport */
    protected $jdependXMLReport = false;

    /** @var bool $jdependChartSVG */
    protected $jdependChartSVG = false;

    /** @var bool $overviewPyramidSVG */
    protected $overviewPyramidSVG = false;

    /** @var bool $phplocReport */
    protected $phplocReport = false;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getConfig(): string
    {
        return $this->config;
    }

    /**
     * @param string $config
     */
    public function setConfig(string $config): void
    {
        $this->config = $config;
    }

    /**
     * @return stdClass
     */
    public function getDecodedConfig(): stdClass
    {
        return $this->decodedConfig;
    }

    /**
     * @param stdClass $decodedConfig
     */
    public function setDecodedConfig(stdClass $decodedConfig): void
    {
        $this->decodedConfig = $decodedConfig;
    }

    /**
     * @return OutputInterface
     */
    public function getOutputInterface(): OutputInterface
    {
        return $this->outputInterface;
    }

    /**
     * @param OutputInterface $outputInterface
     */
    public function setOutputInterface(OutputInterface $outputInterface): void
    {
        $this->outputInterface = $outputInterface;
    }

    /**
     * @return bool
     */
    public function hasSourceReport(): bool
    {
        return $this->sourceReport;
    }

    /**
     * @param bool $sourceReport
     */
    public function setSourceReport(bool $sourceReport): void
    {
        $this->sourceReport = $sourceReport;
    }

    /**
     * @return bool
     */
    public function hasCloverReport(): bool
    {
        return $this->cloverReport;
    }

    /**
     * @param bool $cloverReport
     */
    public function setCloverReport(bool $cloverReport): void
    {
        $this->cloverReport = $cloverReport;
    }

    /**
     * @return bool
     */
    public function hasCoverageXMLReport(): bool
    {
        return $this->coverageXMLReport;
    }

    /**
     * @param bool $coverageXMLReport
     */
    public function setCoverageXMLReport(bool $coverageXMLReport): void
    {
        $this->coverageXMLReport = $coverageXMLReport;
    }

    /**
     * @return bool
     */
    public function hasCoverageCRAP4JReport(): bool
    {
        return $this->coverageCRAP4JReport;
    }

    /**
     * @param bool $coverageCRAP4JReport
     */
    public function setCoverageCRAP4JReport(bool $coverageCRAP4JReport): void
    {
        $this->coverageCRAP4JReport = $coverageCRAP4JReport;
    }

    /**
     * @return bool
     */
    public function hasJunitReport(): bool
    {
        return $this->junitReport;
    }

    /**
     * @param bool $junitReport
     */
    public function setJunitReport(bool $junitReport): void
    {
        $this->junitReport = $junitReport;
    }

    /**
     * @return bool
     */
    public function hasPmdReport(): bool
    {
        return $this->pmdReport;
    }

    /**
     * @param bool $pmdReport
     */
    public function setPmdReport(bool $pmdReport): void
    {
        $this->pmdReport = $pmdReport;
    }

    /**
     * @return bool
     */
    public function hasCheckstyleReport(): bool
    {
        return $this->checkstyleReport;
    }

    /**
     * @param bool $checkstyleReport
     */
    public function setCheckstyleReport(bool $checkstyleReport): void
    {
        $this->checkstyleReport = $checkstyleReport;
    }

    /**
     * @return bool
     */
    public function hasPmdCPDReport(): bool
    {
        return $this->pmdCPDReport;
    }

    /**
     * @param bool $pmdCPDReport
     */
    public function setPmdCPDReport(bool $pmdCPDReport): void
    {
        $this->pmdCPDReport = $pmdCPDReport;
    }

    /**
     * @return bool
     */
    public function hasJdependXMLReport(): bool
    {
        return $this->jdependXMLReport;
    }

    /**
     * @param bool $jdependXMLReport
     */
    public function setJdependXMLReport(bool $jdependXMLReport): void
    {
        $this->jdependXMLReport = $jdependXMLReport;
    }

    /**
     * @return bool
     */
    public function hasJdependChartSVG(): bool
    {
        return $this->jdependChartSVG;
    }

    /**
     * @param bool $jdependChartSVG
     */
    public function setJdependChartSVG(bool $jdependChartSVG): void
    {
        $this->jdependChartSVG = $jdependChartSVG;
    }

    /**
     * @return bool
     */
    public function hasOverviewPyramidSVG(): bool
    {
        return $this->overviewPyramidSVG;
    }

    /**
     * @param bool $overviewPyramidSVG
     */
    public function setOverviewPyramidSVG(bool $overviewPyramidSVG): void
    {
        $this->overviewPyramidSVG = $overviewPyramidSVG;
    }

    /**
     * @return bool
     */
    public function hasPhplocReport(): bool
    {
        return $this->phplocReport;
    }

    /**
     * @param bool $phplocReport
     */
    public function setPhplocReport(bool $phplocReport): void
    {
        $this->phplocReport = $phplocReport;
    }
}
