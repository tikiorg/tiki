<?php

// HAWIKI
// PHP wrapper class for HAWHAW-based wiki's
// Copyright (C) 2003 Norbert Huffschmid & TikiWiki community
// Last modified: 5. July 2003
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
// This software is EARLY BETA STAGE!!!
//
// This library requires hawiki_cfg.inc and hawiki_parser.inc
//
// List of supported/workarounded wiki elements:
//
// plain text
// non-parsed sections
// bold text __bold__
// italic text ''italic''
// colored text ~~#0000FF:colored~~ (workaround: bold)
// monospaced BLANK text (workaround: small)
// headings ! !! !!!
// numbered lists
// paragraphs -= paragraph =-
// WikiNames
// links
// tables
// textbox ^box^
// horizontal rules ----
// images
// smileys
// plugins (for HTML browsers only)


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

  $error_text = new HAW_text(HAWIKI_ERROR . " " . $error_msg);
  $error_deck->add_text($error_text);
  $error_deck->create_page();
  exit();
}


function reconvert_html_entities($string)
{
  // convert html entities like &uuml; back to ü

  $trans = get_html_translation_table(HTML_ENTITIES);
  $trans = array_flip ($trans);
  return(strtr($string, $trans));
}


class HAWIKI_page
{
  var $deck;          // HAW_deck object
  var $wiki_string;   // input in wiki syntax
  var $wiki_base;     // URL for WikiName links
  var $extwiki;       // array with external wiki declaration
  var $navlink;       // array with navigation links (home, menue etc.)
  var $smiley_dir;    // where smiley gif's are stored
  var $link_jingle;   // where link wav file for VoiceXML is located
  var $hawimconv;     // where HAWHAW image converter script is located
  var $firstColumn;   // flag to determine parser position
  var $listLine;      // flag to indicate that line belongs to a list
  var $listNumbering; // array with current list numbers
  var $debugflags;    // flags for dumping debug info
  var $textformat;    // text format: bold, italic etc.

  function HAWIKI_page($input, $wiki_base)
  {
    $this->wiki_string = utf8_decode($input);
    $this->wiki_base = $wiki_base;
    $this->smiley_dir = "";       // has to be set later by function call
    $this->link_jingle = "";      // dito
    $this->hawimconv = "";        // dito
    $this->extwiki = array();
    $this->navlink = array();
    $this->firstColumn = true;
    $this->listLine = false;
    $this->listNumbering = array();
    $this->debugflags = HAWIKI_DEBUG_NONE;
    $this->textformat = HAW_TEXTFORMAT_NORMAL;

    // check input string for presence resp. maximum length
    if ((strlen($input) == 0) || (strlen($input) > HAWIKI_MAXINPUTCHARS))
    {
      // invalid input size
      HAWIKI_error(HAWIKI_ERROR_INVWIKISIZE); // script end
    }

    $this->deck = new HAW_deck(HAWIKI_TITLE);
    $this->deck->set_width(HAWIKI_DISP_WIDTH);
    $this->deck->set_height(HAWIKI_DISP_HEIGHT);
    $this->deck->set_disp_bgcolor(HAWIKI_DISP_BGCOLOR);
  }


  function display()
  {
    // convert wiki text into HAWHAW object structure and create appropriate markup

    // remove remote debugging in stable versions !!!
//    global $HTTP_GET_VARS;
//    $url_debugflags = $HTTP_GET_VARS["hawikidebugremote"];

    $url_debugflags = $_GET["hawikidebugremote"];
    if ($url_debugflags && ($this->debugflags == HAWIKI_DEBUG_NONE))
      $this->debugflags = $url_debugflags;



    if ($this->debugflags & HAWIKI_DEBUG_PARSER)
      printf("<b>Input:</b><br><pre>%s</pre><br>\n", $this->wiki_string);

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

    if ($link_counter > 0)
    {
      // create horizontal rule between link(s) and wiki content
      $rule = new HAW_rule();
      $this->deck->add_rule($rule);
    }

    $input = $this->wiki_string;

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

          if (strlen($po["text"]) == 0)
            die("length of text is 0"); // for test only

          $text = new HAW_text($po["text"], $this->textformat);
          $text->set_br(0);
          $this->deck->add_text($text);

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

        case HAWIKI_TOKEN_COLORED:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Colored: %s</b><br>\n", $po["text"]);

          // color is not supported by HAWHAW -> display as bold
          $this->textformat ^= HAW_TEXTFORMAT_BOLD;

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

        case HAWIKI_TOKEN_BOX:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Box:</b> %s<br>\n", $po["text"]);

          $box = new HAW_text($po["text"], HAW_TEXTFORMAT_BOXED);
          $box->set_br(0);
          $this->deck->add_text($box);

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
            printf("<b>WikiName:</b> extwiki:%s page:%s label:%s<br>\n",
                   $po["extwiki"], $po["page"], $po["label"]);

          if ($po["extwiki"])
          {
            // external wiki page ==> search url of this wiki

            unset($link);

            foreach ($this->extwiki as $value)
            {
              if ($value["name"] == $po["extwiki"])
              {
                // external wiki has been declared before
                $link = new HAW_link($po["label"], ereg_replace("\$page", $po["page"], $value["url"]));
              }
            }

            if (!isset($link))
            {
              // external wiki has not been declared!!!
              $link = new HAW_link($po["label"], $this->wiki_base . $po["extwiki"] . ":" . $po["page"]);

            }
          }
          else
            $link = new HAW_link($po["label"], $this->wiki_base . $po["page"]);

          $link->set_br(0);
          $this->deck->add_link($link);

          break;
        }

        case HAWIKI_TOKEN_LINK:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Link:</b> url:%s label:%s<br>\n", $po["url"], $po["label"]);

          $link = new HAW_link($po["label"], $po["url"]);

          $link->set_br(0);
          $this->deck->add_link($link);

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
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Image:</b> src:%s alt:%s<br>\n", $po["src"], $po["alt"]);

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

            if (!($img_alt = $po["alt"]))
              $img_alt = $po["src"]; // set alternative text to src, if not available

            $image = new HAW_image($img_wbmp, $po["src"], $img_alt);
            $this->deck->add_image($image);
          }

          if ($po["link"])
          {
            // HAWHAW does not support images as links
            // ==> draw @-link next to image
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
      $error_text = new HAW_text(HAWIKI_ERROR . " " . HAWIKI_ERROR_INVWIKISYNTAX);
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
    else
    {
      if (file_exists(getcwd() . "/lib/hawhaw/link.wav"))
      {
        // default wav file available ==> use it!
        $this->deck->set_voice_jingle("./lib/hawhaw/link.wav");
      }
    }

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

    $arr["label"] = reconvert_html_entities($label);
    $arr["url"]   = $url;
    $arr["pos"]   = $pos;

    $this->navlink[] = $arr; // enhance array with navigation links
  }


  function set_extwiki($name, $url)
  {
    // define external wiki
    // e.g. set_extwiki("HAWHAW", "http://www.hawhaw.de/tiki/tiki-index.php?page=$page");

    $arr["name"] = $name;
    $arr["url"]  = $url;

    $this->extwiki[] = $arr; // enhance array with external wikis
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
