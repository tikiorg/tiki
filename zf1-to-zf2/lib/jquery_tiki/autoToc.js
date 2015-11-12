/*
 * $Id$
 *
 * Rewritten for bootstrap tiki 15.x November 2015
 * Based on work by Jobi Carter keacarterdev@gmail.com
 */

$.buildAutoToc = function () {
	var $page = $("#top");

	//if a wiki page, and if there is no toc-off div
	if ($page.length && !$("#toc-off").length && location.href.indexOf("tiki-print.php") == -1) {
		//get $headers
		var $headers = $page.find(":header");

		//if there are more than one $headers on the page
		if ($headers.length > 1) {

			var $tocDiv = $("<div id='autotoc' contenteditable='false' role='complimentary' style='position:relative' class='col-sm-3 autotoc' />");

			//start from indentation level 0, with header one as base
			var previousHeaderLevel = 0;

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

			//open HTML $list and initialize string which will store the HTML $list
			var $list = $("<ul class='nav nav-tabs' role='tablist' />"),
					$currentList = $list,
					$prevList = null,
					headerLevel,
					indentLevel;

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

				indentLevel = headerLevel;

				//if header is a sub header (nested on a child level of the previous header), meaning its indent level is greater than that of the previousHeaderLevel
				if (previousHeaderLevel && indentLevel > previousHeaderLevel) {
					//open a new sublist for each level of difference

					$prevList = $currentList;
					$currentList = $("<ul class='nav' />");

				} else if (indentLevel < previousHeaderLevel) {

					$list.append($currentList);
					$currentList = $prevList;
					$prevList = null;

				}

				$currentList.append($item);

				//set current header level to previous header level for next iteration
				previousHeaderLevel = headerLevel;
			});

			if ($currentList) {
				$list.append($currentList);
			}

			// append the $list
			$tocDiv.append($list).appendTo($page);

			$("#page-data").addClass("col-sm-9");

			// trigger the bootstrap affix and scrollspy
			$page.scrollspy({ target: "#autotoc > .nav" });
			$("#autotoc > .nav").affix({
				offset: {
					top: 0,
					bottom: function () {
						return (this.bottom = $('.footer').outerHeight(true))
					}
				}
			});

		}
	}
};
$.buildAutoToc();
