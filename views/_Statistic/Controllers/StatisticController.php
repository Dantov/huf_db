<?php
namespace Views\_Statistic\Controllers;
use Views\_Statistic\Models\Statistic;
use Views\_Globals\Controllers\GeneralController;


class StatisticController extends GeneralController
{

    /**
     * @throws \Exception
     */
    public function action()
    {
        $stat = new Statistic();

        $thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
            $_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
        }

        $users = $stat->getUsers();
        /*
        $models = $stat->getModels();
        $likes = $stat->getLikedModels();
        $modelsBy3Dmodellers = $stat->getModelsBy3Dmodellers();
        $modelsByAuthors = $stat->getModelsByAuthors();
        */

        $compact = compact([
            'users','models','likes','modelsBy3Dmodellers',
            'modelsByAuthors'
        ]);

        $this->render('statistic', $compact);
    }

}