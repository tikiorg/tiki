<?php

// HAWIKI
// PHP wrapper class for HAWHAW-based wiki's
// Copyright (C) 2005 Norbert Huffschmid & Tikiwiki community
// Last modified: 28. June 2005
//
// This library is free software; you can redistribute it and/or modify it under the
// terms of the GNU Library General Public License as published by the Free Software
// Foundation; either version 2 of the License, or (at your option) any later
// version.
//
// This library is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the GNU Library General Public License for more details.
// http://www.gnu.org/copyleft/lgpl.html
//
// This software is BETA STAGE!!!
//
// This library requires hawiki_cfg.inc and hawiki_parser.inc
//
// List of supported/workarounded wiki elements:
//
// plain text
// non-parsed sections
// bold text __bold__
// italic text ''italic''
// underlined text ===underline===
// colored text ~~#0000FF:colored~~
// monospaced BLANK text (workaround: small)
// headings ! !! !!!
// numbered lists
// paragraphs -= paragraph =-
// WikiNames
// ext:WikiNames
// links
// tables
// textbox ^box^
// horizontal rules ----
// images
// smileys
// plugins (for HTML browsers only)

require_once("lib/hawhaw/hawiki_cfg.inc");
require_once("lib/hawhaw/hawiki_parser.inc");

// internal trace facilities for debugging
define("HAWIKI_DEBUG_NONE",   0);
define("HAWIKI_DEBUG_PARSER", 1);

// definitions for wiki navigation
define("HAWIKI_NAVLINK_TOP",    1); // create link at top of wiki
define("HAWIKI_NAVLINK_BOTTOM", 2); // create link at bottom of wiki


function HAWIKI_error($error_msg)
{
  // throw out error message via HAWHAW and terminate

  $error_deck = new HAW_deck(HAWIKI_TITLE);
  $error_deck->enable_session();

  $error_text = new HAW_text($error_msg);
  $error_deck->add_text($error_text);
  $error_deck->create_page();
  exit();
}


function HAWIKI_specchar($string)
{
  // special character handling
  // utf8_decode and reconvert HTML entities (e.g. &uuml; ==> �)

  $trans = get_html_translation_table(HTML_ENTITIES);
  $trans = array_flip ($trans);
  return(strtr(utf8_decode($string), $trans));
}


function HAWIKI_unhtmlentities($string)
{
  // special character hnadling without utf8-decoding (see above)
  // &amp; -> &, �->� (will be destroyed by utf8_decode)
  $trans = get_html_translation_table (HTML_ENTITIES);
  $trans = array_flip ($trans);
  return(strtr($string, $trans));
}


class HAWIKI_page
{
  var $deck;          // HAW_deck object
  var $wiki_string;   // input in wiki syntax
  var $wiki_base;     // URL for WikiName links
  var $title;         // page title
  var $navlink;       // array with navigation links (home, menue etc.)
  var $smiley_dir;    // where smiley gif's are stored
  var $link_jingle;   // where link wav file for VoiceXML is located
  var $hawimconv;     // where HAWHAW image converter script is located
  var $firstColumn;   // flag to determine parser position
  var $listLine;      // flag to indicate that line belongs to a list
  var $listNumbering; // array with current list numbers
  var $debugflags;    // flags for dumping debug info
  var $textformat;    // text format: bold, italic etc.
  var $textcolor;     // text color

