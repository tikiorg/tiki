<?php

class Comp_wiki {
    var $page;
	var $contenttype;

    /*
     * construct a simple wiki component (only display wiki content, not edit)
     * here, the $content is the wiki page name
     */
    function Comp_wiki($content) {
	$this->page=$content;
	$this->contenttype = 'wiki';
    }

    function getHTMLContent() {
	global $tikilib;

	$pageinfo=$tikilib->get_page_info($this->page);
	return $tikilib->parse_data($pageinfo['data'], $pageinfo['is_html']);
    }

    function getPermObject() {
	global $tikilib;
	$ps = $tikilib->get_perm_object($this->page, 'wiki page', false);
	$ps['tiki_p_view_wiki'] = $ps['tiki_p_view'];
	return $ps;
    }
}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

?>