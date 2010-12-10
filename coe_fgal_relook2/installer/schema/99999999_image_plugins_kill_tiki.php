<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $tikilib, $prefs, $tikiroot, $user_overrider_prefs, $tiki_p_trust_input;	// globals are required here for tiki-setup_base.php
include_once('tiki-setup_base.php');

// ABOUT THE NUMBERING:
//
// Because this script calls tiki-setup_base.php , which does very
// complicated things like checking if users are logged in and so
// on, this script depends on every other script, because
// tiki-setup_base.php does.


function upgrade_99999999_image_plugins_kill_tiki( $installer ) {
	global $tikilib, $installer;

	include_once ('lib/profilelib/profilelib.php');
	include_once ('lib/profilelib/installlib.php');

	// ******************************** THUMB plugin
	$plugstring = <<<PLUGINTEXT
{CODE(caption=>YAML,wrap=1)}
objects:
 -
  type: plugin_alias
  ref: combine_thumb
  data:
   name: THUMB
   implementation: img
   description:
    name: Thumbnail
    documentation: PluginThumb
    description: Displays a thumbnail of an image that enlarges upon mouseover or links to a target
    prefs:
    params:
     file:
      required: false
      name: File ID
      description: File ID from the file gallery.
      filter: digits
     id:
      required: false
      name: Image ID
      description: Image ID from the image gallery.
      filter: digits
     image:
      required: false
      name: Image URL
      description: URL to the image.
      filter: url
     max:
      required: false
      name: Maximum Size
      description: Maximum width or height for the image.
      filter: int
     float:
      required: false
      name: Alignment
      description: Set alignment as left, right or none.
      filter: alpha
      options:
       none:
        text: None
       left:
        text: Left
       right:
        text: Right
     url:
      required: false
      name: Link Target
      description: Link target of the image.
      filter: url
   body:
    default: ''
   params:
    fileId:
     pattern: %file%
     params:
      file:
       token: file
    id:
     pattern: %id%
     params:
      id:
       token: id
    src:
     pattern: %image%
     params:
      image:
       token: image
    max:
     pattern: %max%
     params:
      max:
       token: max
    imalign:
     pattern: %float%
     params:
      float:
       token: float
    link:
     pattern: %url%
     params:
      url:
       token: url
    thumb: mouseover
{CODE}
PLUGINTEXT;
	
	$profile_installer = new Tiki_Profile_Installer;
	$profile = Tiki_Profile::fromString($plugstring, 'THUMB');
	$profile->removeSymbols();
	$profile_installer->install($profile);
	
	// ********************************  IMAGE plugin
	$plugstring = <<<PLUGINTEXT
{CODE(caption=>YAML,wrap=1)}
objects:
 -
  type: plugin_alias
  ref: combine_image
  data:
   name: IMAGE
   implementation: img
   description:
    name: Image
    documentation: PluginImage
    description: Display images (transitional alias, use IMG plugin instead)
    params:
     fileId:
      required: false
      name: File ID
      description: Numeric ID of an image in a File Gallery (or comma-separated list). "fileId", "id" or "src" required.
     id:
      required: false
      name: Image ID
      description: Numeric ID of an image in an Image Gallery (or comma-separated list). "fileId", "id" or "src" required.
     src:
      required: false
      name: Image source
      description: Full URL to the image to display. "fileId", "id" or "src" required.
      filter: url
     scalesize:
      required: false
      name: Maximum size
      description: Maximum width or height for the image in pixels.
     height:
      required: false
      name: Image height
      description: Height in pixels.
      filter: int
     width:
      required: false
      name: Image width
      description: Width in pixels.
      filter: int
     link:
      required: false
      name: Link
      description: For making the image a hyperlink. Enter a url to the page the image should link to.
      filter: url
     rel:
      required: false
      name: Link relation
      description: Link relation attribute to add to the link.
     title:
      required: false
      name: Link title
      description: Link title that appears upon mouseover.
     alt:
      required: false
      name: Alternate text
      description: Alternate text that displays image doesn't load.
     align:
      required: false
      name: Align image block
      description: Enter right, left or center to align the box containing the image.
      options:
       left:
        text: Left
       right:
        text: Right
       center:
        text: Center
     block:
      required: false
      name: Wrapping control
      description: Whether to block items from wrapping next to image from the top or bottom. (top,bottom,both,none)
      options:
       '':
        text: None
       top:
        text: Top
       bottom:
        text: Bottom
       both:
        text: Both
     desc:
      required: false
      name: Description
      description: Image caption
     usemap:
      required: false
      name: Image map
      description: Name of the image map to use.
     class:
      required: false
      name: CSS class
      description: CSS class to apply to the image.
     style:
      required: false
      name: CSS syle
      description: CSS styling to apply.
     border:
      required: false
      name: Border options
      description: Border configuration for image block.
     descoptions:
      required: false
      name: Caption style
      description: Styling of image description. Use CSS syntax to override default setting.
     default:
      required: false
      name: Default configuration
      description: Default configuration definitions (usually set by admin).
     mandatory:
      required: false
      name: Mandatory configuration
      description: Mandatory configuration definitions (usually set by admin).
   body:
    default: ''
   params:
    fileId:
     pattern: %fileId%
     params:
      fileId:
       token: fileId
    id:
     pattern: %id%
     params:
      id:
       token: id
    src:
     pattern: %src%
     params:
      src:
       token: src
    max:
     pattern: %scalesize%
     params:
      scalesize:
       token: scalesize
       default: 200
    height:
     pattern: %height%
     params:
      height:
       token: height
    width:
     pattern: %width%
     params:
      width:
       token: width
    link:
     pattern: %link%
     params:
      link:
       token: link
    rel:
     pattern: %rel%
     params:
      rel:
       token: rel
    title:
     pattern: %title%
     params:
      title:
       token: title
    alt:
     pattern: %alt%
     params:
      alt:
       token: alt
    align:
     pattern: %align%
     params:
      align:
       token: align
    block:
     pattern: %block%
     params:
      block:
       token: block
    desc:
     pattern: %desc%
     params:
      desc:
       token: desc
    usemap:
     pattern: %usemap%
     params:
      usemap:
       token: usemap
    class:
     pattern: %class%
     params:
      class:
       token: class
    stylebox:
     pattern: %style%
     params:
      style:
       token: style
       default: border:3px double;padding:.1cm; font-size:12px; line-height:1.5em; margin-left:4px; width:200px;
    styledesc:
     pattern: %descoptions%
     params:
      descoptions:
       token: descoptions
    default:
     pattern: %default%
     params:
      default:
       token: default
    mandatory:
     pattern: %mandatory%
     params:
      mandatory:
       token: mandatory
    button: y 
    imalign: center  
{CODE}
PLUGINTEXT;
	
	$profile_installer = new Tiki_Profile_Installer;
	$profile = Tiki_Profile::fromString($plugstring, 'IMAGE');
	$profile->removeSymbols();
	$profile_installer->install($profile);
	
	// ********************************  PICTURE plugin
	$plugstring = <<<PLUGINTEXT
{CODE(caption=>YAML,wrap=1)}
objects:
 -
  type: plugin_alias
  ref: combine_picture
  data:
   name: PICTURE
   implementation: img
   description:
    name: Picture
    description: Display uploaded pictures
    params:
     file:
      required: true
      name: File path
      description: File name or path of the image.
   body:
    default: ''
   params:
    src:
     pattern: %file%
     params:
      file:
       token: file
{CODE}
PLUGINTEXT;
	
	$profile_installer = new Tiki_Profile_Installer;
	$profile = Tiki_Profile::fromString($plugstring, 'PICTURE');
	$profile->removeSymbols();
	$profile_installer->install($profile);
	
}

