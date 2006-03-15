<?php

class TikiAjax extends cpaint {

    /**
    * PHP4 constructor.
    *
    * @access   public
    * @return   void
    */
    function TikiAjax() {
	TikiAjax::__construct();
    }

    /**
    * PHP 5 constructor.
    *
    * @access   public
    * @return   void
    */
    function __construct() {
	parent::__construct();

	$this->register('handle');
	$this->register('handleContent');
    }

    /**
    * registers a new method for this object, just a layer over cpaint::register to
    * register a method on this object.
    *
    * @access   public
    * @param  string     $function     a method name from this class
    * @return   void
    */
    function register($function) {
	parent::register(array($this, $function));
    }

    /**
     * sends an error msg to client
     *
     * @access private
     * @param  string  $msg   error msg
     * @param  int     $code  error code
     * @return bool           always returns false so other functions can return $this->_error()
     *
     */
    function _error($msg, $code = false) {
	$this->_send('error',array('msg' => $msg));
	return false;
    }

    /** 
     * loads a function and calls it with $arguments
     *
     * @access private
     * @param string  $file     a file in ajax/ dir
     * @param string  $func     a function declared in $file
     * @param array   $args     arguments passed to $function
     * @return mixed  
     */
    function _call($file, $function, $arguments) {

	if (!$this->_loadFunction($file, $function)) return false;

	return call_user_func_array($function, $arguments);
    }

    /**
     * loads function $function from file $file
     * 
     * @access private
     * @param  string  $file      file to load
     * @param  string  $function  function to test for existence
     * @return bool
     * 
     */
    function _loadFunction($file, $function) {
	$filename = "ajax/".$file;

	if (!file_exists($filename)) return false;

	if (preg_match('/^_/', $function)) {
	    return $this->_error("$function is private");
	}

	// use output buffer so that linebreaks at end of file won't silently kill
	// cpaint client
	ob_start();
	require_once($filename);
	$trash = ob_get_contents();
	ob_end_clean();
	
	if (!function_exists($function)) { 
	    return $this->_error("$function does not exist");
	}
	
	return true;
    }
    

    /**
     * Encodes any data structure into xml and send to client
     * 
     * @access private
     * @param  string  $name      global key for this data
     * @param  mixed  $value      data to send
     * @return void
     * 
     */
    function _send($name, $value) {
	$node =& $this->add_node($name);
	$this->_send_item($node, $value);
    }
    
    /**
     * Recursively encode a variable into a given xml node object
     * 
     * @access private
     * @param  object  &$node      cpaint_node object where data will be encoded
     * @param  mixed   $value      data to send
     * @return void
     * 
     */
    function _send_item(&$node, $value) {
	
	if (!is_array($value) && !is_object($value)) {
	    
	    $node->set_data($value);
	    $node->set_attribute('type','scalar');
	    
	} elseif (is_array($value)) {
	    
	    $node->set_attribute('type','array');
	    
	    foreach ($value as $key => $value) {
		$subNode =& $node->add_node('item');
		$subNode->set_attribute('key',$key);
		$this->_send_item($subNode, $value);		
	    }
	    
	} elseif (is_object($value)) {
	    // not supported yet
	}
    }

    /*
     * REGISTERED FUNCTIONS
     */

    /*
     * This is called by ajax on all javascript load() calls. Passes to javascript
     * encoded result in xml, with key "tikiResult", and also an associative array
     * "metaData", containing information on how to handle the result other side.
     */
    function handle() {
	
	$arguments = func_get_args();
	
	$file = (string) array_shift($arguments);
	$function = (string) array_shift($arguments);
	
	$metadata = array('file' => $file,
			  'function' => $function);
	
	$result = $this->_call($file, $function, $arguments);
	
	
	// Discussion: should we modify result in the middle layer? it's more elegant
	// but might be confusing for some devs.
	if (is_array($result) && isset($result['data']) && isset($result['cant']) && sizeof($result) == 2) {
	    $metadata['cant'] = $result['cant'];
	    $result = $result['data'];
	}
	
	$this->_send('tikiResult', $result);
	$this->_send('metaData',   $metadata);
	
	return true;
    }
    
    function handleContent() {
	$arguments = func_get_args();
	
	$containerId = (string) array_shift($arguments);
	$function = (string) array_shift($arguments);
	
	$metadata = array('function' => $function,
			  'containerId' => $containerId);
	
	$result = (string) $this->_call($file, $function, $arguments);
	
	$this->_send('tikiResult', $result);
	$this->_send('metaData',   $metadata);
    }
    
}

?>