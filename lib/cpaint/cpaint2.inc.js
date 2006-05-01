/**
* CPAINT - Cross-Platform Asynchronous INterface Toolkit
*
* http://sf.net/projects/cpaint
* 
* released under the terms of the LGPL
* see http://www.fsf.org/licensing/licenses/lgpl.txt for details
*
* @package      CPAINT
* @access       public
* @copyright    Copyright (c) 2005-2006 Paul Sullivan, Dominique Stender - http://sf.net/projects/cpaint
* @author       Paul Sullivan <wiley14@gmail.com>
* @author       Dominique Stender <dstender@st-webdevelopment.de>
* @author		Stephan Tijink <stijink@googlemail.com>
* @version      2.0.3
*/
function cpaint() {
  /**
  * CPAINT version
  * 
  * @access     protected
  * @var        string      version
  */
  this.version = '2.0.3';
  
  /**
  * configuration options both for this class but also for  the cpaint_call() objects.
  *
  * @access     protected
  * @var        array       config
  */
  var config                      = new Array();
  config['debugging']             = -1;
  config['proxy_url']             = '';
  config['transfer_mode']         = 'GET';
  config['async']                 = true;
  config['response_type']         = 'OBJECT';
  config['persistent_connection'] = false;
  config['use_cpaint_api']        = true;
  
  /**
  * maintains the next free index in the stack
  *
  * @access   protected
  * @var      integer   stack_count
  */
  var stack_count = 0;

  /**
  * property returns whether or not the browser is AJAX capable
  * 
  * @access		public
  * @return		boolean
  */
  this.capable = test_ajax_capability();
  
  /**
  * switches debug mode on/off.
  *
  * @access   public
  * @param    boolean    debug    debug flag
  * @return   void
  */
  this.set_debug = function() {
    
    if (typeof arguments[0] == 'boolean') {
      if (arguments[0] === true) {
        config['debugging'] = 1;

      } else {
        config['debugging'] = 0;
      }
      
    } else if (typeof arguments[0] == 'number') {
      config['debugging'] = Math.round(arguments[0]);
    }
  }

  /**
  * defines the URL of the proxy script.
  *
  * @access   public
  * @param    string    proxy_url    URL of the proxyscript to connect
  * @return   void
  */
  this.set_proxy_url = function() {
    
    if (typeof arguments[0] == 'string') {

      config['proxy_url'] = arguments[0];
    }
  }

  /**
  * sets the transfer_mode (GET|POST).
  *
  * @access   public
  * @param    string    transfer_mode    transfer_mode
  * @return   void
  */
  this.set_transfer_mode = function() {
    
    if (arguments[0].toUpperCase() == 'GET'
      || arguments[0].toUpperCase() == 'POST') {

      config['transfer_mode'] = arguments[0].toUpperCase();
    }
  }

  /**
  * sets the flag whether or not to use asynchronous calls.
  *
  * @access   public
  * @param    boolean    async    syncronization flag
  * @return   void
  */
  this.set_async = function() {
    
    if (typeof arguments[0] == 'boolean') {
      config['async'] = arguments[0];
    }
  }

  /**
  * defines the response type.
  *
  * allowed values are:
  *   TEXT    = raw text response
  *   XML     = raw XMLHttpObject
  *   OBJECT  = parsed JavaScript object structure from XMLHttpObject
  *
  * the default is OBJECT.
  *
  * @access   public
  * @param    string    response_type    response type
  * @return   void
  */
  this.set_response_type = function() {
    
    if (arguments[0].toUpperCase() == 'TEXT'
      || arguments[0].toUpperCase() == 'XML'
      || arguments[0].toUpperCase() == 'OBJECT'
      || arguments[0].toUpperCase() == 'E4X'
      || arguments[0].toUpperCase() == 'JSON') {

      config['response_type'] = arguments[0].toUpperCase();
    }
  }

  /**
  * sets the flag whether or not to use a persistent connection.
  *
  * @access   public
  * @param    boolean    persistent_connection    persistance flag
  * @return   void
  */
  this.set_persistent_connection = function() {
    
    if (typeof arguments[0] == 'boolean') {
      config['persistent_connection'] = arguments[0];
    }
  }
  
  
  /**
  * sets the flag whether or not to use the cpaint api on the backend.
  *
  * @access    public
  * @param     boolean    cpaint_api      api_flag
  * @return    void
  */
  this.set_use_cpaint_api = function() {
    if (typeof arguments[0] == 'boolean') {
      config['use_cpaint_api'] = arguments[0];
    }
  }
  
  /**
  * tests whether one of the necessary implementations
  * of the XMLHttpRequest class are available
  *
  * @access     protected
  * @return     boolean
  */
  function test_ajax_capability() {
    var cpc = new cpaint_call(0, config, this.version);
    return cpc.test_ajax_capability();
  }

  /**
  * takes the arguments supplied and triggers a call to the CPAINT backend
  * based on the settings.
  *
  * upon response cpaint_call.callback() will automatically be called
  * to perform post-processing operations.
  *
  * @access   public
  * @param    string    url                 remote URL to call
  * @param    string    remote_method       remote method to call
  * @param    object    client_callback     client side callback method to deliver the remote response to. do NOT supply a string!
  * @param    mixed     argN                remote parameters from now on
  * @return   void
  */
  this.call = function() {
    var use_stack = -1;
    
    if (config['persistent_connection'] == true
      && __cpaint_stack[0] != null) {

      switch (__cpaint_stack[0].get_http_state()) {
        case -1:
          // no XMLHttpObject object has already been instanciated
          // create new object and configure it
          use_stack = 0;
          debug('no XMLHttpObject object to re-use for persistence, creating new one later', 2);
          break;
          
        case 4:
          // object is ready for a new request, no need to do anything
          use_stack = 0
          debug('re-using the persistent connection', 2);
          break;
          
        default:
          // connection is currently in use, don't do anything
          debug('the persistent connection is in use - skipping this request', 2);
      }
      
    } else if (config['persistent_connection'] == true) {
      // persistent connection is active, but no object has been instanciated
      use_stack = 0;
      __cpaint_stack[use_stack] = new cpaint_call(use_stack, config, this.version);
      debug('no cpaint_call object available for re-use, created new one', 2);
    
    } else {
      // no connection persistance
      use_stack = stack_count;
      __cpaint_stack[use_stack] = new cpaint_call(use_stack, config, this.version);
      debug('no cpaint_call object created new one', 2);
    }

    // configure cpaint_call if allowed to
    if (use_stack != -1) {
      __cpaint_stack[use_stack].set_client_callback(arguments[2]);
      
      // distribute according to proxy use
      if (config['proxy_url'] != '') {
        __cpaint_stack[use_stack].call_proxy(arguments);
      
      } else {
        __cpaint_stack[use_stack].call_direct(arguments);
      }

      // increase stack counter
      stack_count++;
      debug('stack size: ' + __cpaint_stack.length, 2);
    }
  }

  /**
  * debug method
  *
  * @access  protected
  * @param   string       message         the message to debug
  * @param   integer      debug_level     debug level at which the message appears
  * @return  void
  */
  var debug  = function(message, debug_level) {
    var prefix = '[CPAINT Debug] ';
    
    if (debug_level < 1) {
      prefix = '[CPAINT Error] ';
    }
    
    if (config['debugging'] >= debug_level) {
      alert(prefix + message);
    }
  }
}

