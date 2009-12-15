<?php
// ##################################################################################
// Title                     : Class RSS_parser
// Version                   : 1.0
// Author                    : Luis Argerich (lrargerich@yahoo.com)
// Last modification date    : 06-19-2002
// Description               : A class to parse RSS 1.0 documents.
//                             Can parse documents from files or URIs.
//                             Based in a true RDF parser
//                             so it will respet any valid RDF syntax describing RSS
//                             files.
// ##################################################################################
// History:
// 07-17-2002                : BugFixes (Leandro Mariano Lopez)
// 06-19-2002                : First version of this class.
// ##################################################################################
// To-Dos:
//
// ##################################################################################
// How to use it:
// Read the documentation in rss_parser.html
// ##################################################################################

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (defined("_class_rss_parser_is_included")) {
  return;
  // do nothing since the class is already included
}

define("_class_rss_parser_is_included",1);

include_once("class_rdf_parser.php");

class RSS_parser {
  var $channel_handler_name;
  var $item_handler_name;
  var $items_suject='';
  var $channel_subject='';
  var $items=Array();
  var $unreported_item=false;
  var $channel_properties=Array();
  var $item_properties=Array();
  var $channel_image=Array();
  var $channel_textinput=Array();

  function get_channel_data() {
    // Return channel properties
    return $this->channel_properties;
  }

  function get_channel_image_data() {
    // Return channel_image
    return $this->channel_image;
  }

  function get_channel_textinput_data() {
    // Return channel_textinput
    return $this->channel_textinput;
  }

  function get_items_data() {
    // Return item properties
    return $this->item_properties;
  }

  function rss_parse($rss) {
    $base=$rss;
    $input = fopen($base,"r");
    if(!$input) {
      return false;
    }
    $rdf=new Rdf_parser();
    $rdf->rdf_parser_create( NULL );
    $rdf->rdf_set_user_data( $statements );
    // Here we'd have to use a rdf_set_object and then call the object method if set...
    $rdf->rdf_set_user_data($this);
    $rdf->rdf_set_statement_handler( "my_statement_handler" );
    $rdf->rdf_set_warning_handler("my_warning_handler" );
    $rdf->rdf_set_base($base );
    $done=false;
    while(!$done) {
      $buf = fread( $input, 512 );
      $done = feof($input);
      if ( ! $rdf->rdf_parse( $buf, strlen($buf), feof($input) ) )
      {
        printf(
    "**** ERROR **** : %s at line %s",
    print( xml_get_error_code( $rdf->rdf_get_xml_parser() ) ),
    print( xml_get_current_line_number($rdf->rdf_get_xml_parser() ) ) );
    return false;
      }
    }
    /* close file. */
    fclose( $input );
    $rdf->rdf_parser_free();
    return true;
  }

  function rss_parse_data($data) {
  
    $rdf=new Rdf_parser();
    $rdf->rdf_parser_create( NULL );
    $rdf->rdf_set_user_data( $statements );
    // Here we'd have to use a rdf_set_object and then call the object method if set...
    $rdf->rdf_set_user_data($this);
    $rdf->rdf_set_statement_handler( "my_statement_handler" );
    $rdf->rdf_set_warning_handler("my_warning_handler" );
    $rdf->rdf_set_base('');
    $done=false;
    
      if ( ! $rdf->rdf_parse( $data, strlen($data), true ) )
      {
        printf(
    "**** ERROR **** : %s at line %s",
    print( xml_get_error_code( $rdf->rdf_get_xml_parser() ) ),
    print( xml_get_current_line_number($rdf->rdf_get_xml_parser() ) ) );
    return false;
      }
    $rdf->rdf_parser_free();
    return true;
  }

}

/* handlers */
function my_statement_handler(
    &$user_data,
    $subject_type,
    $subject,
    $predicate,
    $ordinal,
    $object_type,
    $object,
    $xml_lang )
{
    //$statements = $user_data;

        // If we found the channel then set up the channel uri
        if( ($predicate=="http://www.w3.org/1999/02/22-rdf-syntax-ns#type") && ($object=="http://purl.org/rss/1.0/channel") && ($subject_type != RDF_SUBJECT_TYPE_ANONYMOUS)) {
          $user_data->channel_subject=$subject;
        }

        // If we found an RSS property of the channel then add it to the array of channel info
        if ( $subject==$user_data->channel_subject && strstr($predicate,"http://purl.org/rss/1.0/")) {
          $props=explode("/",$predicate);
          $prop=array_pop($props);
          $user_data->channel_properties[$prop]=$object;
        }

        // If we found a member of the Seq resource add it to the items array
        if(isset($user_data->items_subject) && $subject == $user_data->items_subject && $subject_type == RDF_SUBJECT_TYPE_ANONYMOUS) {
          $user_data->items[$ordinal]=$object;
          $user_data->item_properties[$object]=Array();

        }

        // If we found the seq element then we know the anonymous id that the Seq resource will have
        if( ($predicate=="http://www.w3.org/1999/02/22-rdf-syntax-ns#type") && ($object=="http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq")) {
          // Report the channel first
          $user_data->items_subject=$subject;
          // Save the subject as the subject that will list all the items that we'll be described later
        }

        // If we found a resource in the list of items then add the property to the array of properties
        // if it is a RSS property
        // If we had an unreported item then call the handler for the item
        if(in_array($subject,$user_data->items) && strstr($predicate,"http://purl.org/rss/1.0/")) {
          $props=explode("/",$predicate);
          $prop=array_pop($props);
          $user_data->item_properties[$subject][$prop]=$object;
        }

        // If we found the textinput element of the channel
        if(isset($user_data->channel_properties["image"]) && $subject == $user_data->channel_properties["image"] && strstr($predicate, "http://purl.org/rss/1.0/")) {
          $props=explode("/",$predicate);
          $prop=array_pop($props);
          $user_data->channel_image[$prop]=$object;
        }

        // If we found the image element of the the channel
        if(isset($user_data->channel_properties["textinput"]) && $subject == $user_data->channel_properties["textinput"] && strstr($predicate, "http://purl.org/rss/1.0/")) {
          $props=explode("/",$predicate);
          $prop=array_pop($props);
          $user_data->channel_textinput[$prop]=$object;
        }

}

function my_warning_handler($warning )
{
    printf( "**** WARNING **** : %s<br />", $warning );
}

?>
