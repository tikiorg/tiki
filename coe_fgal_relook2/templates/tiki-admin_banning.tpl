{* $Id$ *}

{* this script (un/)checks all checkboxes with id 'banning-section' *}
{jq notonready=true}
	function CheckAll() {
		for (var i = 0; i < document.banningform.elements.length; i++) {
			var e = document.banningform.elements[i];
			if (e.type == 'checkbox' && e.id == 'banning-section' && e.name != 'checkall') {
				e.checked = document.banningform.checkall.checked;
			}
		}
	}
{/jq}

{title help="Banning+System"}{tr}Banning system{/tr}{/title}

<h2>{tr}Add or edit a rule{/tr}</h2>
<form action="tiki-admin_banning.php" name="banningform" method="post">
	<input type="hidden" name="banId" value="{$banId|escape}" />
	<table class="formcolor">
		<tr>
			<td><label for="banning-title">{tr}Rule title{/tr}</label></td>
			<td>
				<input type="text" name="title" id="banning-title" value="{$info.title|escape}" maxlength="200" />
			</td>
		</tr>
		<tr>
			<td><label for="banning-userregex">{tr}Username regex matching:{/tr}</label></td>
			<td>
				<input type="radio" name="mode" value="user" {if $info.mode eq 'user'}checked="checked"{/if} />
				<input type="text" name="userreg" id="banning-userregex" value="{$info.user|escape}" />
			</td>
		</tr>
		<tr>
			<td><label for="banning-ipregex">{tr}IP regex matching:{/tr}</label></td>
			<td>
				<input type="radio" name="mode" value="ip" {if $info.mode eq 'ip'}checked="checked"{/if} />
				<input type="text" name="ip1" id="banning-ipregex" value="{$info.ip1|escape}" size="3" />.
				<input type="text" name="ip2" value="{$info.ip2|escape}" size="3" />.
				<input type="text" name="ip3" value="{$info.ip3|escape}" size="3" />.
				<input type="text" name="ip4" value="{$info.ip4|escape}" size="3" />
			</td>
		</tr>
		<tr>
			<td>{tr}Banned from sections:{/tr}</td>
			<td>
				<div class="toggle">
					<input type="checkbox" name="checkall" onclick="CheckAll();" />
					<label for="sectionswitch">{tr}Check / Uncheck All{/tr}</label>
				</div>
				<table>
					<tr>
						{foreach key=sec name=ix item=it from=$sections}
							<td>
								<input type="checkbox" name="section[{$sec}]" id="banning-section" {if in_array($sec,$info.sections)}checked="checked"{/if} /> <label for="banning-section">{tr}{$sec}{/tr}</label>
							</td>
							{if $smarty.foreach.ix.index mod 2}
								</tr>
								<tr>
							{/if}
						{/foreach}
				</table>
			</td>
		</tr>
		<tr>
			<td><label for="banning-actdates">{tr}Rule activated by dates{/tr}</label></td>
			<td>
				<input type="checkbox" name="use_dates" id="banning-actdates" {if $info.use_dates eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td>{tr}Rule active from{/tr}</td>
			<td>
				{html_select_date prefix="date_from" time="$info.date_from" field_order=$prefs.display_field_order}
			</td>
		</tr>
		<tr>
			<td>{tr}Rule active until{/tr}</td>
			<td>
				{html_select_date prefix="date_to" time="$info.date_to" field_order=$prefs.display_field_order}
			</td>
		</tr>
		<tr>
			<td><label for="banning-mess">{tr}Custom message to the user{/tr}</label></td>
			<td>
				<textarea rows="4" cols="40" name="message">{$info.message|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>
{if $items}
	<form method="post" action="tiki-admin_banning.php">
		<input type="hidden" name="offset" value="{$offset|escape}" />
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
		<label for="banning-find">{tr}Find:{/tr}</label><input type="text" name="find" id="banning-find" value="{$find|escape}" />
	</form>
{/if}
<h2>{tr}Rules:{/tr}</h2>
<form method="post" action="tiki-admin_banning.php">
	<input type="hidden" name="offset" value="{$offset|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<table class="normal">
		<tr>
			<th><input type="submit" name="del" value="{tr}x{/tr} " /></th>
			<th>{tr}Title{/tr}</th>
			<th>{tr}User/IP{/tr}</th>
			<th>{tr}Sections{/tr}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$items}
			<tr class="{cycle}">
				<td class="checkbox">
					<input type="checkbox" name="delsec[{$items[user].banId}]" />
				</td>
				<td class="text">
					<a href="tiki-admin_banning.php?banId={$items[user].banId}" class="link">{$items[user].title|escape}</a>
				</td>
				<td class="text">
					{if $items[user].mode eq 'user'}
						{$items[user].user|escape}
					{else}
						{$items[user].ip1}.{$items[user].ip2}.{$items[user].ip3}.{$items[user].ip4}
					{/if}
				</td>
				<td class="text">
					{section name=ix loop=$items[user].sections}
						{$items[user].sections[ix].section}{if not $smarty.section.ix.last},{/if}
					{/section}
				</td>
				<td class="action">
					&nbsp;&nbsp;<a title="{tr}Delete{/tr}" href="tiki-admin_banning.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$items[user].banId}" class="link">
						{icon _id='cross' alt="{tr}Delete{/tr}"}
					</a>&nbsp;&nbsp;
				</td>
			</tr>
		{sectionelse}
				{norecords _colspan=5}
		{/section}
	</table>
</form>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