/**
* internal FIFO stack of cpaint_call() objects.
*
* @access   protected
* @var      array    __cpaint_stack
*/
var __cpaint_stack = new Array();

/**
* local instance of cpaint_transformer
* MSIE is unable to handle static classes... sheesh.
*
* @access   public
* @var      object    __cpaint_transformer
*/
var __cpaint_transformer = new cpaint_transformer();

/**
* transport agent class
*
* creates the request object, takes care of the response, handles the 
* client callback. Is configured by the cpaint() object.
*
* @package      CPAINT
* @access       public
* @copyright    Copyright (c) 2005-2006 Paul Sullivan, Dominique Stender - http://sf.net/projects/cpaint
* @author       Dominique Stender <dstender@st-webdevelopment.de>
* @author       Paul Sullivan <wiley14@gmail.com>
* @param        integer     stack_id      stack Id in cpaint
* @param        array       config        configuration array for this call
* @param        string      version       CPAINT API version
*/
function cpaint_call() {
  /**
  * CPAINT version
  * 
  * @access     protected
  * @var        string      version
  */
  var version = arguments[2];
  
  /**
  * configuration options both for this class objects.
  *
  * @access     protected
  * @var        array       config
  */
  var config                      = new Array();
  config['debugging']             = arguments[1]['debugging'];
  config['proxy_url']             = arguments[1]['proxy_url'];
  config['transfer_mode']         = arguments[1]['transfer_mode'];
  config['async']                 = arguments[1]['async'];
  config['response_type']         = arguments[1]['response_type'];
  config['persistent_connection'] = arguments[1]['persistent_connection'];
  config['use_cpaint_api']        = arguments[1]['use_cpaint_api'];

  /**
  * XMLHttpObject used for this request.
  *
  * @access   protected
  * @var      object     httpobj
  */
  var httpobj    = false;

  /**
  * client callback function.
  *
  * @access   public
  * @var      function    client_callback
  */
  var client_callback;

  /**
  * stores the stack Id within the cpaint object
  *
  * @access   protected
  * @var      stack_id
  */
  var stack_id = arguments[0];
  
  /**
  * sets the client callback function.
  *
  * @access   public
  * @param    function    client_callback     the client callback function
  * @return   void
  */
  this.set_client_callback = function() {
    
    if (typeof arguments[0] == 'function') {
      client_callback = arguments[0];
    }
  }

  /**
  * returns the ready state of the internal XMLHttpObject
  *
  * if no such object was set up already, -1 is returned
  * 
  * @access     public
  * @return     integer
  */
  this.get_http_state = function() {
    var return_value = -1;
    
    if (typeof httpobj == 'object') {
      return_value = httpobj.readyState;
    }
    
    return return_value;
  }
  
  /**
  * internal method for remote calls to the local server without use of the proxy script.
  *
  * @access   public
  * @param    array    call_arguments    array of arguments initially passed to cpaint.call()
  * @return   void
  */
  this.call_direct = function(call_arguments) {
    var url             = call_arguments[0];
    var remote_method   = call_arguments[1];
    var querystring     = '';
    var i               = 0;
    
    // correct link to self
    if (url == 'SELF') {
      url = document.location.href;
    }
  
    if (config['use_cpaint_api'] == true) {
      // backend uses cpaint api
      // pass parameters to remote method
      for (i = 3; i < call_arguments.length; i++) {

        if ((typeof call_arguments[i] == 'string'
              && call_arguments[i] != ''
              && call_arguments[i].search(/^\s+$/g) == -1)
          && !isNaN(call_arguments[i])
          && isFinite(call_arguments[i])) {
          // numerical value, convert it first
          querystring += '&cpaint_argument[]=' + encodeURIComponent(JSON.stringify(Number(call_arguments[i])));
        
        } else {
          querystring += '&cpaint_argument[]=' + encodeURIComponent(JSON.stringify(call_arguments[i]));
        }
      }
    
      // add response type to querystring
      querystring += '&cpaint_response_type=' + config['response_type'];
    
      // build header
      if (config['transfer_mode'] == 'GET') {
				
        if(url.indexOf('?') != -1) {
					url = url + '&cpaint_function=' + remote_method +	querystring;
				
        } else {
					url = url + '?cpaint_function=' + remote_method +	querystring; 
				}
      
      } else {
        querystring = 'cpaint_function=' + remote_method + querystring;
      }
      
    } else {
      // backend does not use cpaint api
      // pass parameters to remote method
      for (i = 3; i < call_arguments.length; i++) {
        
        if (i == 3) {
          querystring += encodeURIComponent(call_arguments[i]);
        
        } else {
          querystring += '&' + encodeURIComponent(call_arguments[i]);
        }
      }
    
      // build header
      if (config['transfer_mode'] == 'GET') {
        url = url + querystring;
      } 
    }
  
    // open connection 
    get_connection_object();

    // open connection to remote target
    debug('opening connection to "' + url + '"', 1);
    httpobj.open(config['transfer_mode'], url, config['async']);

    // send "urlencoded" header if necessary (if POST)
    if (config['transfer_mode'] == 'POST') {

      try {
        httpobj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      } catch (cp_err) {
        debug('POST cannot be completed due to incompatible browser.  Use GET as your request method.', 0);
      }
    }

    // make ourselves known
    httpobj.setRequestHeader('X-Powered-By', 'CPAINT v' + version + ' :: http://sf.net/projects/cpaint');

    // callback handling for asynchronous calls
    httpobj.onreadystatechange = callback;

    // send content
    if (config['transfer_mode'] == 'GET') {
      httpobj.send(null);

    } else {
      debug('sending query: ' + querystring, 1);
      httpobj.send(querystring);
    }

    if (config['async'] == true) {
      // manual callback handling for synchronized calls
      callback();
    }
  }
    
  /**
  * internal method for calls to remote servers through the proxy script.
  *
  * @access   public
  * @param    array    call_arguments    array of arguments passed to cpaint.call()
  * @return   void
  */
  this.call_proxy = function(call_arguments) {
    var proxyscript     = config['proxy_url'];
    var url             = call_arguments[0];
    var remote_method   = call_arguments[1];
    var querystring     = '';
    var i               = 0;
    
    var querystring_argument_prefix = 'cpaint_argument[]=';

    // pass parameters to remote method
    if (config['use_cpaint_api'] == false) {
      // when not talking to a CPAINT backend, don't prefix arguments
      querystring_argument_prefix = '';
    }

    for (i = 3; i < call_arguments.length; i++) {

      if (config['use_cpaint_api'] == true) {
      
        if ((typeof call_arguments[i] == 'string'
              && call_arguments[i] != ''
              && call_arguments[i].search(/^\s+$/g) == -1)
          && !isNaN(call_arguments[i])
          && isFinite(call_arguments[i])) {
          // numerical value, convert it first
          querystring += encodeURIComponent(querystring_argument_prefix + JSON.stringify(Number(call_arguments[i])) + '&');

        } else {
          querystring += encodeURIComponent(querystring_argument_prefix + JSON.stringify(call_arguments[i]) + '&');
        }
        
      } else {
        // no CPAINT in the backend
        querystring += encodeURIComponent(querystring_argument_prefix + call_arguments[i] + '&');
      }
    }

    if (config['use_cpaint_api'] == true) {
      // add remote function name to querystring
      querystring += encodeURIComponent('&cpaint_function=' + remote_method);
  
      // add response type to querystring
      querystring += encodeURIComponent('&cpaint_responsetype=' + config['response_type']);
    }
    
    // build header
    if (config['transfer_mode'] == 'GET') {
      proxyscript += '?cpaint_remote_url=' + encodeURIComponent(url) 
        + '&cpaint_remote_query=' + querystring
        + '&cpaint_remote_method=' + config['transfer_mode'] 
        + '&cpaint_response_type=' + config['response_type'];

    } else {
      querystring = 'cpaint_remote_url=' + encodeURIComponent(url)
        + '&cpaint_remote_query=' + querystring
        + '&cpaint_remote_method=' + config['transfer_mode'] 
        + '&cpaint_response_type=' + config['response_type'];
    }

    // open connection
    get_connection_object();

    // open connection to remote target
    debug('opening connection to proxy "' + proxyscript + '"', 1);
    httpobj.open(config['transfer_mode'], proxyscript, config['async']);

    // send "urlencoded" header if necessary (if POST)
    if (config['transfer_mode'] == 'POST') {

      try {
        httpobj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      } catch (cp_err) {
        debug('POST cannot be completed due to incompatible browser.  Use GET as your request method.', 0);
      }
    }

    httpobj.setRequestHeader('X-Powered-By', 'CPAINT v' + version);

    // callback handling for asynchronous calls
    httpobj.onreadystatechange = callback;

    // send content
    if (config['transfer_mode'] == 'GET') {
      httpobj.send(null);

    } else {
      debug('sending query: ' + querystring, 1);
      httpobj.send(querystring);
    }

    if (config['async'] == false) {
      // manual callback handling for synchronized calls
      callback();
    }
  }

  this.test_ajax_capability = function() {
    return get_connection_object();
  }
  
  /**
  * creates a new connection object.
  *
  * @access   protected
  * @return   boolean
  */
  var get_connection_object = function() {
    var return_value    = false;
    var new_connection  = false;

    // open new connection only if necessary
    if (config['persistent_connection'] == false) {
      // no persistance, create a new object every time
      debug('Using new connection object', 1);
      new_connection = true;

    } else {
      // persistent connection object, only open one if no object exists
      debug('Using shared connection object.', 1);

      if (typeof httpobj != 'object') {
        debug('Getting new persistent connection object.', 1);
        new_connection = true;
      }
    }

    if (new_connection == true) {
		
	 try {
        httpobj = new XMLHttpRequest();
      } catch (e1) {

		  try {
			httpobj = new ActiveXObject('Msxml2.XMLHTTP');
	  
		  } catch (e) {
			
			try {  
			  httpobj = new ActiveXObject('Microsoft.XMLHTTP');
 
			} catch (oc) {
			  httpobj = null;
			} 
		 }
	  }
     
  
      if (!httpobj) {
        debug('Could not create connection object', 0);
      
      } else {
        return_value = true;
      }
    }

    if (httpobj.readyState != 4) {
      httpobj.abort();
    }

    return return_value;
  }

  /**
  * internal callback function.
  *
  * will perform some consistency checks (response code, NULL value testing)
  * and if response_type = 'OBJECT' it will automatically call
  * cpaint_call.parse_ajax_xml() to have a JavaScript object structure generated.
  *
  * after all that is done the client side callback function will be called 
  * with the generated response as single value.
  *
  * @access   protected
  * @return   void
  */
  var callback = function() {
    var response = null;

    if (httpobj.readyState == 4
      && httpobj.status == 200) {
      
      debug(httpobj.responseText, 1);
      debug('using response type ' + config['response_type'], 2);
      
      // fetch correct response
      switch (config['response_type']) {
        case 'XML':
          debug(httpobj.responseXML, 2);
          response = __cpaint_transformer.xml_conversion(httpobj.responseXML);
          break;
          
        case 'OBJECT':
          response = __cpaint_transformer.object_conversion(httpobj.responseXML);
          break;
        
        case 'TEXT':
          response = __cpaint_transformer.text_conversion(httpobj.responseText);
          break;
          
        case 'E4X':
          response = __cpaint_transformer.e4x_conversion(httpobj.responseText);
          break;
          
        case 'JSON':
          response = __cpaint_transformer.json_conversion(httpobj.responseText);
          break;
          
        default:
          debug('invalid response type \'' + response_type + '\'', 0);
      }
      
      // call client side callback
      if (response != null 
        && typeof client_callback == 'function') {
        client_callback(response, httpobj.responseText);
      }
      
      // remove ourselves from the stack
      remove_from_stack();
    
    } else if (httpobj.readyState == 4
      && httpobj.status != 200) {
      // HTTP error of some kind
      debug('invalid HTTP response code \'' + Number(httpobj.status) + '\'', 0);
    }
  }

  /**
  * removes an entry from the stack
  *
  * @access     protected
  * @return     void
  */
  var remove_from_stack = function() {
    // remove only if everything is okay and we're not configured as persistent connection
    if (typeof stack_id == 'number'
      && __cpaint_stack[stack_id]
      && config['persistent_connection'] == false) {
      
      __cpaint_stack[stack_id] = null;
    }
  }

  /**
  * debug method
  *
  * @access  protected
  * @param   string       message         the message to debug
  * @param   integer      debug_level     debug level at which the message appears
  * @return  void
  */
  var debug  = function(message, debug_level) {
    var prefix = '[CPAINT Debug] ';
    
    if (config['debugging'] < 1) {
      prefix = '[CPAINT Error] ';
    }
    
    if (config['debugging'] >= debug_level) {
      alert(prefix + message);
    }
  }
}

