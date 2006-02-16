This is a Tiki adaptation of the Kubrick theme (http://mambo.rhuk.net/) originally done by Michael Heilemann for the WordPress blog software. It's been tested with Tiki 1.9.3(CVS). Maintained by Gary Cunningham-Lee (chibaguy). For the latest information on and updates of the Kubrick theme for TikiCMS, visit http://zukakakina.com or http://themes.tikiwiki.org. 

This readme describes things to be aware of and do BEFORE installing/selecting this theme at your site, how to install the theme, and license information.

-=Before Installing/Selecting this theme=-

* Site Identity feature

Tiki's Site Identity feature should not be turned on, naturally, since it would be redundant. Also, the "Flip Columns" and "Module Controls" will make things ugly so should not be used. The "Flip Columns" code is still in the tiki.tpl file, but commented out (deactivated), so turning on "Flip Columns" in your Tiki Admin page will have no effect on this theme.

* Menus

This theme is configured to use a PhpLayers menu id=43.

IMPORTANT: If Kubrick is installed and selected before menu id=43 is created, it is possible that no page will display below the logo graphic.

Edit the templates/styles/kubrick/tiki.tpl file and templates/styles/kubrick/error.tpl (around line 14) to change the menu id number if another menu is to be used. Keep in mind that a horizontal menu with too many section items (more than seven or so) will spread the page wider than normal. To use a menu with more sections than this, it would be better to not use the PhpLayers menu, and use a Tiki menu in a side column instead.

If there is an existing menu id=43 but it is too wide to use horizontally, make a new, more compatible menu and edit Kubrick's tiki.tpl file to update the menu id number.

-=Page Header Images=-
The header images can be changed by using new images with the same names as the default images. In the header graphics area, the CSS file specifies a header_left.jpg and header_right.jpg that "cap" the two sides and, between them, a header_bg.jpg that tiles horizontally as a background image and a header_logo.png (the logo image) that's in front (on top) of the header_bg.jpg file. If the same blue background is used, the logo can be changed by simply uploading a modified header_logo.jpg image file to the styles/kubrick/ directory. If the dimensions of the logo image are the same as those of the default logo image, no changes need to be made to the CSS file. If the logo is smaller than the background area, then its position can be indicated as a CSS property. If images with names other than these are used, the CSS file must be edited to reflect the changes. See the web sites listed above for more information on how the graphics are placed, etc.

If a totally new page-top graphic is used, along with the existing side and bottom graphics, then start with a new header image similar to kubrick_header-original.jpg and make 17px-wide slices at the left and right ends, saving them as header_left.jpg and header_right.jpg. 

At least the foreground image should be replaced in order to personalize the theme.

-=Page Header Content=-
In the header area, the logo image is in (actually is the background image for) div#header_logo. Below that on the page is div#tiki-top_bar, which holds tiki-top_bar.tpl and displays the "This is Tiki version..." statement and the calendar link. Modify this text (in a new /templates/styles/kubrick/tiki-top_bar.tpl) as desired. Below this line on the page is div#horiz_menu, which holds PhpLayersMenu id=43.


-=About the Left and Right Columns=-
Tiki normally uses both left and right columns, for modules. The Kubrick theme mainly uses a single side column, on the right. The left modules are displayed on the page under the right modules.

-=Installation=-
Copy or upload kubrick.css and the kubrick images directory to the styles/ directory of your Tiki site.
Copy or upload the kubrick templates directory to the templates/styles/ directory of your Tiki site.

-=License/Copying=-

Consistent with the terms under which this theme was adapted for Tiki CMS, it is offered under the GPL license. This is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation.

This software is distributed in the hope that it will be useful, but without any warranty, without even the implied warranty of merchantability or fitness for a particular purpose. See the GNU General Public License (http://www.gnu.org/licenses/gpl.txt) for more details.