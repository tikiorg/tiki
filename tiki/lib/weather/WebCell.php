<?

  /*
  
 WebCell - PHP Web Scraping Tool
 Version 1.0

 Travis Richardson
 http://www.ravis.org/
 
 Get the latest version, instructions, etc at:
 http://www.ravis.org/code/webcell/
 
 See readme.txt for instructions.
 See browser.php for sample code.
  
  */
  
  
  
  class WebCell {
  
    var $URI = "";
    var $PageLoaded = false;
    var $GlobalTableArray = array();
    var $Unsaved = false;
    var $TaskTime = array();
    var $DebugTime = false;
    var $CacheEnabled = false;
    var $CacheDirectory = "";
    var $CacheFilename = "";
    var $CacheTimeout = 0;
    var $AgentString = "WebCell 1.0 (+http://www.ravis.org/code/webcell/)";
    var $RawHTMLCode = null;
    
    
    
    
    function WebCell($URI = null) {
      $this->URI = $URI;
      $this->GlobalTableArray = array();
    }
    
    function EnableCache($Directory,$Seconds=3600) {

      $this->CacheEnabled = true;
      $this->CacheDirectory = $Directory;
      $this->CacheTimeout = $Seconds;

      // Set the theoretical filename for the cache
      $this->CacheFilename = ereg_replace("/$","",$this->CacheDirectory)."/".urlencode($this->URI);

    }
        
    function UserAgent($AgentString=null) {
      if (is_null($AgentString)) return($this->AgentString);
      else $this->AgentString = $AgentString;
    }
    
    function GetPage($ForceReload=false) {
    
      // This function retrieves a page from a web server
      // and prepares it for parsing.
          
      // Reset everything that's important, in case you're 
      // reusing an existing object
      unset($this->GlobalTableArray);
      
      // This section attempts to load from the cache first, but
      // if the cache is disabled or not working, then tries
      // to load from the live URL. If that doesn't work then
      // you're screwed and it returns false.
      if (!$ForceReload) $LoadSuccess = $this->LoadCache();
      if (!$LoadSuccess) $LoadSuccess = $this->LoadLive();
      
      return($LoadSuccess);

    }
    
    function SetRawHTML($HTMLString) {

      // This function takes a string of HTML code
      // and prepares it for parsing.

      $this->URI = null;
      $this->RawHTMLCode = $HTMLString;
      
      // Reset everything that's important, in case you're 
      // reusing an existing object
      unset($this->GlobalTableArray);
      
      // Set our flag so we know the data is available
      $this->PageLoaded = true;
        
      // If we've gotten this far, we've succeeded in loading
      // the page, so return true!
      return(true);
      
    }
    
    function GetRawHTML() {

      // This function returns the raw HTML code that was
      // retrieved from the URI.
      
      // if the page isn't loaded - load it.
      if (!$this->PageLoaded) $this->GetPage();
      
      // return the raw html code
      return($this->RawHTMLCode);
      
    }
    
    function GetCell($table,$row,$col) {
      if (!$this->GlobalTableArray[$table]["Parsed"]) $this->ParseTable($table);
      return($this->GlobalTableArray[$table]["Data"][$row][$col]);
    }
    
    function GetTable($table) {
      if (!$this->GlobalTableArray[$table]["Parsed"]) $this->ParseTable($table);
      return($this->GlobalTableArray[$table]["Data"]);
    }
    
    function NumberOfCells($table,$row) {
      if (!$this->GlobalTableArray[$table]["Parsed"]) $this->ParseTable($table);
      return(count($this->GlobalTableArray[$table]["Data"][$row]));
    }
    
    function NumberOfRows($table=0) {
      if (!$this->GlobalTableArray[$table]["Parsed"]) $this->ParseTable($table);
      return(count($this->GlobalTableArray[$table]["Data"]));
    }
        
    function NumberOfTables() {
      if (count($this->GlobalTableArray) < 1) $this->FindTablePositions();
      return(count($this->GlobalTableArray));
    }
    
	function PrintTable($table=0) {
	
      if (!$this->GlobalTableArray[$table]["Parsed"]) $this->ParseTable($table);

	  $this->StartTask("Outputing table $table");

	  $TableArray = $this->GlobalTableArray[$table]["Data"];

	  $maxwidth=0;
	  for ($row=0; $row<count($TableArray); $row++) if (count($TableArray[$row])>$maxwidth) $maxwidth = count($TableArray[$row]);

	  print "<table border=1 cellpadding=2 cellspacing=0 bordercolor=gray bgcolor=gray>\n";
      print "<tr><td colspan=$maxwidth bgcolor=black><font color=silver><b>Table $table</b></font></td></tr>\n";
	  for ($row=0; $row<count($TableArray); $row++) {
		print "<tr>";
		for ($col=0; $col<count($TableArray[$row]); $col++) print "<td valign=top bgcolor=white><font size=2 color=gray>($table,$row,$col)</font><br>".htmlspecialchars($TableArray[$row][$col])."</td>";
		print "</tr>\n";
	  }
	  print "</table>\n";

	  $this->EndTask("Outputing table $table");

	}

    function SaveCache($UpdateTimestamp=false) {
    
      // If the cache is enabled then dump the object to 
      // the cache. Note we usually want to preserve the
      // file's timestamp so that the cache will still
      // expire when it should.

      // Gotta clear PHP's file stat cache, or we can't get an acurate
      // report on cache expiry
      clearstatcache();
      
      if ($this->CacheEnabled && $this->Unsaved && !is_null($this->URI)) {
      
        $this->StartTask("Saving to cache");
      
        if (!$UpdateTimestamp) $OldTimestamp = filemtime($this->CacheFilename);
      
        $file = fopen($this->CacheFilename,"w");
        $SaveArray[] = $this->GlobalTableArray;
        $SaveArray[] = $this->RawHTMLCode;
        fwrite($file,serialize($SaveArray));
        fclose($file);
        
        if (!$UpdateTimestamp) touch($this->CacheFilename,$OldTimestamp);
//          else touch($this->CacheFilename);
        
        $this->Unsaved = false;
      
        $this->EndTask("Saving to cache");

      }

    }
    




    // This set of functions provides some interesting stats, basically
    // a method of determining where bottlenecks are in the whole
    // process. This may not work on Windows systems. To enable call the
    // function DisplayTimes(true) - to disable, DisplayTimes(false)

    function DisplayTimes($Times=null) {
      if (is_null($Times)) return $this->DebugTime;
      else $this->DebugTime = $Times;
    }
    
    function getmicrotime(){ 
      if ($this->DebugTime) {
        list($usec, $sec) = explode(" ",microtime()); 
        return ((float)$usec + (float)$sec); 
      }
    } 
    
    function StartTask($TaskName) {
      if ($this->DebugTime) {
        $this->TaskTime[strval($TaskName)] = $this->getmicrotime();
      }
    }
    
    function EndTask($TaskName) {
      if ($this->DebugTime) {
        $TimeTaken = ($this->getmicrotime() - $this->TaskTime[strval($TaskName)]);
        $TimeTaken = round($TimeTaken * 10000)/10000;
        print "\n<b>WebCell : ".htmlspecialchars($TaskName)." took $TimeTaken seconds</b><br>\n";
      }
    }
    
    // End time tracking
    
    
    
    
    
    function LoadCache() {
    
      // If the cache is enabled AND the local file was 
      // updated within the timeout period then restore 
      // the GlobalTableArray from it's cached state
      
      clearstatcache();
      
      if ($this->CacheEnabled && ((filemtime($this->CacheFilename) + $this->CacheTimeout) > time())) {

//        print "Loading local copy from ".$this->CacheFilename."<br>";

        $this->StartTask("Loading cached page");

        $file = fopen($this->CacheFilename,"r");
        if (!$file) return(false);
        
        $SerialArray = fread($file,filesize($this->CacheFilename)+100);
        if (!$SerialArray) return(false);

        fclose($file);

        $LoadArray = unserialize($SerialArray);
        if (!$LoadArray) return(false);
        
  	    // extract the raw code and global table array from the
  	    // saved object
	    $this->GlobalTableArray = $LoadArray[0];
	    $this->RawHTMLCode = $LoadArray[1];

        $this->EndTask("Loading cached page");
        
        $this->PageLoaded = true;
        
        return(true);

      }
      
      return(false);
      
    }
    
    function LoadLive() {
    
//      print "Loading live page from ".$this->URI."<br>";

      // Now, it may look like we're doing whacky stuff here.
      // Why not just use file() you ask? Because we want to
      // send a customized Browser Client string for one.
      // This also allows greater flexability (like adding
      // support for redirects when I get around to it

      eregi("(http)\://([^/:]*)\:{0,1}([^/]*)(.*)",$this->URI,$Vars);
      $Protocol = $Vars[1];
      $Server = $Vars[2];
      $Port = $Vars[3];
      $Path = $Vars[4];
      if (!$Port) $Port = 80;
      if (!$Path) $Path = "/";
//      print "Protocol=$Protocol<br>Server=$Server<br>Port=$Port<br>Path=$Path<br>";
      
      $this->StartTask("Loading live page");

      // Open a connection to the server on the specified port
      // If we run into an error return false
      $socket = fsockopen ($Server, $Port);
      if (!$socket) {
        return(false);
      }
      
      // Set up our header for the request - here's where we
      // specify out User-Agent, which is the whole point of
      // doing it this way
      $RequestHeader = "GET $Path HTTP/1.0\r\n".
                       "Host: $Server\r\n".
                       "User-Agent: ".$this->AgentString."\r\n".
                       "\r\n";
      
      // Send out header - if there's a problem, return false
      $OK = fputs ($socket, $RequestHeader);
      if (!$OK) return(false);
      
	  // Read data from the connection so long as it keeps
	  // sending data.
	  while (!feof($socket)) {
	    $HTMLString .= fgets ($socket,128);
	  }
	  
	  // Close our connection
	  fclose ($socket);
	  
	  // save the raw code in case we want to play with it
	  $this->RawHTMLCode = $HTMLString;

	  // set our unsaved flag
	  $this->Unsaved = true;

      $this->EndTask("Loading live page");
      
      // Save what we've done to the cache
      $this->SaveCache(true);
      
      // Set our flag so we know the data is available
      $this->PageLoaded = true;
        
      // If we've gotten this far, we've succeeded in loading
      // the page, so return true!
      return(true);

    }
    
	function FindTablePositions() {
	
	  if (!$this->PageLoaded) $this->GetPage();

	  $this->StartTask("Extracting tables");
	  
      // Set the unsaved status so we know to update the object when
      // the script dies.
      $this->Unsaved = true;
      
	  // Reset the array we're going to be using - just a good habit
	  unset($TableArray);

      // This line is kinda odd when you first look at it. What
      // I'm doing is converting all '<table' and '</table' items 
      // to lower case so they can be found by strpos (which 
      // ignores case)
      $HTMLString = preg_replace(array("'<table'i","'</table'i"),array("<table","</table"),$this->RawHTMLCode);

	  // Find the first table
	  $TableStart = strpos($HTMLString,"<table");
	  $CurrentPos = $TableStart + 1;

	  // So long as there are tables being found, keep doing this
	  while (!($TableStart === false)) {

		// Save the starting position of the table
		$TableArray[] = array("Start" => $TableStart);
		
		// Find the next table - if there are none, $TableStart
		// will be set to false and the loop will exit the next
		// time around.
		$TableStart = strpos($HTMLString,"<table",$CurrentPos);
		$CurrentPos = $TableStart + 1;

	  }
	  
	  // Quick note: We do the below task backwards so that we remove 
	  // the inner most nested tables first. If we did this forwards, 
	  // we would grab the main table from a page and ignore all inner 
	  // tables. The data would still be there, but it would be messy 
	  // and not in the format that the page desired.

	  for ($t=count($TableArray)-1; $t>=0; $t--) {
	  
	    // This area is really processor intensive. Way too much so.
	    // The problem appears to be the substr statements, which
	    // take a lot of time. Not sure why...

		// Retrieve the starting position for the table
		$TableStart = $TableArray[$t]["Start"];
		
		// Find the ending position
		$TableEnd = strpos($HTMLString,"</table>",$TableStart)+8;
		
		// Extract the part between the start and end and store
		// it in the main table array
		$TableHTML = substr($HTMLString,$TableStart,$TableEnd - $TableStart);
		$this->GlobalTableArray[$t]["HTML"] = $TableHTML;

		// Remove the section we just extracted from the main
		// string, so we don't extract it again
		$HTMLString = substr_replace($HTMLString,"",$TableStart,$TableEnd - $TableStart);
		
	  }
	  
	  $this->EndTask("Extracting tables");

	}
	
    function ParseTable($table) {
    
      // Special sidenote: I am not a regex writer. I have taken various
      // regular expressions from many many different sources, changed
      // them to suit my needs and used them here. If you see ways to
      // optimize this code, please let me know. Thanks to all the
      // people who post their samples and pieces of advice freely
      // on the web - without which I would be so totally lost :)
    
      // Set the unsaved status so we know to update the object when
      // the script dies.
      $this->Unsaved = true;

      // If the page hasn't been loaded yet (ie: the user hasn't called
      // GetPage()) then load it. If there are problems loading it, 
      // return false;
      if (!$this->PageLoaded) {
        if (!$this->GetPage()) return(false);
      }

      // Go find the tables in the code we just retrieved (if it
      // hasn't been done already)
      if (count($this->GlobalTableArray)<1) $this->FindTablePositions();
      
      // Load the raw html from the global array - this just makes
      // the code a little neater
      $TableString = $this->GlobalTableArray[$table]["HTML"];
      
      $this->StartTask("Parsing table $table");
      
	  // Remove all newlines and tabs (because we're going to use these
	  // characters later as delimiters)
	  $TableString = ereg_replace("[\n\r\t]"," ",$TableString);
//	  print "\n=================\nRemove newlines and tabs:\n\n$TableString\n=================\n";

	  // Remove all comments, scripts, and css
	  $Search = array("'/<!--.*?-->/'",
	                  "'/<!.*?>/'",
	                  "'<script[^>]*>.*</script>'si",
	                  "'<style[^>]*?>.*?</style>'si");
	  $TableString = preg_replace($Search,"",$TableString);
//	  print "\n=================\nRemove comments, scripts, and css:\n\n$TableString\n=================\n";

	  // Replace all rows <tr> with newlines \n
	  // Replace all columns <td> with tabs \t
	  $Search = array("'<tr[^>]*>'si",
	                  "'</tr[^>]*>'si",
	                  "'<td[^>]*>'si",
	                  "'</td[^>]*>'si");
	  $Replace = array("\n",
	                   "",
	                   "\t",
	                   "");
	  $TableString = preg_replace($Search,$Replace,$TableString);
//	  print "\n=================\nColumns replaced with tabs:\n\n$TableString\n=================\n";

      // Extract ALT tags and place them in front of whatever object held them
      // We want to keep the rest of the object intact for the next step
      $TableString = eregi_replace("(<[^>]+) alt *= *\"([^\"]*)\"([^>]*>)", " \\2 \\1\\3 ", $TableString);
//	  print "\n=================\nExtract alt tags:\n\n$TableString\n=================\n";

      // Extract all HREF and SRC URLs
      $TableString = eregi_replace("<[^>]+href *=[ \"]*([^\"| |>]*)[^>]*>", " \\1 ", $TableString);
      $TableString = eregi_replace("<[^>]+src *=[ \"]*([^\"| |>]*)[^>]*>", " \\1 ", $TableString);
//	  print "\n=================\nExtract href's and src's:\n\n$TableString\n=================\n";

	  // Nuke any and all remaining markup <...>
	  $TableString = ereg_replace("<[^>]*>"," ",$TableString);
//	  print "\n=================\nNo HTML:\n\n$TableString\n=================\n";

	  // Translate all special characters back to plaintext. Of
	  // special note is &nbsp; which we want to be a normal space,
	  // not anything funky (like the translation table seems to
	  // want to do)
	  $TableString = str_replace("&nbsp;"," ",$TableString);
	  $trans = get_html_translation_table(HTML_ENTITIES);
	  $trans = array_flip($trans);
	  $TableString = strtr($TableString,$trans);
//	  print "\n=================\nSpecial characters translated:\n\n$TableString\n=================\n";

	  // Now neaten up our mess. Remove all multiple spaces and spaces
	  // around our columns (tabs) and the extra tab at the beginning
	  // of each line
	  $TableString = ereg_replace(" {2,}"," ",$TableString);
	  $TableString = ereg_replace(" ?\t ?","\t",$TableString);
	  $TableString = ereg_replace("\n\t| ?\n ?","\n",$TableString);
//	  print "=================\nExtra spaces removed:\n\n$TableString\n=================\n";

	  // Now we have a nicely delimited string that any spreadsheet
	  // could easily read. Let's turn that into a 2D array
	  $TableArray = explode("\n",$TableString);
	  unset($NewTableArray);
	  for ($row=1; $row<count($TableArray); $row++)
		$NewTableArray[] = explode("\t",$TableArray[$row]);

	  // ...and store it in the master array
	  $this->GlobalTableArray[$table]["Data"] = $NewTableArray;
      $this->GlobalTableArray[$table]["HTML"] = "";
      $this->GlobalTableArray[$table]["Parsed"] = true;
      
      $this->EndTask("Parsing table $table");

	}
	
  }


?>