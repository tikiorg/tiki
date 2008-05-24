{* $Id$ *}
{popup_init src="lib/overlib.js"}
<h1><a href="tiki-list_comments.php" class="pagetitle">{tr}Comments{/tr}</a></h1>
{if $comments or ($find ne '')}
<form method="get" action="tiki-list_comments.php">
<table class="findtable">
<tr><td class="findtitle">{tr}Find{/tr}</td>
   <td class="findtitle">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" name="search" value="{tr}Find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </td>
</tr>
<tr><td class="findtable" colspan="2">
{*<select name="types[]" multiple="multiple" size="5">*}
{foreach key=key item=selected from=$list_types}
<input type="checkbox" name="types[]" value="{$key|escape}" {if $selected eq 'y'}checked="checked"{/if} />{tr}{$key|escape}{/tr}&nbsp;&nbsp;
{*<option value="{$key|escape}" {if $selected eq 'y'}selected="selected"{/if}>{$key|escape}</option>*}
{/foreach}
{*</select>*}
</td></tr></table>
   </form>
{/if}
<br />
{if $comments}
<form name="checkboxes_on" method="post" action="tiki-list_comments.php">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
{section name=ix loop=$types}
<input type="hidden" name="types[]" value="{$types[ix]|escape}" />
{/section}
<div class="formcolor">
<script type="text/javascript">
<!--
 // check / uncheck all.
 // in the future, we could extend this to happen serverside as well for the convenience of people w/o javascript.
 // for now those people just have to check every single box
  document.write("<input name=\"switcher\" id=\"clickall\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'checked[]',this.checked)\"/>");
  document.write("<label for=\"clickall\">{tr}Select All{/tr}</label>");
  //-->
</script>
&nbsp;&nbsp;&nbsp;&nbsp;{tr}Perform action with checked:{/tr} <input type="submit" name="remove" value="{tr}Delete{/tr}" />
</div>
{/if}

<table class="normal">
<tr>
<th class="heading">&nbsp;</th>
{if is_array($types) and count($types) > 1}<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="objectType"}{tr}Type{/tr}{/self_link}</th>{/if}
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="object"}{tr}Object{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="title"}{tr}Title{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="userName"}{tr}Author{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="user_ip"}{tr}IP{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="commentDate"}{tr}Date{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="data"}{tr}Data{/tr}{/self_link}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$comments}
<tr>
<td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$comments[ix].threadId|escape}"/></td>
{if is_array($types) and count($types) > 1}<td class="{cycle advance=false}">{if $comments[ix].objectType eq 'post'}{tr}Blog{/tr}{else}{tr}{$comments[ix].objectType}{/tr}{/if}</td>{/if}
<td class="{cycle advance=false}">{$comments[ix].object|truncate:50:"...":true}</td>
<td class="{cycle advance=false}"><a href="{$comments[ix].href}" title="{$comments[ix].title}">{$comments[ix].title|truncate:50:"...":true}</a>
{if $comments[ix].parentId and empty($comments[ix].parentTitle)}<br />{tr}Orphan{/tr}{/if}</td>
<td class="{cycle advance=false}">{$comments[ix].userName}</td>
<td class="{cycle advance=false}">{$comments[ix].user_ip}</td>
<td class="{cycle advance=false}">{$comments[ix].commentDate|tiki_short_datetime}</td>
<td class="{cycle}" {popup caption=$comments[ix].title|escape|replace:'"':'&quot;' text=$comments[ix].parsed|escape|replace:'"':'&quot;'}>{$comments[ix].data|truncate:50:"...":true}</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="7">{tr}No records found.{/tr}</td></tr>
{/section}
</table>
{if $comments}
<div class="formcolor">
<script type="text/javascript">
<!--
 // check / uncheck all.
 // in the future, we could extend this to happen serverside as well for the convenience of people w/o javascript.
 // for now those people just have to check every single box
  document.write("<input name=\"switcher\" id=\"clickall\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'checked[]',this.checked)\"/>");
  document.write("<label for=\"clickall\">{tr}Select All{/tr}</label>");
  //-->
</script>
&nbsp;&nbsp;&nbsp;&nbsp;{tr}Perform action with checked:{/tr} <input type="submit" name="remove" value="{tr}Delete{/tr}" />
</div>
</form>
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
