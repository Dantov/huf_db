<?php
/**
 * Created by PhpStorm.
 * User: Dant
 * Date: 12.01.2020
 * Time: 16:24
 */
session_start();
require_once( _vendorDIR_.'TCPDF/tcpdf.php');
class HufDB_PDF extends TCPDF
{
    //Page header
    public function Header()
    {
        $date = date_create( date('Y-m-d') )->Format('d.m.Y');
        if ( empty($_SESSION['foundRow']) )
        {
            $coll_name = 'Коллекция: '.$_SESSION['assist']['collectionName'].'_'.$date;
        } elseif ( !empty($_SESSION['searchFor']) ) {
            $coll_name = 'Найдено: '. $_SESSION['searchFor'] . ' - ' .$date;
        } else {
            $coll_name = 'Выделенное - '.$date;
        }

        // Set font
        $this->SetFont('dejavusans', '', 12);
        $this->SetTextColor( 167,167,167 ); // серый
        $this->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        // Title
        $this->Cell(0, 10, $coll_name, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
    }

    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-12);
        // Set font
        $this->SetFont('dejavusans', 'I', 9);
        // Page number
        $this->Cell(308, 10, ''.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}