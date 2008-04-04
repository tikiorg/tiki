{* $Id$ *}

{* this script (un/)checks all checkboxes with id 'banning-section' *}
<script type="text/javascript">
{literal}<!--
	function CheckAll() {
		for (var i = 0; i < document.banningform.elements.length; i++) {
			var e = document.banningform.elements[i];
			if (e.type == 'checkbox' && e.id == 'banning-section' && e.name != 'checkall') {
				e.checked = document.banningform.checkall.checked;
			}
		}
	}
//-->{/literal}
</script>

<h1><a class="pagetitle" href="tiki-admin_banning.php">{tr}Banning system{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Banning+System" target="tikihelp" class="tikihelp" title="{tr}Help on Banning System{/tr}">
{icon _id='help'}</a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_banning.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin banning tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>{/if}</h1>

<h2>{tr}Add or edit a rule{/tr}</h2>
<form action="tiki-admin_banning.php" name="banningform" method="post">
<input type="hidden" name="banId" value="{$banId|escape}" />
<table class="normal">
<tr>
	<td class="formcolor"><label for="banning-title">{tr}Rule title{/tr}</label></td>
	<td class="formcolor">
		<input type="text" name="title" id="banning-title" value="{$info.title|escape}" maxlength="200" />
	</td>
</tr>
<tr>
	<td class="formcolor"><label for="banning-userregex">{tr}Username regex matching{/tr}:</label></td>
	<td class="formcolor">
		<input type="radio" name="mode" value="user" {if $info.mode eq 'user'}checked="checked"{/if} />
		<input type="text" name="userreg" id="banning-userregex" value="{$info.user|escape}" />
	</td>
</tr>
<tr>
	<td class="formcolor"><label for="banning-ipregex">{tr}IP regex matching{/tr}:</label></td>
	<td class="formcolor">
		<input type="radio" name="mode" value="ip" {if $info.mode eq 'ip'}checked="checked"{/if} />
		<input type="text" name="ip1" id="banning-ipregex" value="{$info.ip1|escape}" size="3" />.
		<input type="text" name="ip2" value="{$info.ip2|escape}" size="3" />.
		<input type="text" name="ip3" value="{$info.ip3|escape}" size="3" />.
		<input type="text" name="ip4" value="{$info.ip4|escape}" size="3" />
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Banned from sections{/tr}:</td>
	<td class="formcolor">
		<div class="toggle">
			<input type="checkbox" name="checkall" onclick="CheckAll();" />
			<label for="sectionswitch">{tr}Check / Uncheck All{/tr}</label>
		</div>

		<table><tr>
		{foreach key=sec name=ix item=it from=$sections}
        <td class="formcolor">
			<input type="checkbox" name="section[{$sec}]" id="banning-section" {if in_array($sec,$info.sections)}checked="checked"{/if} /> <label for="banning-section">{tr}{$sec}{/tr}</label>
        </td>
        {if $smarty.foreach.ix.index mod 2}
				</tr><tr>
        {/if}
    	{/foreach}
		</table>
	</td>
</tr>
<tr>
	<td class="formcolor"><label for="banning-actdates">{tr}Rule activated by dates{/tr}</label></td>
	<td class="formcolor">
		<input type="checkbox" name="use_dates" id="banning-actdates" {if $info.use_dates eq 'y'}checked="checked"{/if} />
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Rule active from{/tr}</td>
	<td class="formcolor">
		{html_select_date prefix="date_from" time="$info.date_from" field_order=$prefs.display_field_order}
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Rule active until{/tr}</td>
	<td class="formcolor">
		{html_select_date prefix="date_to" time="$info.date_to" field_order=$prefs.display_field_order}
	</td>
</tr>
<tr>
	<td class="formcolor"><label for="banning-mess">{tr}Custom message to the user{/tr}</label></td>
	<td class="formcolor">
		<textarea rows="4" cols="40" name="message">{$info.message|escape}</textarea>
	</td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor">
		<input type="submit" name="save" value="{tr}Save{/tr}" />
	</td>
</tr>
</table>
</form>
{if $items}
<form method="post" action="tiki-admin_banning.php">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<label for="banning-find">{tr}Find{/tr}:</label><input type="text" name="find" id="banning-find" value="{$find|escape}" />
</form>
{/if}
<h2>{tr}Rules{/tr}:</h2>
<form method="post" action="tiki-admin_banning.php">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td  class="heading"><input type="submit" name="del" value="{tr}x{/tr} " /></td>
<td class="heading">{tr}Title{/tr}</td>
<td class="heading">{tr}User/IP{/tr}</td>
<td class="heading">{tr}Sections{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="delsec[{$items[user].banId}]" />
</td>
<td class="{cycle advance=false}">
<a href="tiki-admin_banning.php?banId={$items[user].banId}" class="link">
{$items[user].title}</a>
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{if $items[user].mode eq 'user'}
	{$items[user].user}
{else}
	{$items[user].ip1}.{$items[user].ip2}.{$items[user].ip3}.{$items[user].ip4}
{/if}
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{section name=ix loop=$items[user].sections}
	{$items[user].sections[ix].section}{if not $smarty.section.ix.last},{/if}
{/section}
</td>
<td class="{cycle}">
&nbsp;&nbsp;<a title="{tr}Delete{/tr}" href="tiki-admin_banning.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$items[user].banId}" class="link" 
>{icon _id='cross' alt='{tr}Delete{/tr}'}</a>&nbsp;&nbsp;
</td>
</tr>
{sectionelse}
<tr><td colspan="5" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
</form>

<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_banning.php?offset={$prev_offset}&amp;find={$find}">{tr}Prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="prevnext" href="tiki-admin_banning.php?offset={$next_offset}&amp;find={$find}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_banning.php?offset={$selector_offset}&amp;find={$find}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}
</div>
</div>
