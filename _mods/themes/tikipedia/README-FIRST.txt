This is the Tikipedia theme for Tiki CMS (Tikiwiki). It's been tested with Tiki 1.9.2 and 1.9.3.1. Authored, based on the Mediawiki Monobook theme, and maintained by Gary Cunningham-Lee (chibaguy). For the latest information on and updates of this theme for TikiCMS, visit http://themes.tikiwiki.org or http://zukakakina.com. 

This readme describes things to be aware of and do BEFORE installing/selecting this theme at your site, how to install the theme, and license information.

-=Before Installing/Selecting this theme=-

* Site Identity feature, Column display, Module windowshading

Tiki's Site Identity feature hasn't been tested with this theme, and probably should not be turned on. To customize/personalize the theme, see below or visit http://themes.tikiwiki.org/tiki-index.php?page=Customizing_Tikipedia.

Column display: allowing users to turn column visibility on and off seems to be buggy and is not recommended. 

Module "window-shading" has been tested with this theme and works normally.

* Menus

This theme doesn't currently specify CSS style properties for a PHP Layers menu, although a PHP Layers menu should work with no problems.  

-=Customizing: replacing page background and logo images=-

The background and logo images can be changed by using new image files with the same names as the default image files. The CSS file specifies a headbg.jpg (the background image) and a header_logo.png (the logo image). (Of course new image file names can be used if tikipedia.css is changed to match.)

The page background image (styles/tikipedia/headbg.jpg) is a non-repeating 1941x220 image positioned at the top of the page, and blending into the #f9f9f9 background color. 

The logo is actually the background image in div.logo-box #tikipedia-logo a (line 335 or so in tikipedia.css). Following the Mediawiki/Monobook pattern, separate image files are used, one for Microsoft Internet Explorer (which doesn't support transparency in PNG files) and one for other browsers that do; the GIF file is for IE.) If images with the same names and dimensions are used as the default (logo.png and logo-indexed.gif), then no change is needed in the CSS file. Obviously if new image names are used, the file will have to be edited to match. If only one image is to be served to all browsers, the unneeded lines should be deleted or commented out in the CSS file. #tikipedia-logo a is the selector for Internet Explorer (line 335). html > body div.logo-box #tikipedia-logo a is the selector for other browsers (line 346).

Simply upload new images with the same names to the styles/tikipedia/ directory. The dimensions of the logo image(s) should be the same or less than that of the default logo image. If the logo is smaller than the background, then its position can be indicated as a CSS property. If images with names other than these are used, the CSS file must be edited to reflect that change.

-=About the left and right columns=-

The Tikipedia theme stacks Tiki's right-column modules below the left-column modules on the page.

-=Installation=-

Copy or upload (by ftp) tikipedia.css and the tikipedia/ images directory to the styles/ directory of your Tiki site.
Copy or upload the tikipedia templates directory to the templates/styles/ directory of your Tiki site. See http://themes.tikiwiki.org/tiki-index.php?page=How%20To%20Add%20a%20New%20Theme for more information.
To be able to use the three included wiki plugins, copy or upload them to the lib/wiki-plugins/ directory of your Tiki site. They don't need to be activated or anything; they'll be available for use immediately.

-=License/copying=-

This theme and is offered under the GPL license. This is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation.

This software is distributed in the hope that it will be useful, but without any warranty, without even the implied warranty of merchantability or fitness for a particular purpose. See the GNU General Public License (http://www.gnu.org/copyleft/gpl.html) for more details.