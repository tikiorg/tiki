<a title="{tr}bold{/tr}" class="link" href="javascript:insertAt('{$area_name}','__text__');"><img src='images/ed_format_bold.gif' alt='{tr}bold{/tr}' title='{tr}bold{/tr}' border='0' /></a>
<a title="{tr}italics{/tr}" class="link" href="javascript:insertAt('{$area_name}','\'\'text\'\'');"><img src='images/ed_format_italic.gif' alt='{tr}italic{/tr}' title='{tr}italic{/tr}' border='0' /></a>
<a title="{tr}underline{/tr}" class="link" href="javascript:insertAt('{$area_name}','===text===');"><img src='images/ed_format_underline.gif' alt='{tr}underline{/tr}' title='{tr}underline{/tr}' border='0' /></a>
{if $feature_wiki_tables eq 'new'}
<a title="{tr}table{/tr}" class="link" href="javascript:insertAt('{$area_name}','||r1c1|r1c2||r2c1|r2c2||');"><img src='images/insert_table.gif' alt='{tr}table{/tr}' title='{tr}table{/tr}' border='0' /></a>
{else}
<a title="{tr}table{/tr}" class="link" href="javascript:insertAt('{$area_name}','||r1c1|r1c2\nr2c1|r2c2||');"><img src='images/insert_table.gif' alt='{tr}table{/tr}' title='{tr}table{/tr}' border='0' /></a>
{/if}
<a title="{tr}external link{/tr}" class="link" href="javascript:insertAt('{$area_name}','[http://example.com|desc]');"><img src='images/ed_link.gif' alt='{tr}external link{/tr}' title='{tr}external link{/tr}' border='0' /></a>
<a title="{tr}wiki link{/tr}" class="link" href="javascript:insertAt('{$area_name}','((page))');"><img src='images/ed_copy.gif' alt='{tr}wiki link{/tr}' title='{tr}wiki link{/tr}' border='0' /></a>
<a title="{tr}heading1{/tr}" class="link" href="javascript:insertAt('{$area_name}','!text');"><img src='images/ed_custom.gif' alt='{tr}heading{/tr}' title='{tr}heading{/tr}' border='0' /></a>
<a title="{tr}title bar{/tr}" class="link" href="javascript:insertAt('{$area_name}','-=text=-');"><img src='images/fullscreen_maximize.gif' alt='{tr}title bar{/tr}' title='{tr}title bar{/tr}' border='0' /></a>
<a title="{tr}box{/tr}" class="link" href="javascript:insertAt('{$area_name}','^text^');"><img src='images/ed_about.gif' alt='{tr}box{/tr}' title='{tr}box{/tr}' border='0' /></a>
<a title="{tr}rss feed{/tr}" class="link" href="javascript:insertAt('{$area_name}','{literal}{{/literal}rss id= }');"><img src='images/ico_link.gif' alt='{tr}rss feed{/tr}' title='{tr}rss feed{/tr}' border='0' /></a>
<a title="{tr}dynamic content{/tr}" class="link" href="javascript:insertAt('{$area_name}','{literal}{{/literal}content id= }');"><img src='images/book.gif' alt='{tr}dynamic content{/tr}' title='{tr}dynamic content{/tr}' border='0' /></a>
<a title="{tr}tagline{/tr}" class="link" href="javascript:insertAt('{$area_name}','{literal}{{/literal}cookie}');"><img src='images/footprint.gif' alt='{tr}tagline{/tr}' title='{tr}tagline{/tr}' border='0' /></a>
<a title="{tr}hr{/tr}" class="link" href="javascript:insertAt('{$area_name}','---');"><img src='images/ed_hr.gif' alt='{tr}horizontal ruler{/tr}' title='{tr}horizontal ruler{/tr}' border='0' /></a>
<a title="{tr}center text{/tr}" class="link" href="javascript:insertAt('{$area_name}','::some::');"><img src='images/ed_align_center.gif' alt='{tr}center{/tr}' title='{tr}center{/tr}' border='0' /></a>
<a title="{tr}colored text{/tr}" class="link" href="javascript:insertAt('{$area_name}','~~color:text~~');"><img src='images/fontfamily.gif' alt='{tr}colored text{/tr}' title='{tr}colored text{/tr}' border='0' /></a>
<!--<a class="link" href="javascript:insertAt('{$area_name}','{literal}{{/literal}img src=?nocache=1 width= height= align= desc= link= }');">{tr}img nc{/tr}</a>|-->
<a title="{tr}image{/tr}" class="link" href="javascript:insertAt('{$area_name}','{literal}{{/literal}img src= width= height= align= desc= link= }');"><img src='images/ed_image.gif' alt='{tr}image{/tr}' title='{tr}image{/tr}' border='0' /></a>
<a title="{tr}special chars{/tr}" class="link" href="#" onClick="javascript:window.open('templates/tiki-special_chars.php?area_name={$area_name}','','menubar=no,width=252,height=25');"><img src='images/ed_charmap.gif' alt='{tr}special characters{/tr}' title='{tr}special characters{/tr}' border='0' /></a>
