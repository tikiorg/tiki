This is the Snow theme for Tiki CMS (Tikiwiki). It's been tested with Tiki 1.9.2 and 1.9.3 (CVS). Maintained by Gary Cunningham-Lee (chibaguy). For the latest information on and updates of this theme for TikiCMS, visit http://zukakakina.com or http://themes.tikiwiki.org. 

This readme describes things to be aware of and do BEFORE installing/selecting this theme at your site, how to install the theme, and license information.

-=Before Installing/Selecting this theme=-

* Site Identity feature

Tiki's Site Identity feature could be turned on, and the "Flip Columns" and "Module Controls" can be used. In my testing, though, allowing user control of column display is not functioning properly in most Tiki sections. Module "window-shading" has been tested with this theme and works normally.

* Menus

This theme is configured to use a PHP Layers menu id=43 (but the menu tag must be activated to be used).

IMPORTANT: If this theme is installed and selected before menu id=43 is created, pages will not display from the horizontal menu on down.
PhpLayersMenu

The line of code to activate the menu is at around line 11 of the templates/styles/snow/tiki.tpl file, but is commented out. To use the menu, change the " {* " and " *} " tags in that line to " { " and " } " (in other wors, delete the asterisks) and, if necessary, change 43 to whatever menu id number you want to use.

Keep in mind that a horizontal menu with too many section items (more than seven or so) will spread the page wider than normal. To use a menu with more sections than this, it would be better to replace the PHP Layers menu tag with maybe a row of important links, and use a Tiki menu in a side column.

If there is an existing menu id=43 but it is too wide to use horizontally, make a new, more compatible menu and edit Snow's tiki.tpl file to update the menu id number.

-=Header/logo graphics=-
The Snow theme includes two options for the page-top graphics: a jpeg topbar image and Flash snowfall effect. These will have to be modified for customized use at a new site.


-=Flash topbar=-
By default, the non-Flash tiki-top is used. To use a Flash file instead, rename templates/styles/snowfall/tiki-top_bar.tpl as templates/styles/snowfall/tiki-top_bar_noflash.tpl and rename templates/styles/snowfall/tiki-top_bar_flash.tpl to templates/styles/snowfall/tiki-top_bar.tpl. In other words, the theme comes with two files, either of which can be named tiki-top_bar.tpl to display the desired image/movie. Naming the unused top_bar file to something else keeps it available for future use instead of just letting it get over-written.

The Flash (.fla) file is from Flashkit.com where the description says to "use freely". I resized the .fla document, removed the button, and replaced the background image. Along with the default "Snow theme" .swf file, the .fla file is included with the theme (in the "Resources/Flash" directory) so a personalized Flash movie can be made.

The .fla file should be edited in Macromedia Flash or other .fla editor. Select the background layer and click "Edit/Cut". Import a new image file to be the background; it should be the same size as the original background file (734px by 95px) to keep things simple. "Publish" the movie to create the new .swf file. If the dimensions of the file are changed, update the Flash version of tiki-top_bar.tpl file accordingly.

-=Non-Flash topbar=-
The non-Flash version of the page top can be done different ways. The default treatment is a background image (header-bg.jpg) that tiles the width of the page. A logo image (logo.jpg) is specified to be centered on top of that.

To make a custom logo image that works in this arrangement, use the background image as a background layer pattern in your image editor and make new text layer on top of it, using one of the snowflake patterns as a texture for the text if you like.

The background image doesn't have to be used (edit snowfall.css) for a more plain top area, and the logo image can be changed accordingly.

The header images can be changed by using new images with the same names as the default images. The CSS file specifies a header_bg.jpg (the background image) and a logo.png (the logo image). Just upload new images with the same names to the styles/snow/ directory. The pixel height of the background image should be the same as that of the default background image to match the CSS specification for the tiki-top div height. And the height of the logo image should be the same or less than that of the default logo image. If the logo is smaller than the background, then its position can be indicated as a CSS property. If images with names other than these are used, the CSS file must be edited to reflect the change.

-=About the left and right columns=-
The Snow theme is designed to use Tiki's left and right columns normally.

-=Installation=-
Copy or upload snow.css and the snow/ directories and files to the styles/ directory of your Tiki site.
Copy or upload the snow templates directory to the templates/styles/ directory of your Tiki site.

-=License/copying=-

This theme and is offered under the LGPL license. This is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation.

This software is distributed in the hope that it will be useful, but without any warranty, without even the implied warranty of merchantability or fitness for a particular purpose. See the GNU Lesser General Public License (http://www.gnu.org/copyleft/lesser.txt) for more details.