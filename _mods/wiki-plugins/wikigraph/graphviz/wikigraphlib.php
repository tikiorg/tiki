<?php
//these are the functions in pre-BRANCH-19 that have been in use for wikigraph
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if( !defined( 'PLUGINS_DIR' ) ) {
   define('PLUGINS_DIR', 'lib/wiki-plugins');
}

class WikiGraphLib extends TikiLib {

    function WikiGraphLib($db) {
        $this->TikiLib($db);
    }


    function wiki_page_graph(&$str, &$graph, $garg) {
        $page = $str['name'];

        $graph->addAttributes(array(
                    'nodesep' => (isset($garg['att']['nodesep']))?$garg['att']['nodesep']:".1",
                    'rankdir' => (isset($garg['att']['rankdir']))?$garg['att']['rankdir']:'LR',
                    'size' => (isset($garg['att']['size']))?$garg['att']['size']:'6',
                    'bgcolor' => (isset($garg['att']['bgcolor']))?$garg['att']['bgcolor']:'transparent',
                    'URL' => 'tiki-index.php'
                    ));

        $graph->addNode("$page", array(
                    'URL' => "tiki-index.php?page=" . urlencode(addslashes($page)),
                    'label' => "$page",
                    'fontname' => (isset($garg['node']['fontname']))?$garg['node']['fontname']:"Arial",
                    'fontsize' => (isset($garg['node']['fontsize']))?$garg['node']['fontsize']:'9',
                    'shape' => (isset($garg['node']['shape']))?$garg['node']['shape']:'ellipse',
                    'color' => (isset($garg['node']['color']))?$garg['node']['color']:'#AAAAAA',
                    'style' => (isset($garg['node']['style']))?$garg['node']['style']:'filled',
                    'fillcolor' => (isset($garg['node']['fillcolor']))?$garg['node']['fillcolor']:'#FFFFFF',
                    'width' => (isset($garg['node']['width']))?$garg['node']['width']:'.5',
                    'height' => (isset($garg['node']['height']))?$garg['node']['height']:'.25'
                    ));

        //print("add node $page<br />");
        foreach ($str['pages'] as $neig) {
            $this->wiki_page_graph($neig, $graph, $garg);

            $graph->addEdge(array("$page" => $neig['name']), array(
                        'color' => '#998877',
                        'style' => 'solid'
                        ));
            //print("add edge $page to ".$neig['name']."<br />");
        }
    }

    function get_graph_map($page, $level, $garg) {
        $str = $this->wiki_get_link_structure($page, $level);
        $graph = new Image_GraphViz();
        $this->wiki_page_graph($str, $graph, $garg);
        return $graph->map();
    }

    function wiki_get_link_structure($page, $level) {
        $query = "select `toPage` from `tiki_links` where `fromPage`=?";

        $result = $this->query($query,array($page));
        $aux['pages'] = array();
        $aux['name'] = $page;

        while ($res = $result->fetchRow()) {
            if ($level) {
                $aux['pages'][] = $this->wiki_get_link_structure($res['toPage'], $level - 1);
            } else {
                $inner['name'] = $res['toPage'];

                $inner['pages'] = array();
                $aux['pages'][] = $inner;
            }
        }

        return $aux;
    }
}

?>
