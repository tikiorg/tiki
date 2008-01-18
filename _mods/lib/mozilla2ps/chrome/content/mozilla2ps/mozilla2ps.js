/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

    Michele Baldessari <michele@pupazzo.org> 2006
*/

var paperName    = 'A4';
var bgColors     = false;
var bgImages     = false;
var marginTop    = 0;
var marginBottom = 0;
var marginLeft   = 0;
var marginRight  = 0;
var gPrintSettingsInterface = Components.interfaces.nsIPrintSettings;
var orientation  = gPrintSettingsInterface.kPortraitOrientation;

function ddump(text)
{
  dump(text + "\n");
}

function ddumpObject(obj, name, maxDepth, curDepth)
{
  if (curDepth == undefined)
    curDepth = 0;
  if (maxDepth != undefined && curDepth > maxDepth)
    return;

  var i = 0;
  for (prop in obj)
  {
    i++;
    if (typeof(obj[prop]) == "object")
    {
      if (obj[prop] && obj[prop].length != undefined)
        ddump(name + "." + prop + "=[probably array, length "
              + obj[prop].length + "]");
      else
        ddump(name + "." + prop + "=[" + typeof(obj[prop]) + "]");
      ddumpObject(obj[prop], name + "." + prop, maxDepth, curDepth+1);
    }
    else if (typeof(obj[prop]) == "function")
      ddump(name + "." + prop + "=[function]");
    else
      ddump(name + "." + prop + "=" + obj[prop]);
  }
  if (!i)
    ddump(name + " is empty");    
}

function WebProgressListener() {
}

WebProgressListener.prototype = {
  _requestsStarted: 0,
  _requestsFinished: 0,

  QueryInterface: function(iid) {
    if (iid.equals(Components.interfaces.nsIWebProgressListener) ||
        iid.equals(Components.interfaces.nsISupportsWeakReference) ||
        iid.equals(Components.interfaces.nsISupports))
      return this;
    
    throw Components.results.NS_ERROR_NO_INTERFACE;
  },

  onStateChange: function(webProgress, request, stateFlags, status) {
    const WPL = Components.interfaces.nsIWebProgressListener;

    if (stateFlags & WPL.STATE_IS_REQUEST) {
      if (stateFlags & WPL.STATE_START) {
        this._requestsStarted++;
      } else if (stateFlags & WPL.STATE_STOP) {
        this._requestsFinished++;
      }
    }

    if (stateFlags & WPL.STATE_IS_NETWORK) {
      if (stateFlags & WPL.STATE_STOP) {
        this.onStatusChange(webProgress, request, 0, "Done");
        this._requestsStarted = this._requestsFinished = 0;
      }
    }
  },

  onProgressChange: function(webProgress, request, curSelf, maxSelf,
                             curTotal, maxTotal) {
  },

  onLocationChange: function(webProgress, request, location) {
  },

  onStatusChange: function(webProgress, request, status, message) {
    if (status == 0) {
      /* I haven't figured out why calling print2ps() without a small timeout makes the print
       * component fail. 
       */
      setTimeout('print2ps()', 500);
    }
  },

  onSecurityChange: function(webProgress, request, state) {
  }
};

function quit() {
	window.close();
}

// all progress notifications are done through the nsIWebProgressListener implementation...
var printProgressListener = {
    onStateChange: function(aWebProgress, aRequest, aStateFlags, aStatus) {
      const WPL = Components.interfaces.nsIWebProgressListener;
      if (aStateFlags & WPL.STATE_STOP)
      {
        // Without a small timeout, quitting right away makes xulrunner segfault.
	// Need to investigate
        setTimeout('quit()', 500);
      }
    },
    
    onProgressChange: function(aWebProgress, aRequest, aCurSelfProgress, aMaxSelfProgress, aCurTotalProgress, aMaxTotalProgress) {
    },

    onLocationChange: function(aWebProgress, aRequest, aLocation) {
    },

    onStatusChange: function(aWebProgress, aRequest, aStatus, aMessage)
    {
    },

    onSecurityChange: function(aWebProgress, aRequest, state) {
    },

    QueryInterface : function(iid)
    {
     if (iid.equals(Components.interfaces.nsIWebProgressListener) || iid.equals(Components.interfaces.nsISupportsWeakReference))
      return this;
     
     throw Components.results.NS_NOINTERFACE;
    }
};

