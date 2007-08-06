<?php

class Comp_wiki {
    var $page;

    /*
     * construct a simple wiki component (only display wiki content, not edit)
     * here, the $content is the wiki page name
     */
    function Comp_wiki($content) {
	$this->page=$content;
    }

    function getHTMLContent() {
	global $tikilib;

	$pageinfo=$tikilib->get_page_info($this->page);
	return $tikilib->parse_data($pageinfo['data'], $pageinfo['is_html']);
    }
}

?>