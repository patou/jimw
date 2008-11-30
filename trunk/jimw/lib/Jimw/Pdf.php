<?php
class Jimw_Pdf
{
    protected $_name = 'article';
    protected $_header = null;
    protected $_margin_header = 5;
    protected $_margin_footer = 10;
    protected $_margin_top = 27;
    protected $_margin_bottom = 25;
    protected $_margin_left = 15;
    protected $_margin_right = 15;
    // Scale ratio for images [number of points in user unit]
    protected $_image_scale = 4;
    public function __construct ()
    {
        /*
		 	* Setup external configuration options
		 	*/
        define('K_TCPDF_EXTERNAL_CONFIG', true);
        /**
         * define default PDF document producer
         */
        //define('PDF_PRODUCER','TCPDF 1.53.0.TC034_PHP4 (http://tcpdf.sourceforge.net) JIMW (http://www.jimw.fr)');
        /**
         * installation path
         */
        define("K_PATH_MAIN", JIMW_REP_LIB . 'tcpdf/');
        /**
         * url path
         */
        define("K_PATH_URL", "http://localhost/_OPENSOURCE/tcpdf/");
        /**
         * path for PDF fonts
         */
        define("FPDF_FONTPATH", K_PATH_MAIN . 'fonts/');
        /**
         * cache directory for temporary files (full path)
         */
        define("K_PATH_CACHE", JIMW_ROOT . "cache/");
        /**
         * cache directory for temporary files (url path)
         */
        define("K_PATH_URL_CACHE", JIMW_ROOT . "cache/");
        /**
         *images directory
         */
        define("K_PATH_IMAGES", JIMW_ROOT . "public/");
        /**
         * blank image
         */
        define("K_BLANK_IMAGE", K_PATH_IMAGES . "_blank.png");
        /**
         * page format
         */
        define("PDF_PAGE_FORMAT", "A4");
        /**
         * page orientation (P=portrait, L=landscape)
         */
        define("PDF_PAGE_ORIENTATION", "P");
        /**
         * document creator
         */
        define("PDF_CREATOR", "JIMW");
        /**
         * document author
         */
        define("PDF_AUTHOR", "JIMW");
        /**
         * header title
         */
        define("PDF_HEADER_TITLE", "header title");
        /**
         * header description string
         */
        define("PDF_HEADER_STRING", "first row\nsecond row\nthird row");
        /**
         * image logo
         */
        define("PDF_HEADER_LOGO", "logo_example.png");
        /**
         * header logo image width [mm]
         */
        define("PDF_HEADER_LOGO_WIDTH", 20);
        /**
         *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
         */
        define("PDF_UNIT", "mm");
        /**
         * header margin
         */
        define("PDF_MARGIN_HEADER", 5);
        /**
         * footer margin
         */
        define("PDF_MARGIN_FOOTER", 10);
        /**
         * top margin
         */
        define("PDF_MARGIN_TOP", 27);
        /**
         * bottom margin
         */
        define("PDF_MARGIN_BOTTOM", 25);
        /**
         * left margin
         */
        define("PDF_MARGIN_LEFT", 15);
        /**
         * right margin
         */
        define("PDF_MARGIN_RIGHT", 15);
        /**
         * main font name
         */
        define("PDF_FONT_NAME_MAIN", "freeserif"); //vera
        /**
         * main font size
         */
        define("PDF_FONT_SIZE_MAIN", 10);
        /**
         * data font name
         */
        define("PDF_FONT_NAME_DATA", "freeserif"); //verase
        /**
         * data font size
         */
        define("PDF_FONT_SIZE_DATA", 8);
        /**
         *  scale factor for images (number of points in user unit)
         */
        define("PDF_IMAGE_SCALE_RATIO", 4);
        /**
         * magnification factor for titles
         */
        define("HEAD_MAGNIFICATION", 1.1);
        /**
         * height of cell repect font height
         */
        define("K_CELL_HEIGHT_RATIO", 1.25);
        /**
         * title magnification respect main font size
         */
        define("K_TITLE_MAGNIFICATION", 1.3);
        /**
         * reduction factor for small font
         */
        define("K_SMALL_RATIO", 2 / 3);
        include ('tcpdf/tcpdf.php');
        // Default settings are a portrait layout with an A4 configuration using millimeters as units
        $this->pdf = new TCPDF();
    }
    public function render ($alias, $title, $header, $html)
    {
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
        return $data;
    }
}