function print2ps() {
     ddump("print2ps start");
     var browser = document.getElementById("browser");
     var ifreq = content.QueryInterface(Components.interfaces.nsIInterfaceRequestor);
     var webBrowserPrint = ifreq.getInterface(Components.interfaces.nsIWebBrowserPrint);
     var nsCommandLine = window.arguments[0];
     nsCommandLine = nsCommandLine.QueryInterface(Components.interfaces.nsICommandLine);
     var dest = nsCommandLine.getArgument(1);
     var gPrintSettings = webBrowserPrint.globalPrintSettings;

     var runtime = Components.classes["@mozilla.org/xre/app-info;1"]
   			.getService(Components.interfaces.nsIXULRuntime);
     var OS = runtime.OS;

     try {
         gPrintSettings.orientation     = orientation;
         gPrintSettings.marginTop       = marginTop;
         gPrintSettings.marginBottom    = marginBottom;
         gPrintSettings.marginLeft      = marginLeft;
         gPrintSettings.marginRight     = marginRight;
         gPrintSettings.printBGColors   = bgColors;
         gPrintSettings.printBGImages   = bgImages;
         gPrintSettings.footerStrLeft   = "";
         gPrintSettings.footerStrCenter = "";
         gPrintSettings.footerStrRight  = "";
         gPrintSettings.headerStrLeft   = "";
         gPrintSettings.headerStrRight  = "";
         gPrintSettings.headerStrCenter = "";
	 gPrintSettings.printToFile = true;
	 gPrintSettings.printSilent = true;
	 gPrintSettings.toFileName = dest;
	 // Adobe Postscript Drivers are expected (together with a FILE: printer called 
	 // "Generic PostScript Printer". Drivers can be found here:
	 // http://www.adobe.com/support/downloads/product.jsp?product=44&platform=Windows
	 if (OS == "WINNT") { 
		 gPrintSettings.printerName = "Generic PostScript Printer";
	 } else {
		 gPrintSettings.printerName = "PostScript/default";
	 }
	 gPrintSettings.paperName = paperName;
	 gPrintSettings.showPrintProgress = false;
         webBrowserPrint.print(gPrintSettings, printProgressListener);
     } catch (e) { 
	 ddump(e); 
     }
     ddump("print2ps finished");
};

var listener;

function onload() {
  var browser = document.getElementById("browser");
  var nsCommandLine = window.arguments[0];
  nsCommandLine = nsCommandLine.QueryInterface(Components.interfaces.nsICommandLine);
  var from = nsCommandLine.getArgument(0);
  
  if (nsCommandLine.length != 2) {
    ddump("Wrong number of arguments. Expected <source> <destination>");
    window.close();
  }
  
  browser.loadURI(from, null, null);
  listener = new WebProgressListener();
  browser.addProgressListener(listener,
    Components.interfaces.nsIWebProgress.NOTIFY_ALL);
}

var browser = document.getElementById("browser");
var nsCommandLine = window.arguments[0];
nsCommandLine = nsCommandLine.QueryInterface(Components.interfaces.nsICommandLine);

var tmp_paperName = nsCommandLine.handleFlagWithParam("papername", false);
if(tmp_paperName != null) {
  paperName = tmp_paperName;
}

var tmp_bgcolors = nsCommandLine.handleFlagWithParam("bgcolors", false);
if(tmp_bgcolors == "true" || tmp_bgcolors == "false") {
  bgColors = (tmp_bgcolors == "true") ? true : false;
}

var tmp_bgimages = nsCommandLine.handleFlagWithParam("bgimages", false);
if(tmp_bgimages == "true" || tmp_bgimages == "false") {
  bgImages = (tmp_bgimages == "true") ? true : false;
}

var tmp_margins = nsCommandLine.handleFlagWithParam("margins", false);
if(tmp_margins != null) {
  marginTop    = tmp_margins;
  marginBottom = tmp_margins;
  marginLeft   = tmp_margins;
  marginRight  = tmp_margins;
}

var tmp = nsCommandLine.handleFlagWithParam("marginTop", false);
if(tmp != null) {
  marginTop = tmp;
}

tmp = nsCommandLine.handleFlagWithParam("marginBottom", false);
if(tmp != null) {
  marginBottom = tmp;
}

tmp = nsCommandLine.handleFlagWithParam("marginLeft", false);
if(tmp != null) {
  marginLeft = tmp;
}

tmp = nsCommandLine.handleFlagWithParam("marginRight", false);
if(tmp != null) {
  marginRight = tmp;
}

var tmp_landscape = nsCommandLine.handleFlagWithParam("landscape", false);
if(tmp_landscape == "true") {
  orientation = gPrintSettingsInterface.kLandscapeOrientation;
}


if (nsCommandLine.length != 2) {
  ddump("Wrong number of arguments. Expected <source> <destination>");
  window.close();
}

var from = nsCommandLine.getArgument(0);
browser.loadURI(from, null, null);
addEventListener("load", onload, false);
