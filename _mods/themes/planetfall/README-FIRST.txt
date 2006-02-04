This is a Tiki adaptation of the Planetfall theme (http://mambo.rhuk.net/) originally done by rhuk for the Mambo CMS (now Joomla). It's been tested with Tiki 1.9.2. Maintained by Gary Cunningham-Lee (chibaguy). For the latest information on and updates of the Planetfall theme for TikiCMS, visit http://zukakakina.com or http://themes.tikiwiki.org. 

This readme describes things to be aware of and do BEFORE installing/selecting this theme at your site, how to install the theme, and license information.

-=Before Installing/Selecting this theme=-

* Site Identity feature

Tiki's Site Identity feature should not be turned on, naturally, since it would be redundant. Also, the "Flip Columns" and "Module Controls" will make things ugly so should not be used. The "Flip Columns" code is still in the tiki.tpl file, but commented out (deactivated), so turning on "Flip Columns" in your Tiki Admin page will have no effect on this theme.

* Menus

This theme is configured to use a PhpLayers menu id=43.

IMPORTANT: If Planetfall is installed and selected before menu id=43 is created, no page will display below the logo graphic.

Edit the templates/styles/planetfall/tiki.tpl file (around line 23) to change the menu id number if another menu is to be used. Keep in mind that a horizontal menu with too many section items (more than seven or so) will spread the page wider than normal. To use a menu with more sections than this, it would be better to replace the PhpLayers menu tag with maybe a row of important links, and use a Tiki menu in a side column.

If there is an existing menu id=43 but it is too wide to use horizontally, make a new, more compatible menu and edit Planetfall's tiki.tpl file to update the menu id number.

-=Header images=-
The header images can be changed by using new images with the same names as the default images. The CSS file specifies a header_bg.jpg (the background image) and a header_logo.png (the logo image). Simply upload new images with the same names to the styles/planetfall/ directory. The dimensions of the background image should be the same as those of the default background image. The dimensions of the logo image should be the same or less than that of the default logo image. If the logo is smaller than the background, then its position can be indicated as a CSS property. If images with names other than these are used, the CSS file must be edited to reflect that change. See the illustration for how the graphics are placed, etc.

td#planetfall_header (line 114 or so of planetfall.css) holds the background image ? the earth and space (header_bg.jpg). The image is specified not to repeat and to align left, and the table cell has a background color that matches the color of the right side of the image ("space"). This enables the page to be spread horizontally and still have a continuous image in the header area.

div#planetfall_logo (line 119 or so) holds the "foreground" image ? the logo text (header_logo.png).

At least the foreground image should be replaced in order to personalize the theme.

With the default images, header_logo.png has the same dimensions as the background image (and in fact could be used alone with the same end effect), but I specified separate background and logo images for the most flexibility. An animated gif image and/or text could also be used in the foreground, in div#planetfall_logo.

header_logo_original is a Macromedia Fireworks .png file. If opened in that program, the logo text can be edited, the layers are editable, etc. The font in the default logo is called "Planet X".

-=Header module=-
The original Planetfall theme (for Mambo) has a search box in the top right of the page. Users may want to have some other module in that area. This can be done easily, but keep in mind the space is just 111px tall, so the module should fit in that vertical space.

The module is specified in the tiki.tpl template, at around line 19. To change the module, edit the line to state the name of the module's template (such as "mod-search_box.tpl", "mod-google.tpl", etc.


-=About the left and right columns=-
Tiki normally uses both left and right columns, for modules. The planetfall theme mainly uses a single side column, on the right. In the original theme, there is a second side column, but it displays not on the left of the page, but at the right side of the center column. Because the theme has rather wide margins that already are limiting the width of the content area, it may be a good idea to put important modules in the right column and put less-important modules in the left column and then turn off the left column in most sections, especially sections that need a lot of content area such as image galleries. It would even be good to empty the left column of modules and turn it off entirely, if possible.


-=Installation=-
Copy or upload planetfall.css and the planetfall images directory to the styles/ directory of your Tiki site.
Copy or upload the planetfall templates directory to the templates/styles/ directory of your Tiki site.

-=License/copying=-

This theme and is offered under the LGPL license. This is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation.

This software is distributed in the hope that it will be useful, but without any warranty, without even the implied warranty of merchantability or fitness for a particular purpose. See the GNU Lesser General Public License (http://www.gnu.org/copyleft/lesser.txt) for more details.