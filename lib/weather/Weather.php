<?

  /*

  This script is copyright 2000-2002 by Travis Richardson (http://www.ravis.org). 
  You have my permission to use, distribute, and modify it free of charge so long 
  as this copyright notice remains intact.
  
  You can always get the latest version at http://www.ravis.org/code/weather/
  
  If you make changes or fix any bugs that appear feel free to send them to me and
  I may include them in future releases.
  
  Read the readme.txt file included in the zip file for instructions.

  */
  
  include_once("WebCell.php");
  
  class Weather {
  
    var $locationString = "";
    
    var $currentCondition;
    var $currentTemp;
    var $currentWindChill;
    var $currentHeatIndex;
    var $currentWindDirection;
    var $currentWindSpeed;
    var $currentDewpoint;
    var $currentRelativeHumidity;
    var $currentVisibility;
    var $currentBarometerStatus;
    var $currentBarometer;
    var $currentLastUpdate;
    var $currentImageURL;
    
    var $forecastDate;
    var $forecastCondition;
    var $forecastTempLow;
    var $forecastTempHi;
    var $forecastLastUpdate;
    var $forecastImageURL;
    
    var $currentWeatherHTML = "";
    var $currentWeatherText = "";
    var $forecastWeatherHTML = "";
    var $forecastWeatherText = "";
  
 
    function isWeatherDebug() { return(false); }
 
 
    function writeText() {
    
      $file = fopen($this->getLocation() . ".txt","w");

      fwrite($file,"Processed Data\n");
      fwrite($file,"==============\n");
      fwrite($file,"Location = " . $this->getLocation() . "\n");
      fwrite($file,"Condition = " . $this->getCondition(0) . "\n");
      fwrite($file,"Temp in F = " . $this->getTemp(0,"F") . "\n");
      fwrite($file,"Temp in C = " . $this->getTemp(0,"C") . "\n");
      fwrite($file,"Windchill in F = " . $this->getWindChill(0,"F") . "\n");
      fwrite($file,"Windchill in C = " . $this->getWindChill(0,"C") . "\n");
      fwrite($file,"Heat Index in F = " . $this->getHeatIndex(0,"F") . "\n");
      fwrite($file,"Heat Index in C = " . $this->getHeatIndex(0,"C") . "\n");
      fwrite($file,"Wind Direction = " . $this->getWindDirection(0) . "\n");
      fwrite($file,"Wind Speed in MPH = " . $this->getWindSpeed(0,"MPH") . "\n");
      fwrite($file,"Wind Speed in KPH = " . $this->getWindSpeed(0,"KPH") . "\n");
      fwrite($file,"Dewpoint in F = " . $this->getDewpoint(0,"F") . "\n");
      fwrite($file,"Dewpoint in C = " . $this->getDewpoint(0,"C") . "\n");
      fwrite($file,"Reletive Humidity = " . $this->getReletiveHumidity(0) . "\n");
      fwrite($file,"Visibility in Mi = " . $this->getVisibility(0,"mi") . "\n");
      fwrite($file,"Visibility in Km = " . $this->getVisibility(0,"km") . "\n");
      fwrite($file,"Barometer Status = " . $this->getBarometerStatus(0) . "\n");
      fwrite($file,"Barometer in Inches = " . $this->getBarometer(0,"in") . "\n");
      fwrite($file,"Barometer in cm = " . $this->getBarometer(0,"cm") . "\n");
      fwrite($file,"Barometer in kPa = " . $this->getBarometer(0,"kpa") . "\n");
      fwrite($file,"Last Updated = " . $this->getUpdateTime(0) . " (" . date("D M j, Y @ g:ia T",$this->getUpdateTime(0)) . ")\n");
      fwrite($file,"Image URL = " . $this->getImageURL(0) . "\n");

      fwrite($file,"\nWeather Text Dump\n");
      fwrite($file,"=================\n");
      for ($t=0; $t<count($this->currentWeatherText); $t++)
        fwrite($file, $this->currentWeatherText[$t] . "\n");
        
      fwrite($file,"\nWeather HTML Dump\n");
      fwrite($file,"=================\n");
      fwrite($file, $this->currentWeatherHTML);

      fclose($file);
    
    }
    
    
    function inArray($needle,$haystack) {
    
      for ($t=0; $t<count($haystack); $t++)
        if (strtolower($needle) == strtolower($haystack[$t])) return($t);
        
      return(false);
    
    }
    
    
    function convertTemp($fTemp,$metric) {
      // if the temp is empty, return false
      if ($fTemp !== doubleval(0) && empty($fTemp)) return(false);
      // if the temp is not a number (eg: "n/a") return it
      if (is_double($fTemp) === false) return($fTemp);
      if (strtolower($metric) == "f") return($fTemp);
      if (strtolower($metric) == "c") return(($fTemp - 32) * 5/9);
    }
    
    
    function convertSpeed($mph,$metric) {
      if ($mph !== doubleval(0) && empty($mph)) return(false);
      if (strtolower($metric) == "mph") return($mph);
      if (strtolower($metric) == "kph") return($mph * 1.609344);
    }
    
    
    function convertDistance($miles,$metric) {
      if (!$miles) return(false);
      if (!doubleval($miles)) return($miles);
      if (strtolower($metric) == "mi") return($miles);
      if (strtolower($metric) == "km") return($miles * 1.609344);
    }
    
    
    function convertLength($inches,$metric) {
      if (!$inches) return(false);
      if (strtolower($metric) == "in") return($inches);
      if (strtolower($metric) == "cm") return($inches * 2.54);
      if (strtolower($metric) == "kpa") return(($inches * 2.54) * (101.325/760.0) * 10);
    }
    
    
    function getTemp($day,$metric) {
      if (!$this->Connected) return(false);
      if (!$this->currentTemp) {
        $this->currentTemp = doubleval($this->wcObject->GetCell(0,0,2));
      }
      return($this->convertTemp($this->currentTemp,$metric));
    }
    
    
    function getImageURL($day) {
      if (!$this->Connected) return(false);
      if (!$this->currentImageURL) {
        $temp = $this->wcObject->GetCell(0,0,1);
        $temp = explode(" ",$temp);
        $temp = $temp[count($temp)-1];
        $this->currentImageURL = $temp;
      }
      return($this->currentImageURL);
    }
    
    
    function getWindChill($day,$metric) {
      if (!$this->Connected) return(false);
      if (!$this->currentWindChill) {
        // Get windchill from current temp and feels like
        if ($this->getFeelsLike($day,$metric) < $this->getTemp($day,$metric))
          $this->currentWindChill = $this->getFeelsLike($day,$metric);
        else
          $this->currentWindChill = "none";
      }
      return($this->convertTemp($this->currentWindChill,$metric));
    }
    
    
    function getHeatIndex($day,$metric) {
      if (!$this->Connected) return(false);
      if (!$this->currentHeatIndex) {
        // Get heat index from current temp and feels like
        if ($this->getFeelsLike($day,$metric) > $this->getTemp($day,$metric))
          $this->currentHeatIndex = $this->getFeelsLike($day,$metric);
        else
          $this->currentHeatIndex = "none";
      }
      return($this->convertTemp($this->currentHeatIndex,$metric));
    }
    
    
    function getFeelsLike($day,$metric) {
      if (!$this->Connected) return(false);
      if (!isset($this->feelsLike)) {
        preg_match("/Feels Like ([\-0-9]+)/i",$this->wcObject->GetCell(0,1,2),$temp);
        $temp = $temp[1];
        if (strlen($temp)>0) $this->feelsLike = doubleval($temp);
          else $this->feelsLike = $this->getTemp($day,"F");
      }
      return($this->convertTemp($this->feelsLike,$metric));
    }
    
    
    function getBasics() {
      if (!$this->Connected) return(false);

      eregi("as reported at (.*) last updated (.*)\.",$this->wcObject->GetCell(1,7,1),$temp);
      $this->locationString = $temp[1];
      $this->currentLastUpdate = $temp[2];
      
    }
    
    
    function getLocation() {
      if (!$this->Connected) return(false);
      if (!$this->locationString) {
        $this->getBasics();
      }
      return($this->locationString);
    }
    
    
    function getUpdateTime($day) {
      if (!$this->Connected) return(false);
      if (!$this->currentLastUpdate) {
        $this->getBasics();
      }
      return($this->currentLastUpdate);
    }
    
    
    function getCondition($day) {
      if (!$this->Connected) return(false);
      if (!$this->currentCondition) {
        $this->currentCondition = $this->wcObject->GetCell(0,1,1);
      }
      return($this->currentCondition);
    }
    
    
    function getUVIndex($day) {
      if (!$this->Connected) return(false);
      if (!$this->uvIndex) {
        $this->uvIndex = strtolower($this->wcObject->GetCell(1,0,2));
      }
      return($this->uvIndex);
    }
    
    
    function getWindData() {
      if (!$this->Connected) return(false);

      eregi("From the (.*) at (.*) mph",$this->wcObject->GetCell(1,5,2),$temp);
      
      $direction = explode(" ",$temp[1]);
      $direction = strtolower($direction[count($direction)-1]);
      $this->currentWindSpeed = doubleval($temp[2]);

      // Since Weather.com reports the direction wind is blowing FROM we need 
      // to switch it around to report the actual direction the wind is blowing...
      switch($direction) {
        case "north": 		$this->currentWindDirection = "south"; break;
        case "northeast": 	$this->currentWindDirection = "southwest"; break;
        case "east": 		$this->currentWindDirection = "west"; break;
        case "southeast": 	$this->currentWindDirection = "northwest"; break;
        case "south": 		$this->currentWindDirection = "north"; break;
        case "southwest": 	$this->currentWindDirection = "northeast"; break;
        case "west": 		$this->currentWindDirection = "east"; break;
        case "northwest": 	$this->currentWindDirection = "southeast"; break;
      }
        
    }
    

    function getWindSpeed($day,$metric) {
      if (!$this->Connected) return(false);
      if (!$this->currentWindSpeed) {
        $this->getWindData();
      }
      return($this->convertSpeed($this->currentWindSpeed,$metric));
    }
    
    
    function getWindDirection($day) {
      if (!$this->Connected) return(false);
      if (!$this->currentWindDirection) {
        $this->getWindData();
      }
      return($this->currentWindDirection);
    }
    
    
    function getDewpoint($day,$metric) {
      if (!$this->Connected) return(false);
      if (!$this->currentDewpoint) {
        $this->currentDewpoint = doubleval($this->wcObject->GetCell(1,1,2));
      }
      return($this->convertTemp($this->currentDewpoint,$metric));
    }
    
    
    function getRelativeHumidity($day) {
      if (!$this->Connected) return(false);
      if (!$this->currentRelativeHumidity) {
        $this->currentRelativeHumidity = intval($this->wcObject->GetCell(1,2,2));
      }
      return($this->currentRelativeHumidity);
    }
    
    
    function getVisibility($day,$metric) {
      if (!$this->Connected) return(false);
      if (!$this->currentVisibility) {
        $this->currentVisibility = trim(str_replace("miles","",strtolower($this->wcObject->GetCell(1,3,2))));
      }
      return($this->convertDistance($this->currentVisibility,$metric));
    }
    
    
    function getBarometerData() {
      if (!$this->Connected) return(false);

      $temp = $this->wcObject->GetCell(1,4,2);
      $temp = explode(" ",$temp);
      
      $this->currentBarometerStatus = strtolower($temp[3]);
      $this->currentBarometer = doubleval($temp[0]);

    }
    

    function getBarometerStatus($day) {
      if (!$this->Connected) return(false);
      if (!$this->currentBarometerStatus) {
        $this->getBarometerData();
      }
      return($this->currentBarometerStatus);
    }
    
    
    function getBarometer($day,$metric) {
      if (!$this->Connected) return(false);
      if (!$this->currentBarometer) {
        $this->getBarometerData();
      }
      return($this->convertLength($this->currentBarometer,$metric));
    }
    
    
    function outputDebug() {
      if (!$this->Connected) return(false);
      
      print "<pre>";
      print "   Location = " . $this->getLocation() . " \n";
      print "   Condition = " . $this->getCondition(0) . " \n";
      print "   Temp in F = " . $this->getTemp(0,"F") . " \n";
      print "   Temp in C = " . $this->getTemp(0,"C") . " \n";
      print "   Feels Like in F = " . $this->getFeelsLike(0,"F") . " \n";
      print "   Feels Like in C = " . $this->getFeelsLike(0,"C") . " \n";
      print "   Windchill in F = " . $this->getWindChill(0,"F") . " \n";
      print "   Windchill in C = " . $this->getWindChill(0,"C") . " \n";
      print "   Heat Index in F = " . $this->getHeatIndex(0,"F") . " \n";
      print "   Heat Index in C = " . $this->getHeatIndex(0,"C") . " \n";
      print "   UV Index = " . $this->getUVIndex(0) . "\n";
      print "   Wind Direction = " . $this->getWindDirection(0) . " \n";
      print "   Wind Speed in MPH = " . $this->getWindSpeed(0,"MPH") . " \n";
      print "   Wind Speed in KPH = " . $this->getWindSpeed(0,"KPH") . " \n";
      print "   Dewpoint in F = " . $this->getDewpoint(0,"F") . " \n";
      print "   Dewpoint in C = " . $this->getDewpoint(0,"C") . " \n";
      print "   Relative Humidity = " . $this->getRelativeHumidity(0) . " \n";
      print "   Visibility in Mi = " . $this->getVisibility(0,"mi") . " \n";
      print "   Visibility in Km = " . $this->getVisibility(0,"km") . " \n";
      print "   Barometer Status = " . $this->getBarometerStatus(0) . " \n";
      print "   Barometer in Inches = " . $this->getBarometer(0,"in") . " \n";
      print "   Barometer in cm = " . $this->getBarometer(0,"cm") . " \n";
      print "   Barometer in kPa = " . $this->getBarometer(0,"kpa") . "\n";
      print "   Last Updated = " . $this->getUpdateTime(0) . "\n";
      print "   Image URL = " . $this->getImageURL(0) . " \n";
      print "</pre>";
    
    }
    
    
    function Weather($cityID,$cachedir = null,$cachetimeout = 21600) {
          
      // Build the URL from the cityID
      $url = "http://w3.weather.com/outlook/travel/local/$cityID?"; 

      // Create our WebCell object
      $this->wcObject = new WebCell($url);

      // enable cache if possible
      if (!is_null($cachedir)) $this->wcObject->EnableCache($cachedir, $cachetimeout);
      
      // Load our page
      $this->wcObject->UserAgent("PHPWeather 2.9 (+http://www.ravis.org/code/weather/)");
      $CanConnect = $this->wcObject->GetPage();
      if (!$CanConnect) {
        $this->Connected = false;
        return(false);
      } else {
        $this->Connected = true;
      }
	  
      // Grab the html raw data
      $HTMLData = $this->wcObject->GetRawHTML();
	  
      // Parse out a lot of stuff that changes often (breaking the script)
      // and that just slows us down anyway...
      if (!preg_match("/<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>.*?<\/table>.*?<\/table>/is",$HTMLData,$temp)) {
        print "Could not parse weather data / see source";
        print "<!--\n\n\n\n==== COULD NOT PARSE WEATHER DATA ====\n\nTry checking http://www.ravis.org/code/weather/ for updates\n\n\n\n-->";
        return(false);
      }
      $HTMLData = $temp[0];
      
      // send the html back to webcell
      $this->wcObject->SetRawHTML($HTMLData);
      
      if ($this->isWeatherDebug()) {
        $this->outputDebug();
        print $this->wcObject->GetRawHTML();
        for ($table=0; $table<$this->wcObject->NumberOfTables(); $table++)
          $this->wcObject->PrintTable($table);
      }

    }
    
    
    }



?>