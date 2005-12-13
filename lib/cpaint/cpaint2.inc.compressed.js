/**
* CPAINT - Cross-Platform Asynchronous INterface Toolkit
*
* http://cpaint.sourceforge.net
* 
* released under the terms of the LGPL
* see http://www.fsf.org/licensing/licenses/lgpl.txt for details
*
* @package      CPAINT
* @access       public
* @copyright    Copyright (c) 2005 Paul Sullivan, Dominique Stender - http://cpaint.sourceforge.net
* @author       Paul Sullivan <wiley14@gmail.com>
* @author       Dominique Stender <dstender@st-webdevelopment.de>
* @version      2.0.1
*/
function cpaint(){var config=new Array();config['debugging']=0;config['proxy_url']='';config['transfer_mode']='GET';config['async']=true;config['response_type']='OBJECT';config['persistent_connection']=false;config['use_cpaint_api']=true;var stack_count=0;this.capable=test_ajax_capability();this.set_debug=function(){if(typeof arguments[0]=='boolean'){if(arguments[0]===true){config['debugging']=1;}else{config['debugging']=0;}}else if(typeof arguments[0]=='number'){config['debugging']=Math.round(arguments[0]);}}
this.set_proxy_url=function(){if(typeof arguments[0]=='string'){config['proxy_url']=arguments[0];}}
this.set_transfer_mode=function(){if(arguments[0].toUpperCase()=='GET'||arguments[0].toUpperCase()=='POST'){config['transfer_mode']=arguments[0].toUpperCase();}}
this.set_async=function(){if(typeof arguments[0]=='boolean'){config['async']=arguments[0];}}
this.set_response_type=function(){if(arguments[0].toUpperCase()=='TEXT'||arguments[0].toUpperCase()=='XML'||arguments[0].toUpperCase()=='OBJECT'){config['response_type']=arguments[0].toUpperCase();}}
this.set_persistent_connection=function(){if(typeof arguments[0]=='boolean'){config['persistent_connection']=arguments[0];}}
this.set_use_cpaint_api=function(){if(typeof arguments[0]=='boolean'){config['use_cpaint_api']=arguments[0];}}
function test_ajax_capability(){var cpc=new cpaint_call(0);return cpc.test_ajax_capability();}
this.call=function(){var use_stack=-1;if(config['persistent_connection']==true&&typeof __cpaint_stack[0]=='object'){switch(__cpaint_stack[0].get_http_state()){case-1:use_stack=0;debug('no XMLHttpObject object to re-use for persistence, creating new one later',2);break;case 4:use_stack=0
debug('re-using the persistent connection',2);break;default:debug('the persistent connection is in use - skipping this request',2);}}else if(config['persistent_connection']==true){use_stack=0;__cpaint_stack[use_stack]=new cpaint_call(use_stack);debug('no cpaint_call object available for re-use, created new one',2);}else{use_stack=stack_count;__cpaint_stack[use_stack]=new cpaint_call(use_stack);debug('no cpaint_call object created new one',2);}
if(use_stack!=-1){__cpaint_stack[use_stack].set_debug(config['debugging']);__cpaint_stack[use_stack].set_proxy_url(config['proxy_url']);__cpaint_stack[use_stack].set_transfer_mode(config['transfer_mode']);__cpaint_stack[use_stack].set_async(config['async']);__cpaint_stack[use_stack].set_response_type(config['response_type']);__cpaint_stack[use_stack].set_persistent_connection(config['persistent_connection']);__cpaint_stack[use_stack].set_use_cpaint_api(config['use_cpaint_api']);__cpaint_stack[use_stack].set_client_callback(arguments[2]);if(config['proxy_url']!=''){__cpaint_stack[use_stack].call_proxy(arguments);}else{__cpaint_stack[use_stack].call_direct(arguments);}
stack_count++;debug('stack size: '+__cpaint_stack.length,2);}}
var debug=function(message,debug_level){if(config['debugging']>=debug_level){alert('[CPAINT Debug] '+message);}}}
var __cpaint_stack=new Array();var __cpaint_transformer=new cpaint_transformer();function cpaint_call(){var debugging=false;var httpobj=false;var proxy_url='';var transfer_mode='GET';var async=true;var response_type='OBJECT';var persistent_connection=false;var use_cpaint_api=true;var client_callback;var stack_id=arguments[0];this.set_debug=function(){if(typeof arguments[0]=='number'){debugging=Math.round(arguments[0]);}}
this.set_proxy_url=function(){if(typeof arguments[0]=='string'){proxy_url=arguments[0];}}
this.set_transfer_mode=function(){if(arguments[0].toUpperCase()=='GET'||arguments[0].toUpperCase()=='POST'){transfer_mode=arguments[0].toUpperCase();}}
this.set_async=function(){if(typeof arguments[0]=='boolean'){async=arguments[0];}}
this.set_response_type=function(){if(arguments[0].toUpperCase()=='TEXT'||arguments[0].toUpperCase()=='XML'||arguments[0].toUpperCase()=='OBJECT'){response_type=arguments[0].toUpperCase();}}
this.set_persistent_connection=function(){if(typeof arguments[0]=='boolean'){persistent_connection=arguments[0];}}
this.set_use_cpaint_api=function(){if(typeof arguments[0]=='boolean'){use_cpaint_api=arguments[0];}}
this.set_client_callback=function(){if(typeof arguments[0]=='function'){client_callback=arguments[0];}}
this.get_http_state=function(){var return_value=-1;if(typeof httpobj=='object'){return_value=httpobj.readyState;}
return return_value;}
this.call_direct=function(call_arguments){var url=call_arguments[0];var remote_method=call_arguments[1];var querystring='';var i=0;if(url=='SELF'){url=document.location.href;}
if(use_cpaint_api==true){for(i=3;i<call_arguments.length;i++){querystring+='&cpaint_argument[]='+encodeURIComponent(call_arguments[i]);}
querystring+='&cpaint_response_type='+response_type;if(transfer_mode=='GET'){url=url+'?cpaint_function='+remote_method+querystring;}else{querystring='cpaint_function='+remote_method+querystring;}}else{for(i=3;i<call_arguments.length;i++){if(i==3){querystring+=encodeURIComponent(call_arguments[i]);}else{querystring+='&'+encodeURIComponent(call_arguments[i]);}}
if(transfer_mode=='GET'){url=url+querystring;}}
get_connection_object();debug('opening connection to "'+url+'"',1);httpobj.open(transfer_mode,url,async);if(transfer_mode=="POST"){try{httpobj.setRequestHeader("Content-Type","application/x-www-form-urlencoded");}catch(cp_err){alert('ERROR! POST cannot be completed due to incompatible browser.  Use GET as your request method.');}}
httpobj.onreadystatechange=callback;if(transfer_mode=='GET'){httpobj.send(null);}else{debug('sending query: '+querystring,1);httpobj.send(querystring);}
if(async==false){callback();}}
this.call_proxy=function(call_arguments){var proxyscript=proxy_url;var url=call_arguments[0];var remote_method=call_arguments[1];var querystring='';var i=0;var querystring_argument_prefix='cpaint_argument[]=';if(use_cpaint_api==false){querystring_argument_prefix='';}
for(i=3;i<call_arguments.length;i++){querystring+=encodeURIComponent(querystring_argument_prefix+call_arguments[i]+'&');}
if(use_cpaint_api==true){querystring+=encodeURIComponent('&cpaint_function='+remote_method);querystring+=encodeURIComponent('&cpaint_responsetype='+response_type);}
if(transfer_mode=='GET'){proxyscript+='?cpaint_remote_url='+encodeURIComponent(url)
+'&cpaint_remote_query='+querystring
+'&cpaint_remote_method='+transfer_mode
+'&cpaint_response_type='+response_type;}else{querystring='cpaint_remote_url='+encodeURIComponent(url)
+'&cpaint_remote_query='+querystring
+'&cpaint_remote_method='+transfer_mode
+'&cpaint_response_type='+response_type;}
get_connection_object();debug('opening connection to proxy "'+proxyscript+'"',1);httpobj.open(transfer_mode,proxyscript,async);if(transfer_mode=="POST"){try{httpobj.setRequestHeader("Content-Type","application/x-www-form-urlencoded");}catch(cp_err){alert('[CPAINT Error] POST cannot be completed due to incompatible browser.  Use GET as your request method.');}}
httpobj.onreadystatechange=callback;if(transfer_mode=='GET'){httpobj.send(null);}else{debug('sending query: '+querystring,1);httpobj.send(querystring);}
if(async==false){callback();}}
this.test_ajax_capability=function(){return get_connection_object();}
var get_connection_object=function(){var return_value=false;var new_connection=false;if(persistent_connection==false){debug('Using new connection object',1);new_connection=true;}else{debug('Using shared connection object.',1);if(typeof httpobj!='object'){debug('Getting new persistent connection object.',1);new_connection=true;}}
if(new_connection==true){try{httpobj=new ActiveXObject('Msxml2.XMLHTTP');}catch(e){try{httpobj=new ActiveXObject('Microsoft.XMLHTTP');}catch(oc){httpobj=null;}}
if(!httpobj&&typeof XMLHttpRequest!='undefined'){httpobj=new XMLHttpRequest();}
if(!httpobj){alert('[CPAINT Error] Could not create connection object');}else{return_value=true;}}
if(httpobj.readyState!=4){httpobj.abort();}
return return_value;}
var callback=function(){var response=null;if(httpobj.readyState==4){debug(httpobj.responseText,1);switch(response_type){case'XML':response=__cpaint_transformer.xml_conversion(httpobj.responseXML);break;case'OBJECT':response=__cpaint_transformer.object_conversion(httpobj.responseXML);break;case'TEXT':response=__cpaint_transformer.text_conversion(httpobj.responseText);break;default:alert('[CPAINT Error] invalid response type \''+response_type+'\'');}
if(response!=null&&typeof client_callback=='function'){client_callback(response,httpobj.responseText);}
remove_from_stack();}}
var remove_from_stack=function(){if(typeof stack_id=='number'&&__cpaint_stack[stack_id]&&persistent_connection==false){__cpaint_stack[stack_id]=null;}}
var debug=function(message,debug_level){if(debugging>=debug_level){alert('[CPAINT Debug] '+message);}}}
function cpaint_transformer(){this.object_conversion=function(xml_document){var return_value=new cpaint_result_object();var i=0;var firstNodeName='';if(typeof xml_document=='object'&&xml_document!=null){for(i=0;i<xml_document.childNodes.length;i++){if(xml_document.childNodes[i].nodeType==1){firstNodeName=xml_document.childNodes[i].nodeName;break;}}
var ajax_response=xml_document.getElementsByTagName(firstNodeName);return_value[firstNodeName]=new Array();for(i=0;i<ajax_response.length;i++){var tmp_node=create_object_structure(ajax_response[i]);tmp_node.id=ajax_response[i].getAttribute('id')
return_value[firstNodeName].push(tmp_node);}}else{alert('[CPAINT Error] received invalid XML response');}
return return_value;}
this.xml_conversion=function(xml_document){return xml_document;}
this.text_conversion=function(text){return decode(text);}
var create_object_structure=function(stream){var return_value=new cpaint_result_object();var node_name='';var i=0;var attrib=0;if(stream.hasChildNodes()==true){for(i=0;i<stream.childNodes.length;i++){node_name=decode(stream.childNodes[i].nodeName);node_name=node_name.replace(/[^a-zA-Z0-9]*/g,'');if(typeof return_value[node_name]!='object'){return_value[node_name]=new Array();}
if(stream.childNodes[i].nodeType==1){var tmp_node=create_object_structure(stream.childNodes[i]);for(attrib=0;attrib<stream.childNodes[i].attributes.length;attrib++){tmp_node.set_attribute(decode(stream.childNodes[i].attributes[attrib].nodeName),decode(stream.childNodes[i].attributes[attrib].nodeValue));}
return_value[node_name].push(tmp_node);}else if(stream.childNodes[i].nodeType==3){return_value.data=decode(stream.firstChild.data);}}}
return return_value;}
var decode=function(rawtext){var plaintext='';var i=0;var c1=0;var c2=0;var c3=0;var u=0;var t=0;while(i<rawtext.length){if(rawtext.charAt(i)=='\\'&&rawtext.charAt(i+1)=='u'){u=0;for(j=2;j<6;j+=1){t=parseInt(rawtext.charAt(i+j),16);if(!isFinite(t)){break;}
u=u*16+t;}
plaintext+=String.fromCharCode(u);i+=6;}else{plaintext+=rawtext.charAt(i);i++;}}
if(plaintext!==''&&!isNaN(plaintext)&&isFinite(plaintext)){plaintext=Number(plaintext);}
return plaintext;}}
function cpaint_result_object(){this.id=0;this.data=0;var __attributes=new Array();this.find_item_by_id=function(){var return_value=null;var type=arguments[0];var id=arguments[1];var i=0;if(this[type]){for(i=0;i<this[type].length;i++){if(this[type][i].get_attribute('id')==id){return_value=this[type][i];break;}}}
return return_value;}
this.get_attribute=function(){var return_value=null;var id=arguments[0];if(typeof __attributes[id]!='undefined'){return_value=__attributes[id];}
return return_value;}
this.set_attribute=function(){__attributes[arguments[0]]=arguments[1];}}