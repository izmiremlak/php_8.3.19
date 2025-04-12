<?php
if (!defined("SERVER_HOST")) {
    die();
}
/*
 * @Author: izmirtr.com
 * @Created Project Date: 2015-08-22 23:35
 * @License here: http://www.izmirtr.com/izmet-sozlesmesi-ve-uye-kaydi.html
 */

/* Basit Örnek 
$pagenumber = 57;
$totalrecords = 45533;

$pg = new bootPagination();
$pg->pagenumber = $pagenumber;
$pg->pagesize = $pagesize;
$pg->totalrecords = $totalrecords;
$pg->showfirst = true;
$pg->showlast = true;
$pg->paginationcss = "pagination-large";
$pg->paginationstyle = 1; // 1: gelişmiş, 0: normal
$pg->defaultUrl = "index.php";
$pg->paginationUrl = "index.php?p=[p];
echo $apagination->process();

*/    

class nereu_bootPagination
{
    public $pagenumber;
    public $pagesize;
    public $totalrecords;
    public $showfirst;
    public $showlast;
    public $paginationcss;
    public $paginationstyle;
    public $defaultUrl;
    public $paginationUrl;
	
    // Sayfalama stili
    public $prevCss;
    public $nextCss;
	
    function __construct()
    {
        $this->pagenumber = 1;
        $this->pagesize = 20;
        $this->totalrecords = 0;
        $this->showfirst = true;
        $this->showlast = true;
        $this->paginationcss = "pagination-small";
        $this->paginationstyle = 0;  // 1: gelişmiş, 0: normal, 2: sayfa gösterici
        $this->defaultUrl = "#"; // Ajax sayfalama durumunda
        $this->paginationUrl = "#"; // Ajax sayfalama durumunda örneğin index.php?p=[p]
        $this->prevCss = "previous";
        $this->nextCss = "next";
    }
	
    function process()
    {		
        $paginationlst = "";
        $firstbound = 0;
        $lastbound = 0;
        $tooltip = "";
		
        if ($this->totalrecords > $this->pagesize) {
            $totalpages = ceil($this->totalrecords / $this->pagesize);

            if ($this->pagenumber > 1) {
                if ($this->showfirst && $this->paginationstyle != 2) {
                    $firstbound = 1;
                    $lastbound = $firstbound + $this->pagesize - 1;
                    $tooltip = "Toplam " . $this->totalrecords . " kaydın " . $firstbound . " - " . $lastbound . " arası gösteriliyor";
                    // İlk Sayfa Bağlantısı
                    if ($this->defaultUrl == "") {
                        $this->defaultUrl = "#";
                    }
                    $paginationlst .= "<li><a id=\"p_1\" href=\"" . $this->defaultUrl . "\" class=\"pagination-css\" data-toggle=\"tooltip\" title=\"" . $tooltip . "\"><i class=\"glyphicon glyphicon-fast-backward\"></i></a></li>";
                }
                $firstbound = (($totalpages - 1) * $this->pagesize);
                $lastbound = $firstbound + $this->pagesize - 1;
                if ($lastbound > $this->totalrecords) {
                    $lastbound = $this->totalrecords;
                }
                $tooltip = "Toplam " . $this->totalrecords . " kaydın " . $firstbound . " - " . $lastbound . " arası gösteriliyor";
                // Önceki Sayfa Bağlantısı
                if ($this->paginationUrl == "") {
                    $this->paginationUrl = "#";
                }
				
                $pid = ($this->pagenumber - 1);
                if ($pid < 1) {
                    $pid = 1;
                }
                $prevPageCss = "";
                $prevIcon = "<i class=\"glyphicon glyphicon-chevron-left\"></i>";
                if ($this->paginationstyle == 2) {
                    if ($this->prevCss != "") {
                        $prevPageCss = " class=\"" . $this->prevCss . "\"";
                    }
                    $prevIcon = "&larr; Önceki";
                }
                $paginationlst .= "<li" . $prevPageCss . "><a id=\"pp_" . $pid . "\" href=\"" . $this->prepareUrl($pid) . "\" data-toggle=\"tooltip\" class=\"pagination-css\" title=\"" . $tooltip . "\">" . $prevIcon . "</a></li>";
                // Normal Bağlantılar
                if ($this->paginationstyle != 2) {
                    $paginationlst .= $this->generate_pagination_links($totalpages, $this->totalrecords, $this->pagenumber, $this->pagesize);
                }
				
                if ($this->pagenumber < $totalpages) {
                    $paginationlst .= $this->generate_previous_last_links($totalpages, $this->totalrecords, $this->pagenumber, $this->pagesize, $this->showlast);
                }
            } else {
                // Normal Bağlantılar
                if ($this->paginationstyle != 2) {
                    $paginationlst .= $this->generate_pagination_links($totalpages, $this->totalrecords, $this->pagenumber, $this->pagesize);
                }
                // Sonraki ve Son Sayfa Bağlantıları
                $paginationlst .= $this->generate_previous_last_links($totalpages, $this->totalrecords, $this->pagenumber, $this->pagesize, $this->showlast);
            }
        }
        $paginationCss = "pagination " . $this->paginationcss;
        if ($this->paginationstyle == 2) {
            $paginationCss = "pager";
        }
        return "<ul class=\"" . $paginationCss . "\">\n" . $paginationlst . "</ul>\n";
    }
	
