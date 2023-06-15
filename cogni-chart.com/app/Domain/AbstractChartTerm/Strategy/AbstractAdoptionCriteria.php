<?php

namespace App\Domain\AbstractChartTerm\Strategy;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Domain\AbstractChartTerm\AbstractChartTerm;

abstract class AbstractAdoptionCriteria
{

    abstract public function judge(AbstractChartTermRepositoryInterface $abstractChartTermRepository, AbstractChartTerm $abstractChartTerm);

/*
    private $strategyName;
    private $lastExecutionFileName = "lastExecution.log";

    public function __construct(string $strategyName)
    {
        $this->strategyName = $strategyName;
    }

    public function isDifferent(array $chartEntry)
    {
        $directory = DirectoryHandler::get("chartEntryFile");
        $contents = $directory->getFileContents($this->lastExecutionFileName, $this->strategyName);
        if (empty($contents)) {
            return true;
        }
        $lastExecution = unserialize($contents);
        if (empty($lastExecution)) {
            return true;
        }
        foreach ($chartEntry AS $key => $row) {
            if (empty($lastExecution[$key])) {
                return true;
            }
            $lastRow = $lastExecution[$key];
            if ($row->ranking !== $lastRow->ranking || $row->chart_artist !== $lastRow->chart_artist || $row->chart_music !== $lastRow->chart_music) {
                return true;
            } else {
                unset($lastExecution[$key]);
            }
        }
        if (!empty($lastExecution)) {
            return true;
        }
        return false;
    }

    public function saveForNext(array $chartEntry)
    {
        $contents = serialize($chartEntry);
        $directory = DirectoryHandler::get("chartEntryFile");
        if (!$directory->isDirectoryExist($this->strategyName)) {
            $directory->createDirectory($this->strategyName);
        }
        return $directory->saveFile($this->lastExecutionFileName, $contents, $this->strategyName);
    }
 */

}
