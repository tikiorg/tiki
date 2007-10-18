{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_comments.tpl,v 1.7.2.1 2007-10-18 08:51:34 ohertel Exp $ *}
<h1><a href="tiki-list_comments.php" class="pagetitle">{tr}Comments{/tr}</a></h1>

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
<input type="checkbox"  name="types[]" value="{$key|escape}" {if $selected eq 'y'}checked="checked"{/if}>{tr}{$key|escape}{/tr}&nbsp;&nbsp;
{*<option value="{$key|escape}" {if $selected eq 'y'}selected="selected"{/if}>{$key|escape}</option>*}
{/foreach}
{*</select>*}
</td></tr></table>
   </form>

<form name="checkboxes_on" method="post" action="tiki-list_comments.php">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
{section name=ix loop=$types}
<input type="hidden" name="types[]" value="{$types[ix]|escape}" />
{/section}
<div class="formcolor">
<script language='Javascript' type='text/javascript'>
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

<table class="normal">
<tr>
<th class="heading">&nbsp;</th>
<th class="heading">{if $types eq "wiki page"}<a href="tiki-list_comments.php?sort_mode={if $sort_mode eq 'object_desc'}object_asc{else}object_desc{/if}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Page{/tr}</a>{else}<a href="tiki-list_comments.php?sort_mode={if $sort_mode eq 'objectType_desc'}objectType_asc{else}objectType_desc{/if}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Type{/tr}</a>{/if}</th>
<th class="heading"><a href="tiki-list_comments.php?sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Title{/tr}</a></th>
<th class="heading"><a href="tiki-list_comments.php?sort_mode={if $sort_mode eq 'userName_desc'}userName_asc{else}userName_desc{/if}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Author{/tr}</a></th>
<th class="heading"><a href="tiki-list_comments.php?sort_mode={if $sort_mode eq 'user_ip_desc'}user_ip_asc{else}user_ip_desc{/if}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}IP{/tr}</a></th>
<th class="heading"><a href="tiki-list_comments.php?sort_mode={if $sort_mode eq 'commentDate_desc'}commentDate_asc{else}commentDate_desc{/if}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Date{/tr}</a></th>
<th class="heading"><a href="tiki-list_comments.php?sort_mode={if $sort_mode eq 'data_desc'}data_asc{else}data_desc{/if}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Data{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$comments}
<tr>
<td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$comments[ix].threadId|escape}"/></td>
<td class="{cycle advance=false}">{if $types eq "wiki page"}{$comments[ix].object|truncate:50:"...":true}{elseif $comments[ix].objectType eq 'post'}{tr}Blog{/tr}{else}{$comments[ix].objectType}{/if}</td>
<td class="{cycle advance=false}"><a href="{$comments[ix].href}">{$comments[ix].title|truncate:50:"...":true}</a></td>
<td class="{cycle advance=false}">{$comments[ix].userName}</td>
<td class="{cycle advance=false}">{$comments[ix].user_ip}</td>
<td class="{cycle advance=false}">{$comments[ix].commentDate|tiki_short_datetime}</td>
<td class="{cycle}">{$comments[ix].data|truncate:50:"...":true}</td>
</tr>
{/section}
</table>

<div class="formcolor">
<script language='Javascript' type='text/javascript'>
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

{if cant_pages ne 0}
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_comments.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="prevnext" href="tiki-list_comments.php?offset={$next_offset}&amp;sort_mode={$sort_mode}{if $find}&amp;find={$find}{/if}{$string_types}">{tr}Next{/tr}</a>]
{/if}
</div>
{/if}