  function HAWIKI_page($input, $wiki_base, $title, $lang)
  {
    $this->wiki_string = utf8_decode($input);
    $this->wiki_base = $wiki_base;
    $this->title = HAWIKI_specchar($title);
    $this->smiley_dir = "";       // has to be set later by function call
    $this->link_jingle = "";      // dito
    $this->hawimconv = "";        // dito
    $this->navlink = array();
    $this->firstColumn = true;
    $this->listLine = false;
    $this->listNumbering = array();
    $this->debugflags = HAWIKI_DEBUG_NONE;
    $this->textformat = HAW_TEXTFORMAT_NORMAL;
    $this->textcolor = "";

    // check input string for presence resp. maximum length
    if (strlen($input) > HAWIKI_MAXINPUTCHARS)
    {
      // invalid input size
      HAWIKI_error("Error: Invalid wiki size"); // script end
    }

    $this->deck = new HAW_deck(HAWIKI_TITLE);
    HAWTIKI_deck_init($this->deck);
    $this->deck->set_width(HAWIKI_DISP_WIDTH);
    $this->deck->set_height(HAWIKI_DISP_HEIGHT);
    $this->deck->set_disp_bgcolor(HAWIKI_DISP_BGCOLOR);

    if (isset($lang) && (strlen($lang) == 2))
    {
      $this->deck->set_language($lang);

      if ($this->deck->ml == HAW_VXML)
      {
        global $language; // needed for tra(nslation) to work properly
        $language = $lang;

        if ($lang == "en")
          $this->deck->set_voice_nomatch("I did not understand what you said, please try again.");

        if ($lang == "fr")
          $this->deck->set_voice_nomatch("Je n'ai pas compris ce que vous avez dit. SVP essayer de nouveau.");

        if ($lang == "es")
          $this->deck->set_voice_nomatch("No comprendo lo que tu dices. Por favor, intenta otra vez.");

        if ($lang == "de")
          $this->deck->set_voice_nomatch("Ich habe Sie leider nicht verstanden. Bitte wiederholen Sie.");
      }
    }
  }


  function display()
  {
    // convert wiki text into HAWHAW object structure and create appropriate markup

    if (isset($_GET["hawikidebugremote"]))
      $url_debugflags = $_GET["hawikidebugremote"];
    else
      $url_debugflags = HAWIKI_DEBUG_NONE;
    
    if ($url_debugflags && ($this->debugflags == HAWIKI_DEBUG_NONE))
      $this->debugflags = $url_debugflags;



    if ($this->debugflags & HAWIKI_DEBUG_PARSER)
      printf("<b>Input:</b><br><pre>%s</pre><br>\n", $this->wiki_string);

    if ($this->title)
    {
      $title = new HAW_text($this->title, HAW_TEXTFORMAT_BIG | HAW_TEXTFORMAT_BOXED);
      $this->deck->add_text($title);
    }

    $link_counter = 0;
    foreach ($this->navlink as $value)
    {
      // create navigation link(s) at top of wiki page

      if ($value["pos"] & HAWIKI_NAVLINK_TOP)
      {
        $link = new HAW_link($value["label"], $value["url"]);
        $this->deck->add_link($link);
        $link_counter++;
      }
    }

    if (($link_counter > 0) || $this->title)
    {
      // create horizontal rule between page header and wiki content
      $rule = new HAW_rule();
      $this->deck->add_rule($rule);
    }

    $input = strip_tags($this->wiki_string); // remove all HTML

    while ($po = HAWIKI_parser($input, $this->firstColumn)) // evaluate parser output
    {
      $this->firstColumn = false; // default

      switch ($po["token"])
      {
        case HAWIKI_TOKEN_DUMMY:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Dummy token</b><br>\n");

          // unsupported syntax element ==> do nothing

          break;
        }

        case HAWIKI_TOKEN_NEWLINE:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Newline:</b> %d<br>\n", $po["count"]);

          $text = new HAW_text("");
          $text->set_br($po["count"]); // set number of line breaks
          $this->deck->add_text($text);
          $this->firstColumn = true;    // now we're again in column 0

          // numbered list are terminated by all lines, that are not list elements
          if ($this->listLine)
            $this->listLine = false; // continuation of numbered list is possible
          else
            $this->listNumbering = array(); // init numbering array

          // reset text formatting
          $this->textformat = HAW_TEXTFORMAT_NORMAL;

          break;
        }

        case HAWIKI_TOKEN_TEXT:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Text:</b> %s<br>\n", $po["text"]);

          if (strlen($po["text"]) > 0)
          {
            $text = new HAW_text(HAWIKI_unhtmlentities($po["text"]), $this->textformat);
            
            if ($this->textcolor)
              $text->set_color($this->textcolor);

            $text->set_br(0);
            $this->deck->add_text($text);
          }

          break;
        }

