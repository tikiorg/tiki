/* (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * $Id$
 *
 * Support for client side iconsets
 */

(function ($) {

	/**
	 * Get the element object for an icon depending on the current iconset
	 *
	 * @param name {string}        Name of the icon as defined in the iconset
	 * @returns {*|HTMLElement}
	 */
	$.fn.getIcon = function (name) {
		var icon = jqueryTiki.iconset.icons[name],
			$output = $(), attr = "";

		if (! icon) {
			pos = jqueryTiki.iconset.defaults.indexOf(name);
			if (pos > -1) {
				icon = { id: name };
			}
		}


		if (icon) {
			icon.tag     =  icon.tag     || jqueryTiki.iconset.tag;
			icon.prepend =  icon.prepend || jqueryTiki.iconset.prepend;
			icon.append  =  icon.append  || jqueryTiki.iconset.append;

			$output = $("<" + icon.tag + ">");
			attr = icon.prepend + icon.id + icon.append;

			if (icon.tag === "img") {
				$output.attr("src", attr);
			} else {
				$output.addClass(attr)
					.addClass("icon")
					.addClass("icon-" + name);
			}

		} else {
			$output = $().getIcon("warning");
			console.log("iconset: icon not found:" + name);
		}

		return $output;
	};

	/**
	 * Change an existing icon's icon
	 * Could be a span for a font-icon or an img for legacy
	 *
	 * @param name string    Name of the icon as defined in the iconset
	 *
	 */

	$.fn.setIcon = function(name) {

		var $icon = $(this).getIcon(name);

		if ($(this).children("img").length) {
			$(this).children($icon[0].tagName + ":first").replaceWith($icon);
		} else {
			$(this).replaceWith($icon);
		}
	}

})(jQuery);
