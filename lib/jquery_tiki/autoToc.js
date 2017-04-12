/* (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * $Id$
 *
 * Rewritten for bootstrap tiki 15.x 2015-2016
 * Based on work by Jobi Carter keacarterdev@gmail.com
 */

$.buildAutoToc = function () {
	var $page = $("body"),
		$top = $("#top");


	//if a wiki page, and if there is no toc-off div and not printing
	if ($top.length && !$("#toc-off").length && location.href.indexOf("tiki-print.php") == -1) {

		var $headers = $("h1.pagetitle", "#col1").add($(":header", $top)).not("#toctitle > h3");

		//if there are more than one $headers on the page
		if ($headers.length > 1) {

			var $tocDiv = $("<div id='autotoc' contenteditable='false' role='complimentary' style='position:relative' class='col-sm-3 autotoc' />");

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

			// open HTML $list
			var $list = $("<ul class='nav' />"),
					$currentList = $list,		// pointer to where to add items
					headerLevel,				// how deep we are
					previousHeaderLevel = 0; 	//start from indentation level 0, with header one as base


			//Iterate over the $headers
			$headers.each(function () {

				//get header level for header to see if this header should be processed or not
				var $this = $(this);
				headerLevel = parseInt($this.prop("tagName").substring(1));

				//grab the whole header element to show as the anchor text
				var aText = $.trim($this.text());

				//generate and set id if necessary (if element does not already have an id, create one)
				var id = $this.attr("id");
				if (!id){
					// Set the id to the the inner text of the header, with underscores instead of spaces (" ").
					// processId checks if the ID has been assigned yet, and if so, increments the Id with a number at the end of the id name
					id = processId(aText.replace(/\W/g, "_"));

				} else {
					id = id.replace(":", "\\:").replace(".", "\\.").replace("#", "\\#");
				}
				//set the element's id to the constructed ID
				$this.attr("id", id);
				//construct the anchor URL with chars jquery doesn't like escaped
				var url = "#" + id;

				//create the HTML anchor item with the text from the header and pointing to baseurl#divId
				var $item = $("<li><a href=" + url + ">" + aText + "</a></li>");


				if (previousHeaderLevel && headerLevel > previousHeaderLevel) {	// deeper level

					//open a new sublist for each level of difference
					var $lastItem = $("li:last", $currentList).append($("<ul class='nav' />"));
					$currentList = $("ul:last", $lastItem);

				} else if (headerLevel < previousHeaderLevel) {					// up some levels

					$currentList = $($currentList.parents("ul")[previousHeaderLevel - headerLevel - 1]);

				}

				$currentList.append($item);

				//set current header level to previous header level for next iteration
				previousHeaderLevel = headerLevel;
			});

			// append the $list
			$("#page-data").addClass("col-sm-9");
			$tocDiv.append($list).appendTo($top);

			// trigger the bootstrap affix and scrollspy
			$page.scrollspy({ target: "#autotoc" });

			$("> .nav", "#autotoc").affix({
				offset: {
					top: function () {
						return (this.top = $('.page-header').outerHeight(true) + 20)
					},
					bottom: function () {
						return (this.bottom = $('.footer').outerHeight(true))
					}
				}
			})
			.on('affix.bs.affix', function (e) {
				$(e.target).width(e.target.offsetWidth)
			});

		}
	}
};
$.buildAutoToc();
