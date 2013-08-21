// $Id$
/*
file: autoToc.js
author: Jobi Carter
contact: keacarterdev@gmail.com
license: WTFPL
purpose: Automatically generate two table of contents on tiki pages with more than one header. The tocs are composed of clickable links to headers. One toc stays on the top of the page, while the seconds scrolls along with the page (css necessary) and hides until moused over.
last updated: 08/09/13
 
This script can be implimented in Tiki Wiki via editing the tiki-setup.php file and adding the following to the end of it. 
	$headerlib->add_jsfile('path/to/file/here');
	
Generated output / What this does
	This script will generate two divs which contain a clickable table of contents for the headers on the page. One sits at the top of the page, and the other appears when you scroll past the one on the top. When you scroll past the top toc, a tab appears on the top of the page. When you mouse over this tab, a table of contents will appear. Clicking an item in the toc, or scrolling the page, will make the tab close again. The tab will be semi-transparent until it is moused over, and the toc appears. You can also click the tab to be taken to the top of the page.

disabling the toc
		turn off this script by adding a div with the id "toc-off" to a page. 
		this script can be turned off for a category by adding a module with the "toc-off" div, and setting that module to only appear on that category.
		the script can be set to apply only to specific headers via the largestHeader and smallestHeader variables
		the script can be completely disabled by removing the headerLib entry in tiki-setup.php
		 
This script does the following:
		determine if wiki page
		determine if page has the "toc-off" 
		determine if more than one header on page
		insert toc HTML
		iterate over page and set Ids for header elements
			This is necessary because WYSIWYG fails to set correct Ids consistently. 
		While setting header Ids, generate a HTML indented list of header anchors which point to their respective headers via header id
		set mouseover and onScroll events for two tocs

The following CSS should be applied to your theme's css file for correct behavior. You may need to adjust margin (where the toc that scrolls with you shows up) based on your theme:

	div#outerToc {
	        position:fixed;
	        top:0px;
	        margin-left:25%;
	        background:#E0E0E0;
	        display:none;
	        padding:10px;
	        border-radius:0px 0px 5px 5px;
	        z-index:5;
	}

*/

