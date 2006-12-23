{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_help_tool.tpl,v 1.23 2006-12-23 17:28:05 mose Exp $ *}
<div class="quicktag">
{literal}
<script type="text/javascript">
<!--
function taginsert(area_name,tagid)
{
//fill variables
{/literal}
  var tag = new Array();
  {section name=qtg loop=$quicktags}
  tag[{$quicktags[qtg].tagId}]='{$quicktags[qtg].taginsert|escape:"javascript"}';
  {/section}
//done
{literal}  
  insertAt(area_name,tag[tagid]);
}
-->
</script>
{/literal}
<a href="javascript:flip('helptool{$qtnum}');" class="link"><img src="img/icons/plus.gif" border='0' alt='+' />&nbsp;{tr}Quicktags{/tr} ...</a><br /><br />
{*get_strings {tr}bold{/tr}{tr}italic{/tr}{tr}underline{/tr}{tr}table{/tr}{tr}table new{/tr}{tr}external link{/tr}{tr}wiki link'{/tr}{tr}heading1{/tr}{tr}title bar{/tr}{tr}box{/tr}
{tr}rss feed{/tr}{tr}dynamic content{/tr}{tr}tagline{/tr}{tr}hr{/tr}{tr}center text{/tr}{tr}colored text{/tr}{tr}dynamic variable{/tr}{tr}image{/tr}{tr}New wms Metadata{/tr}{tr}New Class{/tr}{tr}New Projection{/tr}{tr}New Query{/tr}{tr}New Scalebar{/tr}{tr}New Layer{/tr}{tr}New Label{/tr}{tr}New Reference{/tr}{tr}New Legend{/tr}{tr}New Web{/tr}{tr}New Outputformat{/tr}{tr}New Mapfile{/tr} *}
<div id='helptool{$qtnum}' 
{assign var=show value="show_helptool"|cat:$qtnum}
{if isset($smarty.session.tiki_cookie_jar.$show) and $smarty.session.tiki_cookie_jar.$show eq 'y'}
style="display:block;"
{else}
style="display:none;"
{/if}>
<div>
{cycle name='cycle'|cat:$qtnum values=$qtcycle|default:",,,</div><div>" advance=false print=false}
{section name=qtg loop=$quicktags}
<a title="{tr}{$quicktags[qtg].taglabel}{/tr}" href="javascript:taginsert('{$area_name}','{$quicktags[qtg].tagId}');"><img
src='{$quicktags[qtg].tagicon}' alt='{tr}{$quicktags[qtg].taglabel}{/tr}' title='{tr}{$quicktags[qtg].taglabel}{/tr}' border='0' /></a>
{cycle name='cycle'|cat:$qtnum}
{/section}
<a title="{tr}special chars{/tr}" class="link" href="#" onclick="javascript:window.open('tiki-special_chars.php?area_name={$area_name}','','menubar=no,width=252,height=25');"><img
src='images/ed_charmap.gif' alt='{tr}special characters{/tr}' title='{tr}special characters{/tr}' border='0' /></a>
</div>
{if $tiki_p_admin eq 'y'}
<br /><a href="tiki-admin_quicktags.php" class="link">{tr}admin quicktags{/tr}</a>
{/if}
</div>
</div>
