<?php
class Jimw_Pdf
{
    public function __construct ()
    {
		require_once("dompdf/dompdf_config.inc.php");


		$this->pdf = new DOMPDF();
 
 }
    public function render ($alias, $title, $header, $html)
    {

		$this->pdf->load_html($html);
		$this->pdf->render();
		$this->pdf->stream($alias . ".pdf");
/*
		//set margins
        $this->pdf->SetMargins($this->_margin_left, $this->_margin_top, $this->_margin_right);
        //set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, $this->_margin_bottom);
        $this->pdf->SetHeaderMargin($this->_margin_header);
        $this->pdf->SetFooterMargin($this->_margin_footer);
        $this->pdf->setImageScale($this->_image_scale);
        $this->pdf->SetCreator('JIMW');
        $this->pdf->SetTitle($title);
        //$this->pdf->header_title = $title;
        //$this->pdf->SetSubject($this->getDescription());
        //$this->pdf->SetKeywords($this->getMetaData('keywords'));
        // Set PDF Header data
        $this->pdf->setHeaderData('', 0, $title, $header);
        $this->pdf->setHeaderFont(array('vera' , '' , 10));
        $this->pdf->setFooterFont(array('vera' , '' , 8));
        // Initialize PDF Document
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        // Build the PDF Document string from the document buffer
        $this->pdf->WriteHTML($html, true);
        $data = $this->pdf->Output('', 'S');
        //Close and output PDF document
        return $data;*/
    }
}