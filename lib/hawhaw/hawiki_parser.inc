<?php

// HAWIKI parser for TikiWiki
// Last modified: 19. September 2003

// token definitions
define("HAWIKI_TOKEN_NEWLINE",    1);
define("HAWIKI_TOKEN_TEXT",       2);
define("HAWIKI_TOKEN_BOLD",       3);
define("HAWIKI_TOKEN_ITALIC",     4);
define("HAWIKI_TOKEN_HEADING",    5);
define("HAWIKI_TOKEN_BULLIST",    6);
define("HAWIKI_TOKEN_NUMLIST",    7);
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
define("HAWIKI_TOKEN_PLUGIN",     18);
define("HAWIKI_TOKEN_WIKIVOICE",  19);
define("HAWIKI_TOKEN_DUMMY",      20);


function HAWIKI_parser(&$input, $isFirstColumn)
{
  // lexical analyser for tikiwiki pages

  $po = array(); // array for parser output

  $po["text"] = ""; // init text output

  if (strlen($input) == 0)
    return(0);  // end of input reached

  if (preg_match("/^~np~((.|[\r\n])*?)~\/np~/", $input, $matches))
  {
    // non-parsed section
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]) + 9);
    $po["token"] = HAWIKI_TOKEN_TEXT;
    return($po);
  }

  if (preg_match("/^([\r\n]+)/", $input, $matches))
  {
    // new line(s)
    $po["count"] = substr_count($matches[1], "\n");
    $input = substr($input, strlen($matches[1]));
    $po["token"] = HAWIKI_TOKEN_NEWLINE;
    return($po);
  }

  if (preg_match("/^__/", $input))
  {
    // format bold
    $input = substr($input, 2);
    $po["token"] = HAWIKI_TOKEN_BOLD;
    return($po);
  }

  if (preg_match("/^''/", $input))
  {
    // format italic
    $input = substr($input, 2);
    $po["token"] = HAWIKI_TOKEN_ITALIC;
    return($po);
  }

  if (preg_match("/^-=(.*?)=-/", $input, $matches))
  {
    // paragraph
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]) + 4);
    $po["token"] = HAWIKI_TOKEN_PARAGRAPH;
    return($po);
  }

  if (preg_match("/^\^(.*?)\^/", $input, $matches))
  {
    // box
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]) + 2);
    $po["token"] = HAWIKI_TOKEN_BOX;
    return($po);
  }

  if ($isFirstColumn && preg_match("/^(!+)/", $input, $matches))
  {
    // heading
    $po["level"] = strlen($matches[1]);
    $input = substr($input, $po["level"]);
    $po["token"] = HAWIKI_TOKEN_HEADING;
    return($po);
  }

  if ($isFirstColumn && preg_match("/^(\*+)/", $input, $matches))
  {
    // bullet list
    $po["level"] = strlen($matches[1]);
    $input = substr($input, $po["level"]);
    $po["token"] = HAWIKI_TOKEN_BULLIST;
    return($po);
  }

  if ($isFirstColumn && preg_match("/^(#+)/", $input, $matches))
  {
    // numbered list
    $po["level"] = strlen($matches[1]);
    $input = substr($input, $po["level"]);
    $po["token"] = HAWIKI_TOKEN_NUMLIST;
    return($po);
  }

  if ($isFirstColumn && preg_match("/^ /", $input))
  {
    // monospaced
    $input = substr($input, 1);
    $po["token"] = HAWIKI_TOKEN_MONOSPACED;
    return($po);
  }

  if (preg_match("/^::/", $input))
  {
    // centered text (not supported)
    $input = substr($input, 2);
    $po["token"] = HAWIKI_TOKEN_DUMMY;
    return($po);
  }

  if (preg_match("/^~~#(\w*?):/", $input, $matches))
  {
    // colored text start
    $po["text"] = $matches[1];
    $input = substr($input, strlen($matches[1]) + 4);
    $po["token"] = HAWIKI_TOKEN_COLORED;
    return($po);
  }

  if (preg_match("/^~~/", $input))
  {
    // colored text end
    $po["text"] = "";
    $input = substr($input, 2);
    $po["token"] = HAWIKI_TOKEN_COLORED;
    return($po);
  }

  if (preg_match("/^\)\)(.*?)\(\(/", $input, $matches))
  {
    // No WikiName ==> plain text
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]) + 4);
    $po["token"] = HAWIKI_TOKEN_TEXT;
    return($po);
  }

  if ($isFirstColumn && preg_match("/^(----*\r?\n)/", $input, $matches))
  {
    // horizontal rule
    $input = substr($input, strlen($matches[1]));
    $po["token"] = HAWIKI_TOKEN_RULE;
    return($po);
  }

  if (preg_match("/^([A-Z][a-z0-9_]+[A-Z][a-z0-9_]+[\|\w]*)/", $input, $matches))
  {
    // WikiName
    $input = substr($input, strlen($matches[1]));
    $po["extwiki"] = ""; // internal wiki page

    $link = explode("|", $matches[1]); // test for alternative link label
    if (count($link) > 1)
    {
      $po["page"]  = $link[0];
      $po["label"] = $link[1];
    }
    else
    {
      $po["page"]  = $link[0]; // no extra labelling of link required
      $po["label"] = $link[0];
    }

    $po["token"] = HAWIKI_TOKEN_WIKINAME;
    return($po);
  }

  if (preg_match("/^\(\((.*?)\)\)/", $input, $matches))
  {
    // Forced WikiName ((a_page_with_any_name))
    $input = substr($input, strlen($matches[1]) + 4);

    $wikiname = explode(":", $matches[1]); // test for external wiki page
    if (count($wikiname) > 1)
    {
      $po["extwiki"] = $wikiname[0];
      $wikipage = $wikiname[1];
    }
    else
    {
      // if $matches contained no ':' ==> explode returns input string
      $po["extwiki"] = ""; // internal wiki page
      $wikipage = $wikiname[0];
    }

    $link = explode("|", $wikipage); // test for alternative link label
    if (count($link) > 1)
    {
      $po["page"]  = $link[0];
      $po["label"] = $link[1];
    }
    else
    {
      $po["page"]  = $link[0]; // no extra labelling of link required
      $po["label"] = $link[0];
    }

    $po["token"] = HAWIKI_TOKEN_WIKINAME;
    return($po);
  }

  if (preg_match("/^\[(.*?)\]/", $input, $matches))
  {
    // link
    $input = substr($input, strlen($matches[1]) + 2);

    $link = explode("|", $matches[1]); // test for alternative link label
    if (count($link) > 1)
    {
      $po["url"]   = $link[0];
      $po["label"] = $link[1];
    }
    else
    {
      $po["url"]   = $link[0]; // no extra labelling of link required
      $po["label"] = $link[0];
    }

    $po["token"] = HAWIKI_TOKEN_LINK;
    return($po);
  }

  if (preg_match("/^\|\|((.|[\r\n])*?)\|\|[\r\n]/", $input, $matches))
  {
    // table
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]) + 4);
    $po["text"] = ereg_replace("\|\|", "\n", $po["text"]); // consider both valid syntax formats
    $po["token"] = HAWIKI_TOKEN_TABLE;
    return($po);
  }

  if (preg_match("/^\{img (.*?)\}/", $input, $matches))
  {
    // image
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]) + 6);

    $po["text"] = trim($po["text"]); // strip leading and tailing whitespace
    $po["text"] = ereg_replace(" +", " ", $po["text"]);
    $img_arr = explode(" ", $po["text"]);

    while (list($key, $value) = each($img_arr))
    {
      $value = ereg_replace("[\"']", "", $value); // unquote attribute (if quoted)

      if (strtolower(substr($value, 0, 4)) == "src=")
      {
        $po["src"] = substr($value, 4, strlen($value) - 4);
      }
      elseif (strtolower(substr($value, 0, 5)) == "desc=")
      {
        $po["alt"] = substr($value, 5, strlen($value) - 5);
      }
      elseif (strtolower(substr($value, 0, 5)) == "link=")
      {
        $po["link"] = substr($value, 5, strlen($value) - 5);
      }
    }

    $po["token"] = HAWIKI_TOKEN_IMAGE;
    return($po);
  }

  if (preg_match("/^\(:(.*?):\)/", $input, $matches))
  {
    // smiley
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]) + 4);
    $po["token"] = HAWIKI_TOKEN_SMILEY;
    return($po);
  }

  if (preg_match("/^\{([A-Z]+)\(([^\)]*)\)\}/", $input, $matches))
  {
    // plugin
    $plugin_name = $matches[1];
    $params = split(',',trim($matches[2]));
    $input = substr($input, strlen($matches[1]) + strlen($matches[2]) + 4);

    if (preg_match("/^((.|[\r\n])*?)\{$plugin_name\}/", $input, $matches))
    {
      $plugin_data = $matches[1];
      $input = substr($input, strlen($plugin_data) + strlen($plugin_name) + 2);

      // the following code was borrowed from tikilib.php

      $php_name = "lib/wiki-plugins/wikiplugin_" . strtolower($plugin_name) . ".php";
      $func_name = "wikiplugin_" . strtolower($plugin_name);
      $arguments = array();
      foreach($params as $param)
      {
        $decoded_param = str_replace('&gt;', '>', $param);
        $parts = explode( '=>', $decoded_param );
        if (isset($parts[0]) && isset($parts[1]))
        {
          $name = trim($parts[0]);
          $arguments[$name] = trim($parts[1]);
        }
      }
      if (file_exists($php_name))
      {
        if (strtolower($plugin_name) == "wikivoice")
        {
          // wikivoice plugin
          $po["php_name"]  = $php_name;  // file name to include
          $po["func_name"] = $func_name; // function to call
          $po["data"]   = $plugin_data;  // data string (to be parsed from plugin)
          $po["params"] = $arguments;    // array of parameters (only cmd=>... is used)
          $po["token"]  = HAWIKI_TOKEN_WIKIVOICE;
        }
        else
        {
          // some other tiki plugin
          include_once($php_name);
          $po["html"]  = $func_name($plugin_data,$arguments);
          $po["token"] = HAWIKI_TOKEN_PLUGIN;
        }
      }
    }
    else
      return(0); // no plugin end found

    return($po);
  }

  // plain text - search next syntax element resp. eof
  if (preg_match("/^(.*?)(~np~|__|''|-=|\^|::|~~|\)\)|\(\(|\[|\|\||\{img |\(:|[A-Z][a-z0-9_]+[A-Z][a-z0-9_]+[\|\w]*|[\r\n]|\{[A-Z]+\([^\)]*\)\}|$)/",
      $input, $matches))
  {
    // plain text
    $po["text"] = $matches[1];
    $input = substr($input, strlen($po["text"]));

    if (strlen($po["text"]) > 0)
      $po["token"] = HAWIKI_TOKEN_TEXT;
    else
      return(0); // can happen in case of syntax errors

    return($po);
  }

  return(0);
}

?>
