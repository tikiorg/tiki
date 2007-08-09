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

    /*
     * $for can be only 'view' actually
     */
    function getPerm($for) {
	return true;
    }

    function getConfigureDiv() {
	return "URL: <input name='url' type='text' value='' />";
    }

    function configure($form) {
	$this->page=$form['url'];
	return $this->page;
    }
}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

?>