/**
* CPAINT transformation object
*
* @package      CPAINT
* @access       public
* @copyright    Copyright (c) 2005-2006 Paul Sullivan, Dominique Stender - http://sf.net/projects/cpaint
* @author       Paul Sullivan <wiley14@gmail.com>
* @author       Dominique Stender <dstender@st-webdevelopment.de>
*/
function cpaint_transformer() {

  /**
  * will take a XMLHttpObject and generate a JavaScript
  * object structure from it.
  *
  * is internally called by cpaint_call.callback() if necessary.
  * will call cpaint_call.create_object_structure() to create nested object structures.
  *
  * @access   public
  * @param    object    xml_document  a XMLHttpObject
  * @return   object
  */
  this.object_conversion = function(xml_document) {
    var return_value  = new cpaint_result_object();
    var i             = 0;
    var firstNodeName = '';
    
    if (typeof xml_document == 'object'
      && xml_document != null) {

      // find the first element node - for MSIE the <?xml?> node is the very first...
      for (i = 0; i < xml_document.childNodes.length; i++) {

        if (xml_document.childNodes[i].nodeType == 1) {
          firstNodeName = xml_document.childNodes[i].nodeName;
          break;
        }
      }
      
      var ajax_response = xml_document.getElementsByTagName(firstNodeName);

      return_value[firstNodeName] = new Array();
    
      for (i = 0; i < ajax_response.length; i++) {
        var tmp_node = create_object_structure(ajax_response[i]);
        tmp_node.id  = ajax_response[i].getAttribute('id')
        return_value[firstNodeName].push(tmp_node);
      }

    } else {
      alert('received invalid XML response', 0);
    }

    return return_value;
  }

  /**
  * performs the necessary conversions for the XML response type
  *
  * @access   public
  * @param    object    xml_document  a XMLHttpObject
  * @return   object
  */
  this.xml_conversion = function(xml_document) {
    return xml_document;
  }
  
  /**
  * performs the necessary conversions for the TEXT response type
  *
  * @access   public
  * @param    string    text  the response text
  * @return   string
  */
  this.text_conversion = function(text) {
    return decode(text);
  }
  
  /**
  * performs the necessary conversions for the E4X response type
  *
  * @access   public
  * @param    string    text  the response text
  * @return   string
  */
  this.e4x_conversion = function(text) {
    // remove <?xml ?>tag
    text = text.replace(/^\<\?xml[^>]+\>/, '');
    return new XML(text);
  }
  
  /**
  * performs the necessary conversions for the JSON response type
  *
  * @access   public
  * @param    string    text  the response text
  * @return   string
  */
  this.json_conversion = function(text) {
    return JSON.parse(text);
  }
  
  /**
  * this method takes a HTML / XML node object and creates a
  * JavaScript object structure from it.
  *
  * @access   public
  * @param    object    stream    a node in the XML structure
  * @return   object
  */
  var create_object_structure = function(stream) {
    var return_value = new cpaint_result_object();
    var node_name = '';
    var i         = 0;
    var attrib    = 0;
    
    if (stream.hasChildNodes() == true) {
      for (i = 0; i < stream.childNodes.length; i++) {
  
        node_name = stream.childNodes[i].nodeName;
        node_name = node_name.replace(/[^a-zA-Z0-9_]*/g, '');
        
        // reset / create subnode
        if (typeof return_value[node_name] != 'object') {
          return_value[node_name] = new Array();
        }
        
        if (stream.childNodes[i].nodeType == 1) {
          var tmp_node  = create_object_structure(stream.childNodes[i]);

          for (attrib = 0; attrib < stream.childNodes[i].attributes.length; attrib++) {
            tmp_node.set_attribute(stream.childNodes[i].attributes[attrib].nodeName, stream.childNodes[i].attributes[attrib].nodeValue);
          }
          
          return_value[node_name].push(tmp_node);
        
        } else if (stream.childNodes[i].nodeType == 3) {
          return_value.data  = decode(String(stream.firstChild.data));
        }
      }
    }
    
    return return_value;
  }

  /**
  * converts an encoded text back to viewable characters.
  *
  * @access     public
  * @param      string      rawtext     raw text as provided by the backend
  * @return     mixed
  */
  var decode = function(rawtext) {
    var plaintext = ''; 
    var i         = 0; 
    var c1        = 0;
    var c2        = 0;
    var c3        = 0;
    var u         = 0;
    var t         = 0;

    // remove special JavaScript encoded non-printable characters
    while (i < rawtext.length) {
      if (rawtext.charAt(i) == '\\'
        && rawtext.charAt(i + 1) == 'u') {
        
        u = 0;
        
        for (j = 2; j < 6; j += 1) {
          t = parseInt(rawtext.charAt(i + j), 16);
          
          if (!isFinite(t)) {
            break;
          }
          u = u * 16 + t;
        }

        plaintext += String.fromCharCode(u);
        i       += 6;
      
      } else {
        plaintext += rawtext.charAt(i);
        i++;
      }
    }

    // convert numeric data to number type
    if (plaintext != ''
      && plaintext.search(/^\s+$/g) == -1
      && !isNaN(plaintext) 
      && isFinite(plaintext)) {
      
      plaintext = Number(plaintext);
    }
  
    return plaintext;
  }
}

