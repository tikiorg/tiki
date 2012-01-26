
<?php
// $id$
//This modifier allows the unique "[breakline]" into a string to split into separate lines replaced by <br /> after the use of escape modifier which unallows any html tag.
// usefull for <label> when they need to be long and/or when automatic breaklines particularly after translations creates a split line in an unapproriate place.
// Trebly:B01229:[ADD] $
function smarty_modifier_breakline($str_content) {
return str_replace('[breakline]','<br />',$str_content);
}
?>
