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

$.buildAutoToc = function () {
	//if a wiki page, and if there is no toc-off div
	if (document.getElementById("page-data") && !document.getElementById("toc-off") && document.location.href.indexOf("tiki-print.php") == -1) {
		//get headers
		var headers = $("div#page-data").find(":header");

		//if there are more than one headers on the page
		if (headers.length > 1) {

			//**************Add the Toc Divs to the Page**************//

			//set target div IDs. these divs will be populated with an autoToc
			//innerToc and toc-static are required. Additional divs may be specified.
			var tocDivs = ["innerToc", "toc-static"];

			//string to add html for toc divs
			var tocString = "<div id='outerToc' contenteditable='false'> <div id='outerTocTitle'> <a href='#'> Table of Contents, or Click for Page Top</a></div><div id='innerToc' contenteditable='false'></div></div><div id='outerToc-static' contenteditable='false' class='opaque'><div id='toc-static'></div></div>";
			//create the div to hold the tocs, and populate its inner HTML with the tocString
			var tocContainer = document.createElement("div");
			tocContainer.innerHTML = tocString;

			//get pageData div, as we want to add toc before that
			var page = document.getElementById("page-data");

			//add the tocContainer as first child of page-data div
			var firstChild = page.firstChild;
			page.insertBefore(tocContainer, firstChild);

			//Configure starting header and ending header
			var largestHeader = 1;
			var smallestHeader = 6;

			//**************Iterate over headers and create autoToc HTML List**************//

			//start from indentation level 0, with header one as base
			var previousHeaderLevel = 1;

			//create object to store processed IDs. 
			var processedId = {};

			//function to process header Id generation. If an ID which has been processed is generated again and passed in again, the id name will be incremented to id_[1*]
			function processId(id) {
				if (id in processedId) {
					//if processed before
					//iterate count for header with this ane
					processedId[id] += 1;
					//set the new id to id plus count for header
					var newId = id + "_" + processedId[id];
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
			for (var i = 0; i < headers.length; i++) {

				//get header level for header to see if this header should be processed or not
				var headerLevel = parseInt(headers[i].tagName[1]);

				//If the header level in range of acceptable headers, begin processing it
				if (headerLevel >= largestHeader && headerLevel <= smallestHeader) {

					//store browser location to append the #[1*/header text] to
					var browserLocation = document.location.href;

					//Make the base URL for the link as everything up to page name plus hash. multistructure pages might have issues with this, but we are avoiding multistruction pages to avoid multicategory pages, so should be fine. For links to headers (via ids) to work, you need to have a clean url like cewiki/pagename#id, with nothing after the id string: https://cewiki-dev.colorado.edu/es-Searching+for+a+Student+Record#Overview will work, but https://cewiki-dev.colorado.edu/es-Searching+for+a+Student+Record#Overview&structure=es-Enrollment+Services will not.
					var pointTo = browserLocation.substring(0, browserLocation.indexOf('&')) + "#";

					//current header
					var currentHeader = headers[i];		// header is a reserved word in IE

					//grab the whole header element to show as the anchor text
					// aText = $.trim(currentHeader.innerHTML); //trim incase inner html is text (if <hx>text</hx>)
					var aText = $.trim(currentHeader.textContent);

					//generate and set id if necessary (if element does not already have an id, create one)
					if (!currentHeader.id){
						//set the id to the the inner text of the header, with underscores instead of "<", ">", and spaces (" "). Increment Id checks if the ID has been assigned yet, and if so, increments the Id with a number at the end of the id name
						var id = processId(currentHeader.textContent.trim().replace(/ /g, "_").replace(/&lt;/g, "_").replace(/&gt;/g, "_"));

						//set the element's id to the constructed ID
						currentHeader.setAttribute("id", id);
					} else {
						id = currentHeader.id;
					}
					//construct the URL from the base URL with a # and the ID for the Div
					var url = pointTo + id; //url is baseurl#divId

					//create the HTML anchor item with the text from the header and pointing to baseurl#divId
					var itm = '<li><a href=' + url + '>' + aText + '</a></li>';

					//*****use the information gathered to write the string of the HTML for the list of anchors*****//

					//The following code determines how indented the previous header was, and closes or opens sublists to match that indentation level. 
					//Example: 
					//h1
					//h2
					//h3
					//h1
					//Above, if it had just written the h3, and it went to write the h1, its initial indentLevel would be 1, while previous would be 3, so it would know it needs to close sublists equal to the difference beteen its indentation level and that of h3. So, it would need to close two levels from h3 to get to h1, and then write the item. 
					//To do this, the code below either applies ++ or -- on the indentLevel variable while closing or opening sublists until the current indentLevel matches the previousHeaderLevel.

					var indentLevel = headerLevel;

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
						str += itm;
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
						str += itm;

						//elif header levels are same    
					} else if (indentLevel == previousHeaderLevel) {
						//write item
						str += itm;
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
			//make it so the non-static toc shows up only when you scroll past the first static top which sits at the top of the page. 
			//make it so that when you mouse over the non-static toc's outer container, the inner container shows up
			//make it so that when you scroll the main page, the innnerToc, which holds the toc, hides, and you just see the outerToc container. 
			//make it so when you scroll past the bottom of the innerToc overflow it doesn't scroll the page, preventing the autoToc from dissapearing
			
			//determine where the "mobile toc" should start showing up
			var static_toc = $('#outerToc-static');
			var start = $(static_toc).offset().top + $(static_toc).height();

			var outer = $('#outerToc'); 
			var inner = $('#innerToc');
			var title = $("#outerTocTitle");			
			
			//set on scroll window event to do: on scroll, hide the floating toc if you would overlap with the toc on the top of the page, otherwise hide the inner toc
			$.event.add(window, "scroll", function () {
				//position
				var p = $(window).scrollTop();

				//if current position is below the other toc and start of the page
				if (p > start) {
					//reset scrollbar position for inner toc to top
                                        inner.scrollTop(0);
					//set outertoc position to fixed at top of screen
					outer.css('display', 'block');
					//hide inner toc
					inner.css('display', 'none');
					//make outerToc slightly transparent
					outer.css('opacity','0.5');
				} else {
					//if current position overlaps with the static toc at top of page, hide non-static toc div
					outer.css('display', 'none');
				}
			});


			 //set onmouseover for outerToc to show inner toc and adjust inner toc height to parent so overflow correctly
             document.getElementById("outerToc").onmouseover = function () {
				//make the outerToc container more opaque
				outer.css('opacity','1');
				//show the inner toc div, displaying the toc
				inner.css('display','block');
				//set innerToc to appropriate height for overflow-y scroll
				inner.height(outer.height() - title.height() + "px");
             };

			//set mouse scroll on innerToc to not scroll main page when hit bottom of innerToc overflow
                        //code via http://stackoverflow.com/questions/16323770/stop-page-from-scrolling-if-hovering-div, thanks Troy Alford!
                        $('#innerToc').on('DOMMouseScroll mousewheel', function(ev) {
                            var $this = $(this),
                                scrollTop = this.scrollTop,
                                scrollHeight = this.scrollHeight,
                                height = $this.height(),
                                delta = (ev.type == 'DOMMouseScroll' ?
                                    ev.originalEvent.detail * -40 :
                                    ev.originalEvent.wheelDelta),
                                up = delta > 0;

                            var prevent = function() {
                                ev.stopPropagation();
                                ev.preventDefault();
                                ev.returnValue = false;
                                return false;
                            };

                            if (!up && -delta > scrollHeight - height - scrollTop) {
                                // Scrolling down, but this will take us past the bottom.
                                $this.scrollTop(scrollHeight);
                                return prevent();
                            } else if (up && delta > scrollTop) {
                                // Scrolling up, but this will take us past the top.
                                $this.scrollTop(0);
                                return prevent();
                            }
                        });
		}
	}
};
$.buildAutoToc();
