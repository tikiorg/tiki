<?php

// HAWIKI
// PHP wrapper class for HAWHAW-based wiki's
// Copyright (C) 2003 Norbert Huffschmid & TikiWiki project team
// Last modified: 16. May 2003
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
// List of supported/workarounded wiki elements:
//
// plain text
// bold text __bold__
// italic text ''italic''
// colored text ~~#0000FF:colored~~ (workaround: bold)
// monospaced BLANK text (workaround: small)
// headings ! !! !!!
// paragraphs -= paragraph =- (workaround: big and bold)
// WikiNames
// links
// tables
// textbox ^box^ (workaround: underlined)
// horizontal rules ----
// images
// smileys
//
// Remarks: Nested formats are currently NOT supported 


// ---------------------  begin of config part  ---------------------

define("HAWIKI_TITLE", "TikiWiki");       // -> "title" of created HAWHAW deck
define("HAWIKI_MAXINPUTCHARS", 100000);   // maximum wiki size
define("HAWIKI_DISP_WIDTH",  300);        // HAWHAW display width (pixel)
define("HAWIKI_DISP_HEIGHT", 300);        //                height
define("HAWIKI_DISP_BGCOLOR", "#FFFFFF"); //                background color

// error messages (modify these string for country adaptions
define("HAWIKI_ERROR", "Error:");
define("HAWIKI_ERROR_INVWIKISIZE", "Invalid wiki size");
define("HAWIKI_ERROR_INVWIKISYNTAX", "Invalid wiki syntax");

// ----------------------  end of config part  ----------------------



// token definitions
define("HAWIKI_TOKEN_EOF",        0);
define("HAWIKI_TOKEN_NEWLINE",    1);
define("HAWIKI_TOKEN_TEXT",       2);
define("HAWIKI_TOKEN_BOLD",       3);
define("HAWIKI_TOKEN_ITALIC",     4);
define("HAWIKI_TOKEN_HEADING1",   5);
define("HAWIKI_TOKEN_HEADING2",   6);
define("HAWIKI_TOKEN_HEADING3",   7);
define("HAWIKI_TOKEN_PARAGRAPH",  8);
define("HAWIKI_TOKEN_COLORED",    9);
define("HAWIKI_TOKEN_MONOSPACED", 10);
define("HAWIKI_TOKEN_BOX",        11);
define("HAWIKI_TOKEN_RULE",       12);
define("HAWIKI_TOKEN_WIKINAME",   13);
define("HAWIKI_TOKEN_LINK",       14);
define("HAWIKI_TOKEN_TABLE",      15);
define("HAWIKI_TOKEN_IMAGE",      16);
define("HAWIKI_TOKEN_SMILEY",     17);

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


class HAWIKI_wiki
{
  var $deck;          // HAW_deck object
  var $wiki_string;   // input in wiki syntax
  var $wiki_base;     // URL for WikiName links
  var $navlink;       // array with navigation links (home, menue etc.)  
  var $smiley_dir;    // where smiley gif's are stored
  var $yytext;        // parser output
  var $firstColumn;   // flag to determine parser position
  var $debugflags;    // flags for dumping debug info


  function HAWIKI_wiki($input, $wiki_base)
  {
    $this->wiki_string = $input;
    $this->wiki_base = $wiki_base;
    $this->smiley_dir = "";       // has to be set later by function call
    $this->navlink = array();
    $this->firstColumn = true;
    $this->debugflags = HAWIKI_DEBUG_NONE;

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
      // create empty line between link(s) and wiki content
      $emptyline = new HAW_text("");
      $this->deck->add_text($emptyline);
    }

    $input = $this->wiki_string;

    while ($token = $this->lexan($input))
    {
      $this->firstColumn = false; // default

      switch ($token)
      {
        case HAWIKI_TOKEN_NEWLINE:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Newline:</b> %d<br>\n", $this->yytext);

          $text = new HAW_text("");
          $text->set_br($this->yytext); // set number of line breaks
          $this->deck->add_text($text);
          $this->firstColumn = true;    // now we're again in column 0

          break;
        }

        case HAWIKI_TOKEN_TEXT:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Plain text:</b> %s<br>\n", $this->yytext);

          if (strlen($this->yytext) == 0)
            die("length of text is 0"); // for test only

          $text = new HAW_text($this->yytext);
          $text->set_br(0);
          $this->deck->add_text($text);

          break;
        }

        case HAWIKI_TOKEN_BOLD:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Bold:</b> %s<br>\n", $this->yytext);

          $boldtext = new HAW_text($this->yytext, HAW_TEXTFORMAT_BOLD);
          $boldtext->set_br(0);
          $this->deck->add_text($boldtext);