$(function () {
	//if a wiki page, and if there is no toc-off div
	if (document.getElementById("page-data") && !document.getElementById("toc-off") && document.location.href.indexOf("tiki-print.php") == -1) {
		//get headers
		headers = $("div#page-data").find(":header");

		//if there are more than one headers on the page
		if (headers.length > 1) {

			//**************Add the Toc Divs to the Page**************//

			//set target div IDs. these divs will be populated with an autoToc
			//innerToc and toc-static are required. Additional divs may be specified.
			tocDivs = ["innerToc", "toc-static"];

			//string to add html for toc divs
			tocString = "<div id='outerToc' contenteditable='false'> <a href='#'> Table of Contents, or Click for Page Top</a><div id='innerToc' contenteditable='false'></div></div><div id='outerToc-static' contenteditable='false'><p>Table of Contents</a><div id='toc-static'></div></div>";

			//create the div to hold the tocs, and populate its inner HTML with the tocString
			tocContainer = document.createElement("div");
			tocContainer.innerHTML = tocString;

			//get pageData div, as we want to add toc before that
			page = document.getElementById("page-data");

			//add the tocContainer as first child of page-data div
			firstChild = page.firstChild;
			page.insertBefore(tocContainer, firstChild);

			//Configure starting header and ending header
			largestHeader = 1;
			smallestHeader = 6;

			//**************Iterate over headers and create autoToc HTML List**************//

			//start from indentation level 0, with header one as base
			previousHeaderLevel = 1;

			//create object to store processed IDs. 
			var processedId = new Object();

			//function to process header Id generation. If an ID which has been processed is generated again and passed in again, the id name will be incremented to id_[1*]
			function processId(id) {
				if (id in processedId) {
					//if processed before
					//iterate count for header with this ane
					processedId[id] += 1;
					//set the new id to id plus count for header
					newId = id + "_" + processedId[id];
				} else {
					//if not processed before
					//add to "dictionary' with count of 0
					processedId[id] = 0;
					//return id passed in
					newId = id;
				}
				return newId;
			}

			//open HTML list and initialize string which will store the HTML list 
			var str = '<ul>';

			//Iterate over the headers
			for (i = 0; i < headers.length; i++) {

				//get header level for header to see if this header should be processed or not
				headerLevel = parseInt(headers[i].tagName[1]);

				//If the header level in range of acceptable headers, begin processing it
				if (headerLevel >= largestHeader && headerLevel <= smallestHeader) {

					//store browser location to append the #[1*/header text] to
					browserLocation = document.location.href;

					//Make the base URL for the link as everything up to page name plus hash. multistructure pages might have issues with this, but we are avoiding multistruction pages to avoid multicategory pages, so should be fine. For links to headers (via ids) to work, you need to have a clean url like cewiki/pagename#id, with nothing after the id string: https://cewiki-dev.colorado.edu/es-Searching+for+a+Student+Record#Overview will work, but https://cewiki-dev.colorado.edu/es-Searching+for+a+Student+Record#Overview&structure=es-Enrollment+Services will not.
					pointTo = browserLocation.substring(0, browserLocation.indexOf('&')) + "#";

					//current header
					header = headers[i]

					//grab the whole header element to show as the anchor text
					aText = header.innerHTML.trim(); //trim incase inner html is text (if <hx>text</hx>)

					//if the header is complicated (composed of multiple HTML Elements)
					if (header.children.length > 0) {
						//set the id to the the inner text of the header, with underscores instead of "<", ">", and spaces (" "). Increment Id checks if the ID has been assigned yet, and if so, increments the Id with a number at the end of the id name
						id = processId($(aText).text().trim().replace(/ /g, "_").replace(/&lt;/g, "_").replace(/&gt;/g, "_"))

						//if the header is simple
					} else {
						//the inner html of the header is just text, so use anchor text for the id directly
						id = processId(aText.replace(/ /g, "_").replace(/&lt;/g, "_").replace(/&gt;/g, "_")) //replace all spaces with underscore, and special chars
					}

					//construct the URL from the base URL with a # and the ID for the Div
					url = pointTo + id; //url is baseurl#divId

					//set the element's id to the constructed ID
					//Need to do this because WYSIWYG does not set IDs on divs correctly. May be able to set flag to check if tiki editor, and mimic how it creates IDs, avoiding this step. 
					header.setAttribute("id", id);

					//create the HTML anchor item with the text from the header and pointing to baseurl#divId
					item = '<li><a href=' + url + '>' + aText + '</a></li>';

					//*****use the information gathered to write the string of the HTML for the list of anchors*****//

					//The following code determines how indented the previous header was, and closes or opens sublists to match that indentation level. 
					//Example: 
					//h1
					//h2
					//h3
					//h1
					//Above, if it had just written the h3, and it went to write the h1, its initial indentLevel would be 1, while previous would be 3, so it would know it needs to close sublists equal to the difference beteen its indentation level and that of h3. So, it would need to close two levels from h3 to get to h1, and then write the item. 
					//To do this, the code below either applies ++ or -- on the indentLevel variable while closing or opening sublists until the current indentLevel matches the previousHeaderLevel.

					indentLevel = headerLevel;

					//if header is a sub header (nested on a child level of the previous header), meaning its indent level is greater than that of the previousHeaderLevel
					if (indentLevel > previousHeaderLevel) {
						//open a new sublist for each level of difference

						//this is an alternative way to think about this
						//~ openCount = indentLevel - previousHeaderLevel 
						//~ for i in range(openCount):
						//~ str +="<ul>"
						//~ str+= item

						while (indentLevel > previousHeaderLevel) {
							//open sublist
							str += "<ul>";
							//track header level vs previous header to tell when to stop
							indentLevel -= 1;
						}
						//when header levels match, write item
						str += item;
					}
					//else, if header is higher up (parent level of list, but wont be a parent of the page because below)
					else if (indentLevel < previousHeaderLevel) {
						//close sublists for each level of difference
						while (indentLevel < previousHeaderLevel) {
							//close sublist
							str += "</ul>";
							//track header level vs previous header to tell when to stop
							indentLevel += 1;
						}
						//when header levels match, write item
						str += item;

						//elif header levels are same    
					} else if (indentLevel == previousHeaderLevel) {
						//write item
						str += item;
					}
				}
				//set current header level to previous header level for next iteration
				previousHeaderLevel = headerLevel;
			}

			//close list
			str += '</ul>';

			//set target div's inner html to the list
			for (i = 0; i < tocDivs.length; i++) {
				document.getElementById(tocDivs[i]).innerHTML = str;
			}


			//**************Add special mouse over and scroll behavior to toc**************//
			//make it so the non-static toc shows up on top of the page when you scroll past the first static top which sits at the top of the page. 
			//make it so that when you mouse over the non-static toc's outer container, the inner container shows up
			//make it so that when you scroll, the innner-toc, which holds the toc, hides, and you just see the outerToc container. 

			//set onmouseover for outerToc 
			document.getElementById("outerToc").onmouseover = function () {
				//closure, sets "scope/context" for function setVis on mouse over
				var inner = document.getElementById('innerToc');
				var outer = document.getElementById('outerToc');

				function setVis(inner, outer) {
					//make the outerToc container more opaque
					outer.style.opacity = 1;
					//show the inner toc div, displaying the toc
					inner.style.display = 'block';
				}

				return setVis(inner, outer);
			}

			//set the scroll functionality
			//determine where the "mobile toc" should start showing up
			static_toc = $('#outerToc-static');
			start = $(static_toc).offset().top + $(static_toc).height();

			//assign inner and outer vars for window scroll event
			var outer = $('#outerToc'); //$(div);
			var inner = $('#innerToc');

			//set on scroll event to do: on scroll, hide the floating toc if you would overlap with the toc on the top of the page, otherwise hide the inner toc
			$.event.add(window, "scroll", function () {
				//position
				var p = $(window).scrollTop();

				//if current position is below the other toc and start of the page
				if (p > start) {
					//set outertoc position to fixed at top of screen
					outer.css('display', 'block');
					//hide inner toc
					inner.css('display', 'none');
					//make outerToc slightly transparent
					var elemtoc = document.getElementById('outerToc');
					if (elemtoc != null) {
						elemtoc.style.opacity = .5
					}
				} else {
					//if current position overlaps with the static toc at top of page, hide non-static toc div
					outer.css('display', 'none');
				}
			});
		}
	}
});
