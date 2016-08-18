To make your own theme (e.g. a theme called "abc"), the layout of the files are:

css:
themes/abc/css/tiki.css   (As soon as this is created, you can pick theme from http://example.com/tiki-admin.php?page=look)

less:
themes/abc/less/tiki.less   (Here are the less files to be compiled by the developers IDE or command line)

fonts:
themes/abc/fonts/*   (If you want to use custom fonts not being loaded via an API, put them here)

icons:
themes/abc/icons/*   (This is where you deploy your icon-set, if you do prefer not to use glyphs)

templates:
themes/abc/templates/*.tpl   (This is where you deploy .tpl files which override the ones at templates/*.tpl)

images:
themes/abc/images/*    (This is where you store all images related to your theme)

More details at:
https://themes.tiki.org/How+To+Add+a+New+Bootstrap+Theme
