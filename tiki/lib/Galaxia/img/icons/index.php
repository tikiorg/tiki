<?php
#
# list all gif images in the icon directory
#

function page_head() {
  echo("<html>
<head>
 <title>Bookmark4U Icon List</title>
</head>
<body bgcolor=gray>
<script>
function setBgColor(color) {
  document.bgColor = color;
}
</script>
<form>
<input type=button onclick=\"setBgColor('white')\" value='White'>
<input type=button onclick=\"setBgColor('gray')\" value='Gray'>
<input type=button onclick=\"setBgColor('black')\" value='Black'>
</form>\n");
}

function page_tail() {
  echo("</body></html>");
}

#$gif_images = exec("find . -name '*.gif' | xargs ");
$gif_images = exec("find . -name '*.gif' | tr '\n' ' ' "); 

$gifs = explode(" ", $gif_images);
sort($gifs);

page_head();

for ($i = 0; $i < sizeof($gifs); $i++) {
  echo("<img src='$gifs[$i]' border=1 alt=''>
  <font color=black>$gifs[$i]</font><br>\n");
}

page_tail();
exit;

?>