        case HAWIKI_TOKEN_BOLD:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Toggle bold</b><br>\n");

          $this->textformat ^= HAW_TEXTFORMAT_BOLD;

          break;
        }

        case HAWIKI_TOKEN_ITALIC:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Toggle italic</b><br>\n");

          $this->textformat ^= HAW_TEXTFORMAT_ITALIC;

          break;
        }

        case HAWIKI_TOKEN_UNDERLINE:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Toggle underline</b><br>\n");

          $this->textformat ^= HAW_TEXTFORMAT_UNDERLINE;

          break;
        }

        case HAWIKI_TOKEN_COLORED:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Colored: %s</b><br>\n", $po["color"]);

          $this->textcolor = $po["color"];

          break;
        }

        case HAWIKI_TOKEN_MONOSPACED:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Monospaced</b><br>\n");

          // monospace is not supported by HAWHAW -> draw small instead
          $this->textformat |= HAW_TEXTFORMAT_SMALL;

          break;
        }

        case HAWIKI_TOKEN_PARAGRAPH:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Paragraph:</b> %s<br>\n", $po["text"]);

          $paragraph = new HAW_text($po["text"], HAW_TEXTFORMAT_BIG | HAW_TEXTFORMAT_BOXED);
          $paragraph->set_br(0);
          $this->deck->add_text($paragraph);

          break;
        }

        case HAWIKI_TOKEN_HEADING:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Heading (level %d)</b><br>\n", $po["level"]);

          $this->textformat |= HAW_TEXTFORMAT_BOLD;

          if ($po["level"] <= 2)
            $this->textformat |= HAW_TEXTFORMAT_BIG;

          if ($po["level"] == 1)
            $this->textformat |= HAW_TEXTFORMAT_UNDERLINE;

          break;
        }

        case HAWIKI_TOKEN_BULLIST:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Bullet list (level %d)</b><br>\n", $po["level"]);

          $bullet = str_repeat("*", $po["level"]);

          $text = new HAW_text($bullet . " ", HAW_TEXTFORMAT_BOLD);
          $text->set_br(0);
          $this->deck->add_text($text);

          break;
        }

        case HAWIKI_TOKEN_NUMLIST:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Numbered list (level %d)</b><br>\n", $po["level"]);

          if (isset($this->listNumbering[$po["level"]]))
            $this->listNumbering[$po["level"]]++; // increment this level
          else
            $this->listNumbering[$po["level"]] = 1; // init this level

          $number = "";

          for ($n=1; $n <= $po["level"]; $n++)
          {
            // assemble current numbering
            if (isset($this->listNumbering[$n]))
              $number .= sprintf("%d.", $this->listNumbering[$n]);
            else
              $number .= "0.";
          }

          $number = substr($number, 0, -1); // remove trailing dot

          $text = new HAW_text($number . " ", HAW_TEXTFORMAT_BOLD);
          $text->set_br(0);
          $this->deck->add_text($text);

          $this->listLine = true;

          break;
        }

        case HAWIKI_TOKEN_RULE:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Horizontal rule</b><br>\n");

          $rule = new HAW_rule();
          $this->deck->add_rule($rule);
          $this->firstColumn = true;    // now we're again in column 0

          break;
        }

        case HAWIKI_TOKEN_WIKINAME:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>WikiName:</b> extwiki:%s page:%s label:%s dtmf:%s<br>\n",
                   $po["extwiki"], $po["page"], $po["label"], $po["dtmf"]);
          
          if ($po["extwiki"])
          {
            $link = new HAW_link($po["label"], ereg_replace('\$page', $po["page"], $po["extwiki"]) . "&mode=mobile");
          }
          else
            $link = new HAW_link($po["label"], $this->wiki_base . $po["page"]);

          if (isset($po["dtmf"]))
            // assign DTMF key
            $link->set_voice_dtmf($po["dtmf"]);

          $link->set_br(0);
          $this->deck->add_link($link);

          break;
        }

        case HAWIKI_TOKEN_LINK:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Link:</b> url:%s label:%s<br>\n", $po["url"], $po["label"]);

          // determine file extension of link
          if (preg_match("/\.(\w*)$/", $po["url"], $matches))
            $extension = strtolower($matches[1]);

          if (($this->deck->ml == HAW_VXML) && isset($extension) &&
              (($extension == "wav") || ($extension == "mp3")))
          {
            // play audio file on VoiceXML browser instead of creating link
            $introtext = new HAW_text($po["label"]);
            $this->deck->add_text($introtext);
            $audio = new HAW_text("");
            $audio->set_voice_text("Sorry, this voice browser does not support " . $extension . " format", HAWIKI_specchar($po["url"]));
            $this->deck->add_text($audio);
          }
          else
          {
            // mark as external link
            if ($po["ext"])
            {
              $el_image = new HAW_image(HAWIKI_EXTLINK_ICON . ".wbmp",
                                        HAWIKI_EXTLINK_ICON . ".gif", $po["extlink_text"]);
              $el_image->set_voice_text($po["extlink_text"]);
              $this->deck->add_image($el_image);
            }

            // create link
            $link = new HAW_link($po["label"], HAWIKI_specchar($po["url"]));
            $link->set_br(0);
            $this->deck->add_link($link);
          }

          break;
        }

        case HAWIKI_TOKEN_TABLE:
        {
          // format of "text":
          // colums are seperated by '|' (this is pipe, not slash!)
          // rows are seperated by '\n'

          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Table:</b> %s<br>\n", $po["text"]);

          $table = new HAW_table();
          $table_arr = explode("\n", $po["text"]);

          while (list($key1, $val1) = each($table_arr))
          {
            $row = new HAW_row();
            $row_arr = explode("|", $val1);

            while (list($key2, $val2) = each($row_arr))
            {
              $text = new HAW_text($val2);
              $text->set_br(0);
              $row->add_column($text);
            }

            $table->add_row($row);
          }

          $this->deck->add_table($table);

          break;
        }

        case HAWIKI_TOKEN_IMAGE:
        {
          global $tikidomain;
          
          if ($tikidomain)
            $po["src"] = preg_replace("~img/wiki_up/~","img/wiki_up/$tikidomain/", $po["src"]);

          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Image:</b> src:%s alt:%s link:%s<br>\n", $po["src"], $po["alt"], $po["link"]);

          if ($po["src"])
          {
            if ($this->hawimconv)
            {
              // HAWHAW image converter available

              if (preg_match("/^htt/", $po["src"]))
              {
                // full qualified url ==> use unchanged
                $img_wbmp = sprintf("%s?img=%s", $this->hawimconv, urlencode($po["src"]));
              }
              elseif(preg_match("/^\//", $po["src"]))
              {
                // absolute url ==> append filename to document root
                $img_wbmp = sprintf("%s?img=%s", $this->hawimconv,
                                    urlencode($_SERVER["DOCUMENT_ROOT"] . $po["src"]));

              }
              else
              {
                // relative url ==> append filename to working directory
                $img_wbmp = sprintf("%s?img=%s", $this->hawimconv,
                                    urlencode(getcwd() . "/" . $po["src"]));
              }
            }
            else
            {
              // no image converter available ==> rely on available wbmp image ...
              $img_wbmp = ereg_replace(".gif$|.png$|.jpg$", ".wbmp", $po["src"]);
            }

            if (isset($po["alt"]) && ($po["alt"] != ""))
              $img_alt = $po["alt"];
            else
              $img_alt = $po["src"]; // set alternative text to src, if not available

            $image = new HAW_image($img_wbmp, $po["src"], $img_alt);
            $this->deck->add_image($image);
          }

          if (isset($po["link"]) && $po["link"] && ($this->deck->ml != HAW_VXML))
          {
            // HAWHAW does not support images as links
            // ==> draw @-link next to image (for visual browsers)
            $po["link"] = ereg_replace("&amp;", "&", $po["link"]); // &-treatment is done by HAWHAW
            $link = new HAW_link("@", $po["link"]);
            $link->set_br(0);
            $this->deck->add_link($link);
          }

          break;
        }

        case HAWIKI_TOKEN_SMILEY:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Smiley:</b> %s<br>\n", $po["text"]);

          if ($this->smiley_dir)
          {
            // smiley directory has been set by the user

            $img_html = $this->smiley_dir . "/icon_" . $po["text"] . ".gif";
            $img_wbmp = $this->smiley_dir . "/icon_" . $po["text"] . ".wbmp";
            $img_alt = ":)";
            $image = new HAW_image($img_wbmp, $img_html, $img_alt);
            $image->use_localsrc("smileyface");
            $this->deck->add_image($image);
          }

          break;
        }

        case HAWIKI_TOKEN_PLUGIN:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Plugin:</b> %s<br>\n", htmlspecialchars($po["html"]));

          if ($po["html"])
          {
            // plugins return pure HTML ==> show on HTML browsers only
            $rawhtml = new HAW_raw(HAW_HTML, $po["html"]);
            $this->deck->add_raw($rawhtml);
          }

          break;
        }

        default:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Unknown token:</b> %d<br>\n", $po["token"]);

          break;
        }
      }
    }

    if (strlen($input) > 0)
    {
      // parsing aborted due to syntax error
      // ==> terminate output with error message

      $newline = new HAW_text("");
      $this->deck->add_text($newline);
      $error_text = new HAW_text("Error: Invalid wiki syntax");
      $this->deck->add_text($error_text);
    }

    $link_counter = 0;
    foreach ($this->navlink as $value)
    {
      // create navigation link(s) at bottom of wiki page

      if ($value["pos"] & HAWIKI_NAVLINK_BOTTOM)
      {
        if ($link_counter == 0)
        {
          // create horozontal rule between wiki content and 1st link
          $rule = new HAW_rule();
          $this->deck->add_rule($rule);
        }

        $link = new HAW_link($value["label"], $value["url"]);
        $this->deck->add_link($link);
        $link_counter++;
      }
    }

    // set jingle for VoiceXML links
    if ($this->link_jingle)
    {
      // jingle has been set by hawiki user
      $this->deck->set_voice_jingle($this->link_jingle);
    }

    // smooth voice sections
    $this->deck->smooth_voice();

    // output appropriate markup language
    $this->deck->create_page();
  }


  function set_navlink($label, $url, $pos)
  {
    // define navigation link on top and/or bottom of wiki page
    // label: link text
    // url:   link url
    // pos:   HAWIKI_NAVLINK_TOP    (link is created at top of wiki page)
    //        HAWIKI_NAVLINK_BOTTOM (link is created at bottom of wiki page)
    //        HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM (top AND bottom)

    $arr["label"] = HAWIKI_specchar($label);
    $arr["url"]   = $url;
    $arr["pos"]   = $pos;

    $this->navlink[] = $arr; // enhance array with navigation links
  }


  function set_smiley_dir($dir)
  {
    // set directory where smileys are stored
    $this->smiley_dir = $dir;
  }


  function set_link_jingle($url)
  {
    // set link wav-file for VoiceXML
    $this->link_jingle = $url;
  }


  function set_hawimconv($url)
  {
    // determine HAWHAW image conversion script
    $this->hawimconv = $url;
  }


  function set_debugflags($flags)
  {
    $this->debugflags = $flags;
  }
};

?>
