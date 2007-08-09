<?php

class Comp_iframe {
    var $page;

    /*
     * construct a simple iframe component
     * here, the $content is the iframe url
     */
    function Comp_iframe($content) {
	$this->page=$content;
    }

    function getHTMLContent() {
	// iframe is a special case and this fonction
	// should be never call
    }

    function getPermObject() {
	// TODO: I don't know what to do here.
	global $tikilib;
	$ps = $tikilib->get_perm_object($this->page, 'iframe page', false);
	$ps['tiki_p_view_iframe'] = $ps['tiki_p_view'];
	return $ps;
    }

    function getConfigureDiv() {
	return "URL: <input name='url' type='text' value='' />";
    }

    function configure($form) {
	return $form['url'];
    }
}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

?>