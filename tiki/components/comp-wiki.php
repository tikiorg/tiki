<?php

class Comp_wiki {
    var $mypagewin;

    /*
     * construct a simple wiki component (viewable only)
     */
    function Comp_wiki($mypagewin) {
	$this->mypagewin=$mypagewin;
    }

    /* static */
    function newInstance_new($mypagewin) {
	return new Comp_wiki($mypagewin);
    }

    /* static */
    function newInstance_load($mypagewin) {
	return new Comp_wiki($mypagewin);
    }

    /* static */
    function newInstance_clone($mypagewin, $clone) {
	return new Comp_wiki($mypagewin);
    }

    /*
     * called when the user create a new component on the mypage
     */
    function create() {
	/*
	 * if you use a different sql table for exemple,
	 * you should create you're new entry here.
	 * you can get the mypagewin like this:
	 * $id=$this->mypagewin->id;
	 */
    }

    /*
     * called when the user destroy this component from his mypage
     */
    function destroy() {
	/*
	 * if you use a different sql table for exemple,
	 * you should destroy you're entry here
	 * you can get the mypagewin like this:
	 * $id=$this->mypagewin->id;
	 */
    }


    /*
     * called for displaying the component content
     */
    function getHTMLContent() {
	global $tikilib;

	$wiki_page_name=$this->mypagewin->getParam('config');
	$pageinfo=$tikilib->get_page_info($wiki_page_name);
	return $tikilib->parse_data($pageinfo['data'], $pageinfo['is_html']);
    }

    /*
     * $for can be only 'view' actually
     */
    function getPerm($for) {
	global $tikilib;
	switch($for) {
	case 'view':
	    $wiki_page_name=$this->mypagewin->getParam('config');
	    $ps = $tikilib->get_perm_object($wiki_page_name, 'wiki page', '', false);
	    return (isset($ps['tiki_p_view']) && ($ps['tiki_p_view'] == 'y'));
	default:
	    return false;
	}
    }

    /*
     * This function must return the HTML code to be displayed for configuring
     * this component
     */
    /* static or not static */
    function getConfigureDiv() {
	/*
	 * If we call this function by the static method,
	 * this is because we are creating a new component,
	 * so the component don't exist yet
	 * If not, we have to display the configure div with
	 * previous values
	 */

	if (isset($this)) {
	    $wiki_page_name=$this->mypagewin->getParam('config');
	} else {
	    $wiki_page_name='';
	}
	return "Wiki page name: <input name='pagename' type='text' value='".htmlspecialchars($wiki_page_name, ENT_QUOTES)."' />";
    }


    /*
     * This function is called when the user configure his component
     * The $form is an array of values like a $_REQUEST would be
     * Return true if you want the component to be redisplayed
     */
    function configure($form) {
	/*
	 * The 'config' parameter is provided for component use facility
	 * for storing config datas about this component.
	 * This is an optional field that we can use instead of storing
	 * you're configs datas somewhere else, but this have some limitations:
	 * this is only one field of type 'BLOB'.
	 */
	$this->mypagewin->setParam('config', $form['pagename']);
	
	return true;
    }

}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

?>