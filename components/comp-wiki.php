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

    /*
     * $for can be only 'view' actually
     */
    function getPerm($for) {
	global $tikilib;
	switch($for) {
	case 'view':
	    $ps = $tikilib->get_perm_object($this->page, 'wiki page', false);
	    return (isset($ps['tiki_p_view']) && ($ps['tiki_p_view'] == 'y'));
	default:
	    return false;
	}
    }

    function getConfigureDiv() {
	return "Title: <input name='pagename' type='text' value='' />";
    }

    function configure($form) {
	$this->page=$form['pagename'];
	return $this->page;
    }
}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

?>