/**
* this is the basic prototype for a cpaint node object
* as used in cpaint_call.parse_ajax_xml()
*
* @package      CPAINT
* @access       public
* @copyright    Copyright (c) 2005-2006 Paul Sullivan, Dominique Stender - http://sf.net/projects/cpaint
* @author       Paul Sullivan <wiley14@gmail.com>
* @author       Dominique Stender <dstender@st-webdevelopment.de>
*/
function cpaint_result_object() {
  this.id           = 0;
  this.data         = '';
  var __attributes  = new Array();
  
  /**
  * Returns a subnode with the given type and id.
  *
  * @access     public
  * @param      string    type    The type of the subnode. Equivalent to the XML tag name.
  * @param      string    id      The id of the subnode. Equivalent to the XML tag names id attribute.
  * @return     object
  */
  this.find_item_by_id = function() {
    var return_value  = null;
    var type    = arguments[0];
    var id      = arguments[1];
    var i       = 0;
    
    if (this[type]) {

      for (i = 0; i < this[type].length; i++) {

        if (this[type][i].get_attribute('id') == id) {
          return_value = this[type][i];
          break;
        }
      }
    }

    return return_value;
  }
  
  /**
  * retrieves the value of an attribute.
  *
  * @access   public
  * @param    string    name    name of the attribute
  * @return   mixed
  */
  this.get_attribute = function() {
    var return_value  = null;
    var id            = arguments[0];
    
    if (typeof __attributes[id] != 'undefined') {
      return_value = __attributes[id];
    }
    
    return return_value;
  }
  
  /**
  * assigns a value to an attribute.
  *
  * if that attribute does not exist it will be created.
  *
  * @access     public
  * @param      string    name    name of the attribute
  * @param      string    value   value of the attribute
  * @return     void
  */
  this.set_attribute = function() {
    __attributes[arguments[0]] = arguments[1];
  }
}


