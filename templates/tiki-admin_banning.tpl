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

{* this script (un/)checks all checkboxes with id 'multi-banning-section' *}
{jq notonready=true}
	function CheckMultiIP() {
		for (var i = 0; i < document.banningform.elements.length; i++) {
			var e = document.banningform.elements[i];
			if (e.type == 'checkbox' && e.id == 'multi-banning-section' && e.name != 'checkall') {
				e.checked = document.banningform.checkmultiip.checked;
			}
		}
	}
{/jq}

{* this script hides date fields when they are irrelevant *}
{jq notonready=true}
	function CheckUseDates() {
		var e = document.getElementById('usedates_date');
		var e_from = document.getElementById('usedates_date_from');
		var e_to = document.getElementById('usedates_date_to');
		var check = document.getElementById('banning-actdates');
		if ( check.checked == true ) {
			e.style.display = 'block' ;
		}else{
			e.style.display = 'none' ;
		}
	}
{/jq}

{title help="Banning+System"}{tr}Banning system{/tr}{/title}

<div class="navbar">
	<form action="tiki-admin_banning.php" method="post">
	<input type="hidden" name="export" value="y"/>
		<input type="submit" name="csv" value="{tr}Export as CSV{/tr}" class="button"/>
		{button _text="{tr}Import as CSV{/tr}" href="#Import_rules_as_CSV" class="button"}
	</form>
</div>

{if $updated}
	{remarksbox type="note" title="{tr}Note:{/tr}"}
		<strong>{tr}Banning rules have been updated{/tr}</strong>
	{/remarksbox}
{/if}

<h2>{tr}Add or edit rules{/tr}</h2>
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
		{if isset($mass_ban_ip)}
		<tr>
			<td><label for="banning-ipregex">{tr}Multiple IP regex matching:{/tr}</label></td>
			<td>
				<input type="radio" name="mode" value="mass_ban_ip" {if $info.mode eq 'mass_ban_ip'}checked="checked"{/if} />
				<div class="toggle">
					<input type="checkbox" name="checkmultiip" checked="checked" onclick="CheckMultiIP();" />
					<label for="sectionswitch">{tr}Check / Uncheck All{/tr}</label>
				</div>
				<table>
					{foreach key=ip item=comment from=$ban_comments_list}
						<tr>
							<td>
								<input type="checkbox" name="multi_banned_ip[{$ip|escape}]" id="multi-banning-section" checked="checked" /> <label for="multi-banning-section">{$ip|escape}</label>
							</td>
							<td>
								{foreach key=id item=user from=$comment}
									<div>{$user.userName|escape}</div>
								{/foreach}
							</td>
						</tr>
					{/foreach}
				</table>
			</td>
		</tr>
		{else}
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
		{/if}
		<tr>
			<td>{tr}Banned from sections:{/tr}</td>
			<td>
				<div class="toggle">
					<input type="checkbox" name="checkall" {if (!$banId)}checked="checked"{/if} onclick="CheckAll();" />
					<label for="sectionswitch">{tr}Check / Uncheck All{/tr}</label>
				</div>
				<table>
					<tr>
						{foreach key=sec name=ix item=it from=$sections}
							<td>
								<input type="checkbox" name="section[{$sec}]" id="banning-section" {if ((!$banId) || in_array($sec,$info.sections))}checked="checked"{/if} /> <label for="banning-section">{tr}{$sec}{/tr}</label>
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
				<input type="checkbox" name="use_dates" id="banning-actdates" {if $info.use_dates eq 'y'}checked="checked"{/if} onclick="CheckUseDates();" />
				<div id="usedates_date" style="display: {if $info.use_dates eq 'y'}block{else}none{/if};" >
					<table class="formcolor">
						<tr>
							<td>{tr}Rule active from{/tr}</td>
							<td>
								{html_select_date prefix="date_from" time=$info.date_from field_order=$prefs.display_field_order}
							</td>
						</tr>
						<tr >
							<td>{tr}Rule active until{/tr}</td>
							<td>
								{html_select_date prefix="date_to" time=$info.date_to end_year="+10" field_order=$prefs.display_field_order}
							</td>
						</tr>
					</table>
				</div>
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

<h2 id="Import_rules_as_CSV">{tr}Import rules as CSV{/tr}</h2>
<form method="post" action="tiki-admin_banning.php" enctype="multipart/form-data">
  <table class="formcolor">
    <tr>
      <td>
        {tr}CSV File{/tr}
		{capture name=help}{tr}Column names on the first line:{/tr}<br>banId,mode,title,ip1,ip2,ip3,ip4,user,date_from,date_to,use_dates,created,created_readable,message,sections<br>{tr}Sections format:{/tr} {tr}section names are splitted by pipes (vertical bars). To see an example and use it as template, add one rule by hand, and export it as csv{/tr}<br>{tr}Date format:{/tr} {tr}See:{/tr} http://php.net/strtotime{/capture}
        <a {popup text=$smarty.capture.help|escape}>{icon _id='help'}</a>
      </td>
      <td>
        <input type="file" name="fileCSV" size="50" />
        <label>
        	<input type="checkbox" name="import_as_new" />
        	{tr}Import as new rules{/tr}
        </label>
        <input type="submit" name="import" value="{tr}import{/tr}" />
      </td>
    </tr>
  </table>
</form>

{if $items}
<h2>{tr}Find{/tr}</h2>
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
					&nbsp;&nbsp;<a title="{tr}Edit{/tr}" href="tiki-admin_banning.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;banId={$items[user].banId}" class="link">
						{icon _id='page_edit' alt="{tr}Edit{/tr}"}
					</a>
					<a title="{tr}Delete{/tr}" href="tiki-admin_banning.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$items[user].banId}" class="link">
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
