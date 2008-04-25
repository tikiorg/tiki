<?php

class Comp_iframe {
    var $mypagewin;

    /*
     * construct a simple iframe component
     */
    function Comp_iframe($mypagewin) {
	$this->mypagewin=$mypagewin;
    }

    /* static */
    function newInstance_new($mypagewin) {
	return new Comp_iframe($mypagewin);
    }

    /* static */
    function newInstance_load($mypagewin) {
	return new Comp_iframe($mypagewin);
    }

    /* static */
    function newInstance_clone($mypagewin, $clone) {
	return new Comp_iframe($mypagewin);
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
	// iframe is a special case of mypage and this fonction
	// will be never call
    }

    /*
     * $for can be only 'view' actually
     */
    function getPerm($for) {
	return true;
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

	$configs=array('url' => 'http://');

	if (isset($this)) {
	    $configs['url']=$this->mypagewin->getParam('config');
	}

	return "URL: <input name='url' type='text' value='".htmlspecialchars($configs['url'], ENT_QUOTES)."' />";
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
	$this->mypagewin->setParam('config', $form['url']);
	
	return true;
    }
}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

?>