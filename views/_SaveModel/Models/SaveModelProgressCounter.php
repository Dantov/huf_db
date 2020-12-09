<?php
/**
 * Date: 03.12.2020
 * Time: 17:47
 */

namespace Views\_SaveModel\Models;
use Views\_Globals\Models\ProgressCounter;

class SaveModelProgressCounter extends ProgressCounter
{

    /**
     * @var int
     * текущее ко-во прогресса
     */
    public $currentProgresses = 0;

    /**
     * @var int
     * общее кол-во процессов
     */
    public $overallProcesses = 0;

    public function __construct( string $userName, string $tabID, int $overallProcesses = 0 )
    {
        $this->overallProcesses = $this->overallProcesses ? $overallProcesses : 0;
        parent::__construct();

        $this->setProgress($userName, $tabID);
    }

    public function count()
    {
        if ( $this->currentProgresses >= $this->overallProcesses )
        {
            $this->progressCount(100);
            return;
        }

        $newPercent = ceil( ( ++$this->currentProgresses * 100 ) / $this->overallProcesses );

        $this->progressCount($newPercent);
    }
}