          break;
        }

        case HAWIKI_TOKEN_ITALIC:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Italic:</b> %s<br>\n", $this->yytext);

          $italictext = new HAW_text($this->yytext, HAW_TEXTFORMAT_ITALIC);
          $italictext->set_br(0);
          $this->deck->add_text($italictext);

          break;
        }

        case HAWIKI_TOKEN_COLORED:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Colored:</b> %s<br>\n", $this->yytext);

          // selective coloring is not supported by HAWHAW -> draw bold
          $coloredtext = new HAW_text($this->yytext, HAW_TEXTFORMAT_BOLD);
          $coloredtext->set_br(0);
          $this->deck->add_text($coloredtext);

          break;
        }

        case HAWIKI_TOKEN_MONOSPACED:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Monospaced:</b> %s<br>\n", $this->yytext);

          // monospace is not supported by HAWHAW -> draw small instead
          $smalltext = new HAW_text($this->yytext, HAW_TEXTFORMAT_SMALL);
          $smalltext->set_br(0);
          $this->deck->add_text($smalltext);

          break;
        }

        case HAWIKI_TOKEN_PARAGRAPH:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Paragraph:</b> %s<br>\n", $this->yytext);

          $paragraph = new HAW_text($this->yytext, HAW_TEXTFORMAT_BIG);
          $paragraph->set_br(0);
          $this->deck->add_text($paragraph);

          break;
        }

        case HAWIKI_TOKEN_BOX:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Box:</b> %s<br>\n", $this->yytext);

          // boxes are not supported by HAWHAW -> underline instead
          $box = new HAW_text($this->yytext, HAW_TEXTFORMAT_UNDERLINE);
          $box->set_br(0);
          $this->deck->add_text($box);

          break;
        }

        case HAWIKI_TOKEN_HEADING1:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Heading level 1:</b> %s<br>\n", $this->yytext);

          $h1 = new HAW_text($this->yytext, HAW_TEXTFORMAT_BIG | HAW_TEXTFORMAT_BOLD | HAW_TEXTFORMAT_UNDERLINE );
          $h1->set_br(0);
          $this->deck->add_text($h1);

          break;
        }

        case HAWIKI_TOKEN_HEADING2:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Heading level 2:</b> %s<br>\n", $this->yytext);

          $h2 = new HAW_text($this->yytext, HAW_TEXTFORMAT_BIG | HAW_TEXTFORMAT_BOLD);
          $h2->set_br(0);
          $this->deck->add_text($h2);

          break;
        }

        case HAWIKI_TOKEN_HEADING3:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Heading level 3:</b> %s<br>\n", $this->yytext);

          $h3 = new HAW_text($this->yytext, HAW_TEXTFORMAT_BOLD);
          $h3->set_br(0);
          $this->deck->add_text($h3);

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
            printf("<b>WikiName:</b> %s<br>\n", $this->yytext);

          $link = new HAW_link($this->yytext, $this->wiki_base . $this->yytext);
          $link->set_br(0);
          $this->deck->add_link($link);

          break;
        }

        case HAWIKI_TOKEN_LINK:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Link:</b> %s<br>\n", $this->yytext);

          $link_arr = explode("|", $this->yytext);

          if ($link_arr[1])
            // display alternative link text instead of URL
            $link = new HAW_link($link_arr[1], $link_arr[0]);
          else
            // display URL
            $link = new HAW_link($link_arr[0], $link_arr[0]);

          $link->set_br(0);
          $this->deck->add_link($link);

          break;
        }

        case HAWIKI_TOKEN_TABLE:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Table:</b> %s<br>\n", $this->yytext);

          $table = new HAW_table();
          $table_arr = explode("\n", $this->yytext);

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
            printf("<b>Image:</b> %s<br>\n", $this->yytext);

          $this->yytext = trim($this->yytext); // strip leading and tailing whitespace
          $this->yytext = ereg_replace(" +", " ", $this->yytext);
          $img_arr = explode(" ", $this->yytext);

          while (list($key, $value) = each($img_arr))
          {
            if (strtolower(substr($value, 0, 4)) == "src=")
            {
              $img_html = substr($value, 4);
              $img_wbmp = ereg_replace(".gif$|.png$|.jpg$", ".wbmp", $img_html);
            }
            elseif (strtolower(substr($value, 0, 5)) == "desc=")
            {
              $img_alt = substr($value, 5);
            }
          }

          $image = new HAW_image($img_wbmp, $img_html, $img_alt);
          $this->deck->add_image($image);

          break;
        }

        case HAWIKI_TOKEN_SMILEY:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Smiley:</b> %s<br>\n", $this->yytext);

          if ($this->smiley_dir)
          {
            // smiley directory has been set by the user

            $img_html = $this->smiley_dir . "/icon_" . $this->yytext . ".gif";
            $img_wbmp = $this->smiley_dir . "/icon_" . $this->yytext . ".wbmp";
            $img_alt = ":)";
            $image = new HAW_image($img_wbmp, $img_html, $img_alt);
            $image->use_localsrc("smileyface");
            $this->deck->add_image($image);
          }

          break;
        }

        default:
        {
          if ($this->debugflags & HAWIKI_DEBUG_PARSER)
            printf("<b>Unknown token:</b> %d<br>\n", $token);

          break;
        }
      }
    }

    if (strlen($input) > 0)
    {
      // parsing aborted due to syntax error
      HAWIKI_error(HAWIKI_ERROR_INVWIKISYNTAX); // script end
    }

    $link_counter = 0;
    foreach ($this->navlink as $value)
    {
      // create navigation link(s) at bottom of wiki page

      if ($value["pos"] & HAWIKI_NAVLINK_BOTTOM)
      {
        if ($link_counter == 0)
        {
          // create empty line between wiki content and 1st link
          $emptyline = new HAW_text("");
          $this->deck->add_text($emptyline);
        }

        $link = new HAW_link($value["label"], $value["url"]);
        $this->deck->add_link($link);
        $link_counter++;
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
   
    $arr["label"] = $label;
    $arr["url"]   = $url;
    $arr["pos"]   = $pos;

    $this->navlink[] = $arr; // enhance array with navigation links
  }


  function set_smiley_dir($dir)
  {
    $this->smiley_dir = $dir;
  }


  function set_debugflags($flags)
  {
    $this->debugflags = $flags;
  }


  function lexan(&$input)
  {
    // lexical analyser

    $this->yytext = ""; // init text to be delivered additionally to token
   
    if (strlen($input) == 0)
      return(HAWIKI_TOKEN_EOF); // end of input reached

    if (preg_match("/^([\r\n]+)/", $input, $matches))
    {
      // new line(s)
      $this->yytext = substr_count($matches[1], "\n");
      $input = substr($input, strlen($matches[1]));
      return(HAWIKI_TOKEN_NEWLINE);
    }

    if (preg_match("/^__(.*?)__/", $input, $matches))
    {
      // format bold
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_BOLD);
    }

    if (preg_match("/^''(.*?)''/", $input, $matches))
    {
      // format italic
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_ITALIC);
    }

    if (preg_match("/^-=(.*?)=-/", $input, $matches))
    {
      // paragraph
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_PARAGRAPH);
    }

    if (preg_match("/^\^(.*?)\^/", $input, $matches))
    {
      // box
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 2);
      return(HAWIKI_TOKEN_BOX);
    }

    if ($this->firstColumn && preg_match("/^!!!(.*)/", $input, $matches))
    {
      // heading 3
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 3);
      return(HAWIKI_TOKEN_HEADING3);
    }

    if ($this->firstColumn && preg_match("/^!!(.*)/", $input, $matches))
    {
      // heading 2
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 2);
      return(HAWIKI_TOKEN_HEADING2);
    }

    if ($this->firstColumn && preg_match("/^!(.*)/", $input, $matches))
    {
      // heading 1
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 1);
      return(HAWIKI_TOKEN_HEADING1);
    }

    if ($this->firstColumn && preg_match("/^ (.*)/", $input, $matches))
    {
      // monospaced
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 1);
      return(HAWIKI_TOKEN_MONOSPACED);
    }

    if (preg_match("/^::(.*?)::/", $input, $matches))
    {
      // centered text is not supported ==> handle as plain text
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_TEXT);
    }

    if (preg_match("/^~~#(\w*):(.*?)~~/", $input, $matches))
    {
      // colored text (not supported - displayed as bold)
      $this->yytext = $matches[2];
      $input = substr($input, strlen($this->yytext) + strlen($matches[1]) + 6);
      return(HAWIKI_TOKEN_COLORED);
    }

    if (preg_match("/^\)\)(.*?)\(\(/", $input, $matches))
    {
      // No WikiName ==> plain text
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_TEXT);
    }

    if ($this->firstColumn && preg_match("/^(----*\r?\n)/", $input, $matches))
    {
      // horizontal rule
      $input = substr($input, strlen($matches[1]));
      return(HAWIKI_TOKEN_RULE);
    }

    if (preg_match("/^([A-Z][a-z]+[A-Z][a-z]+\w*)/", $input, $matches))
    {
      // WikiName
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext));
      return(HAWIKI_TOKEN_WIKINAME);
    }

    if (preg_match("/^\(\((.*?)\)\)/", $input, $matches))
    {
      // Forced WikiName ((a_page_with_any_name))
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_WIKINAME);
    }

    if (preg_match("/^\[(.*?)\]/", $input, $matches))
    {
      // link
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 2);
      return(HAWIKI_TOKEN_LINK);
    }

    if (preg_match("/^\|\|((.|[\r\n])*?)\|\|/", $input, $matches))
    {
      // table
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_TABLE);
    }

    if (preg_match("/^\{img (.*?)\}/", $input, $matches))
    {
      // image
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 6);
      return(HAWIKI_TOKEN_IMAGE);
    }

    if (preg_match("/^\(:(.*?):\)/", $input, $matches))
    {
      // smiley
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext) + 4);
      return(HAWIKI_TOKEN_SMILEY);
    }

    // plain text - search next syntax element resp. eof
    if (preg_match("/^(.*?)(__|''|-=|\^|::|~~|\)\)|\(\(|\[|\|\||\{img |\(:|[A-Z][a-z]+[A-Z][a-z]+\w*|[\r\n]|$)/",
        $input, $matches))
    {
      // plain text
      $this->yytext = $matches[1];
      $input = substr($input, strlen($this->yytext));
      return(HAWIKI_TOKEN_TEXT);
    }

    return(HAWIKI_TOKEN_EOF);
  }

};

?>