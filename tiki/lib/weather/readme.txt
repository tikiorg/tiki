PHP Weather Script


Copyright
---------
This script is copyright 2000-2003 by Travis Richardson (http://www.ravis.org).
You have my permission to use, distribute, and modify it free of charge so
long as this copyright notice remains intact.
  
You can always get the latest version at http://www.ravis.org/code/weather/
  
If you make changes or fix any bugs that appear feel free to send them to
me and I may include them in future releases.


About 
-----
Weather is a PHP script to provide weather data for cities around the
world. When I was looking for such a script to integrate into my site I
couldn't find anything that suit my needs. Most of them simply combined
explode, replace and ereg statements and provided html code that was hard to
modify. The one script I did find that looked to be exactly what I wanted
read METAR reports, which, unfortunately, did not list the cities I was
looking to use.

Then I stumbled on the script Current Weather by greedo which grabbed data
from Weather.com. Weather.com provides data on almost everything and
provided all the information I needed. However, greedo's script only
returned the raw HTML formatted in Weather.com's site style. I wanted the
data itself, not the HTML, so I "extended" the script to a complete PHP
class. It's simple to use and provides all the data in variables that you
can easily enter into your site in whatever format you choose.


How to... 
---------

See sample.php (included in the zip) for an example.

____________
:: STEP 1 ::

Place the following include statement in your script:

<? include("path/to/Weather.php"); ?> 

You will (of course) need to substitute path/to/Weather.php with 
the path, i.e.: scripts/Weather.php

____________
:: STEP 2 ::

Before you can use the weather data you must fetch it. To do so place the
following line in your file before you attempt to call the data functions:

<? $weatherData = New Weather("LOCATION_STRING"); ?> 

substitute LOCATION_STRING with the string from Weather.com - e.g.: Bangkok, 
Thailand is "THXX0002". Find the location you want to use by going to 
Weather.com and finding the city you want a weather report for. Look at the 
URL for your LOCATION_STRING. 
eg: http://www.weather.com/weather/local/THXX0002

____________
:: STEP 3 ::

Now you have your weather data contained in the $weatherData object. To use
it simply call one of the data functions below. Since these return actual
variables and not HTML code you can use them in scripts and calculate
various results such as the difference in temperature between two location
for example. Note that not all locations will report all items - Alaska is
unlikely to have a Heat Index in the middle of winter and some locations
will (for various reasons) not report the latest weather. If this occurs the
variable will not exist and the function you call will return false. You may
want to check for this before relying on the results you expect. Most of the
functions require one or two parameters, DAY and METRIC. 

DAY is the day you are looking to get data for. 0 (zero) is the current
weather, 1 is today/tonight's general forecast, and 2-10 are forecasts for
the next 9 days. NOTE: DAY currently does not do anything. It's there 
because Weather.com reports the data and I plan to write code to read it.

METRIC is the unit of measure you want the result to be returned in and is
explained for each function below.


Here are the available function calls: 

getLocation() - Returns the location of the weather readings - not all
cities return their own weather reports

getUpdateTime(DAY) - Returns a weather.com formatted string containing 
the date/time (and possibly time zone) of the last weather report.

getCondition(DAY) - Returns a string of the weather condition - e.g.:
"Fair", "Cloudy", "Raining", etc.

getImageURL(DAY) - Returns the URL for the image provided by Weather.com -
this image is a visual representation of the condition

getTemp(DAY,METRIC) - Returns the temperature - METRIC is "c" (Celsius) or
"f" (Fahrenheit)

getWindChill(DAY,METRIC) - Returns the temperature adjusted for current
wind-chill conditions - METRIC is "c" or "f"

getHeatIndex(DAY,METRIC) - Returns the temperature adjusted for current
heat index conditions - METRIC is "c" or "f"

getWindSpeed(DAY,METRIC) - Returns the wind speed - METRIC is "mph" or
"kph"

getWindDirection(DAY) - Returns a string of the current wind direction -
e.g.: "North", "Southeast", etc. - Weather.com reports the wind direction
as "blowing from" - this script switches it around so the returned string
is the actual direction the wind is blowing in

getDewpoint(DAY,METRIC) - Returns the dewpoint temperature - METRIC is "c"
or "f"

getRelativeHumidity(DAY) - Returns the relative humidity (a percentage)

getVisibility(DAY,METRIC) - Returns the visibility distance - METRIC is
"mi" (miles) or "km" (kilometers)

getBarometerStatus(DAY) - Returns the barometer's status - e.g.: "Rising"
or "Falling" - returns false if barometer is stable

getBarometer(DAY,METRIC) - Returns the barometer height - METRIC is "in"
(inches), "cm" (centimeters), or "kpa" (kPa)


Legal 
-----
Remember that you must get permission from Weather.com in order to
grab their data and use it on your site. So far as I know they have a
program where you can sign up and get permission to place some of their code
on your site. Whether this script falls within those terms of use guidelines
or not I cannot say. I am not responsible for your use of this script or any
resulting legal action. In other words, cover your butt and ask their
permission.

So far as the rights on this script are concerned I give you full rights to
use, distribute, and modify this script, free of charge, as long as the
copyright notice at the top is maintained. If you do make changes feel free
to send them to me and I'll consider including them in future releases.
Since I am providing this script free of charge I also take no
responsibility for any damage or losses that may occur while using this
script. Use at your own risk.