    function generate_pagination_links($totalpages, $totalrecords, $pagenumber, $pagesize)
    {
        $script = "";
        $firstbound = 0;
        $lastbound = 0;
        $tooltip = "";

        $lst = new pagination();
        if ($this->paginationstyle == 1) {
            $arr = $lst->advance_pagination_links($totalpages, $pagenumber);
        } else {
            $arr = $lst->simple_pagination_links($totalpages, 15, $pagenumber);
        }
        if (count($arr) > 0) {
            foreach ($arr as $item) {
                $firstbound = (($item - 1) * $pagesize) + 1;
                $lastbound = $firstbound + $pagesize - 1;
                if ($lastbound > $totalrecords) {
                    $lastbound = $totalrecords;
                }
                $tooltip = "Toplam " . $totalrecords . " kaydın " . $firstbound . " - " . $lastbound . " arası gösteriliyor";
                $css = "";
                if ($item == $pagenumber) {
                    $css = " class=\"active\"";
                }
                $script .= "<li" . $css . "><a id=\"pg_" . $item . "\" href=\"" . $this->prepareUrl($item) . "\" class=\"pagination-css\" data-toggle=\"tooltip\" title=\"" . $tooltip . "\">" . $item . "</a></li>";
            }
        }
        return $script;
    }
	
    function generate_previous_last_links($totalpages, $totalrecords, $pagenumber, $pagesize, $showlast)
    {
        $script = "";
        $firstbound = (($pagenumber) * $pagesize) + 1;
        $lastbound = $firstbound + $pagesize - 1;
        if ($lastbound > $totalrecords) {
            $lastbound = $totalrecords;
        }

        $tooltip = "Toplam " . $totalrecords . " kaydın " . $firstbound . " - " . $lastbound . " arası gösteriliyor";
        // Sonraki Sayfa Bağlantısı
        $pid = ($pagenumber + 1);
        if ($pid > $totalpages) {
            $pid = $totalpages;
        }
        $nextPageCss = "";
        $nextPageIcon = "<i class=\"glyphicon glyphicon-chevron-right\"></i>";
        if ($this->paginationstyle == 2) {
            if ($this->nextCss != "") {
                $nextPageCss = " class=\"" . $this->nextCss . "\"";
            }
            $nextPageIcon = "Sonraki &rarr;";
        }
        $script .= "<li" . $nextPageCss . "><a id=\"pn_" . $pid . "\" href=\"" . $this->prepareUrl($pid) . "\" class=\"pagination-css\" data-toggle=\"tooltip\" title=\"" . $tooltip . "\">" . $nextPageIcon . "</a></li>";
        if ($showlast && $this->paginationstyle != 2) {
            // Son Sayfa Bağlantısı
            $firstbound = (($totalpages - 1) * $pagesize) + 1;
            $lastbound = $firstbound + $pagesize - 1;
            if ($lastbound > $totalpages) {
                $lastbound = $totalpages;
            }
            $tooltip = "Toplam " . $totalrecords . " kaydın " . $firstbound . " - " . $lastbound . " arası gösteriliyor";
            $script .= "<li><a id=\"pl_" . $totalpages . "\" href=\"" . $this->prepareUrl($totalpages) . "\" class=\"pagination-css\" data-toggle=\"tooltip\" title=\"" . $tooltip . "\"><i class=\"glyphicon glyphicon-fast-forward\"></i></a></li>";
        }
        return $script;
    }
	
    function prepareUrl($pid)
    {
        if ($this->paginationUrl == "") {
            $this->paginationUrl = "#";
        }
        if ($pid > 1) {
            return preg_replace("/\[p\]/", $pid, $this->paginationUrl);
        } else {
            return preg_replace("/\[p\]/", $pid, $this->defaultUrl);
        }
    }
}