/*
Copyright (c) 2005 JSON.org

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The Software shall be used for Good, not Evil.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

Array.prototype.______array = '______array';

var JSON = {
  org: 'http://www.JSON.org',
  copyright: '(c)2005 JSON.org',
  license: 'http://www.crockford.com/JSON/license.html',

  stringify: function (arg) {
    var c, i, l, s = '', v;
    var numeric = true;
    
    switch (typeof arg) {
    case 'object':
      if (arg) {
        if (arg.______array == '______array') {
          // do a test whether all array keys are numeric
          for (i in arg) {
            if (i != '______array'
              && (isNaN(i) 
                || !isFinite(i))) {
              numeric = false;
              break;
            }
          }
          
          if (numeric == true) {
            for (i = 0; i < arg.length; ++i) {
              if (typeof arg[i] != 'undefined') {
                v = this.stringify(arg[i]);
                if (s) {
                  s += ',';
                }
                s += v;
              } else {
                s += ',null';
              }
            }
            return '[' + s + ']';
          } else {
            for (i in arg) {
              if (i != '______array') {
                v = arg[i];
                if (typeof v != 'undefined' && typeof v != 'function') {
                  v = this.stringify(v);
                  if (s) {
                    s += ',';
                  }
                  s += this.stringify(i) + ':' + v;
                }
              }
            }
            // return as object
            return '{' + s + '}';
          }
        } else if (typeof arg.toString != 'undefined') {
          for (i in arg) {
            v = arg[i];
            if (typeof v != 'undefined' && typeof v != 'function') {
              v = this.stringify(v);
              if (s) {
                s += ',';
              }
              s += this.stringify(i) + ':' + v;
            }
          }
          return '{' + s + '}';
        }
      }
      return 'null';
    case 'number':
      return isFinite(arg) ? String(arg) : 'null';
    case 'string':
      l = arg.length;
      s = '"';
      for (i = 0; i < l; i += 1) {
        c = arg.charAt(i);
        if (c >= ' ') {
          if (c == '\\' || c == '"') {
            s += '\\';
          }
          s += c;
        } else {
          switch (c) {
            case '\b':
              s += '\\b';
              break;
            case '\f':
              s += '\\f';
              break;
            case '\n':
              s += '\\n';
              break;
            case '\r':
              s += '\\r';
              break;
            case '\t':
              s += '\\t';
              break;
            default:
              c = c.charCodeAt();
              s += '\\u00' + Math.floor(c / 16).toString(16) +
                (c % 16).toString(16);
          }
        }
      }
      return s + '"';
    case 'boolean':
      return String(arg);
    default:
      return 'null';
    }
  },
  parse: function (text) {
    var at = 0;
    var ch = ' ';

    function error(m) {
      throw {
        name: 'JSONError',
        message: m,
        at: at - 1,
        text: text
      };
    }

    function next() {
      ch = text.charAt(at);
      at += 1;
      return ch;
    }

    function white() {
      while (ch != '' && ch <= ' ') {
        next();
      }
    }

    function str() {
      var i, s = '', t, u;

      if (ch == '"') {
outer:      while (next()) {
          if (ch == '"') {
            next();
            return s;
          } else if (ch == '\\') {
            switch (next()) {
            case 'b':
              s += '\b';
              break;
            case 'f':
              s += '\f';
              break;
            case 'n':
              s += '\n';
              break;
            case 'r':
              s += '\r';
              break;
            case 't':
              s += '\t';
              break;
            case 'u':
              u = 0;
              for (i = 0; i < 4; i += 1) {
                t = parseInt(next(), 16);
                if (!isFinite(t)) {
                  break outer;
                }
                u = u * 16 + t;
              }
              s += String.fromCharCode(u);
              break;
            default:
              s += ch;
            }
          } else {
            s += ch;
          }
        }
      }
      error("Bad string");
    }

    function arr() {
      var a = [];

      if (ch == '[') {
        next();
        white();
        if (ch == ']') {
          next();
          return a;
        }
        while (ch) {
          a.push(val());
          white();
          if (ch == ']') {
            next();
            return a;
          } else if (ch != ',') {
            break;
          }
          next();
          white();
        }
      }
      error("Bad array");
    }

    function obj() {
      var k, o = {};

      if (ch == '{') {
        next();
        white();
        if (ch == '}') {
          next();
          return o;
        }
        while (ch) {
          k = str();
          white();
          if (ch != ':') {
            break;
          }
          next();
          o[k] = val();
          white();
          if (ch == '}') {
            next();
            return o;
          } else if (ch != ',') {
            break;
          }
          next();
          white();
        }
      }
      error("Bad object");
    }

    function assoc() {
      var k, a = [];

      if (ch == '<') {
        next();
        white();
        if (ch == '>') {
          next();
          return a;
        }
        while (ch) {
          k = str();
          white();
          if (ch != ':') {
            break;
          }
          next();
          a[k] = val();
          white();
          if (ch == '>') {
            next();
            return a;
          } else if (ch != ',') {
            break;
          }
          next();
          white();
        }
      }
      error("Bad associative array");
    }

    function num() {
      var n = '', v;
      if (ch == '-') {
        n = '-';
        next();
      }
      while (ch >= '0' && ch <= '9') {
        n += ch;
        next();
      }
      if (ch == '.') {
        n += '.';
        while (next() && ch >= '0' && ch <= '9') {
          n += ch;
        }
      }
      if (ch == 'e' || ch == 'E') {
        n += 'e';
        next();
        if (ch == '-' || ch == '+') {
          n += ch;
          next();
        }
        while (ch >= '0' && ch <= '9') {
          n += ch;
          next();
        }
      }
      v = +n;
      if (!isFinite(v)) {
        error("Bad number");
      } else {
        return v;
      }
    }

    function word() {
      switch (ch) {
        case 't':
          if (next() == 'r' && next() == 'u' && next() == 'e') {
            next();
            return true;
          }
          break;
        case 'f':
          if (next() == 'a' && next() == 'l' && next() == 's' &&
              next() == 'e') {
            next();
            return false;
          }
          break;
        case 'n':
          if (next() == 'u' && next() == 'l' && next() == 'l') {
            next();
            return null;
          }
          break;
      }
      error("Syntax error");
    }

    function val() {
      white();
      switch (ch) {
        case '{':
          return obj();
        case '[':
          return arr();
        case '<':
          return assoc();
        case '"':
          return str();
        case '-':
          return num();
        default:
          return ch >= '0' && ch <= '9' ? num() : word();
      }
    }

    return val();
  }
};
