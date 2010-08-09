{* $Id$ *}
{assign var=nlId_urlencoded value=$nlId|urlencode}
{title url="tiki-admin_newsletter_subscriptions.php?nlId=$nlId_urlencoded"}{tr}Admin newsletter subscriptions{/tr}{/title}

<div class="navbar">
	{button href="tiki-newsletters.php" _text="{tr}List Newsletters{/tr}"}
	{assign var="nlid_encod" value=$nlId|urlencode}
	{button href="tiki-admin_newsletters.php?nlId=$nlid_encod" _text="{tr}Edit Newsletter{/tr}"}
	{button href="tiki-admin_newsletters.php" _text="{tr}Admin Newsletters{/tr}"}
	{button href="tiki-send_newsletters.php?nlId=$nlid_encod" _text="{tr}Send Newsletters{/tr}"}
</div>

<table class="normal">
	<tr>
		<th colspan="2">{tr}Newsletter{/tr}</th>
	</tr>
	<tr>
		<td class="even" width="30%">{tr}Name:{/tr}</td>
		<td class="even">{$nl_info.name|escape}</td>
	</tr>
	<tr>
		<td class="even">{tr}Description:{/tr}</td>
		<td class="even">{$nl_info.description|escape|nl2br}</td>
	</tr>
</table>

<h2>{tr}Add subscribers{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
	<input type="hidden" name="nlId" value="{$nlId|escape}" />
	<table class="normal">
		<tr>
			<td class="formcolor" width="30%">{tr}Email:{/tr}</td>
			<td colspan="2" class="formcolor">
				<textarea cols="70" rows="6" wrap="soft" name="email"></textarea>
				<br />
				<i>{tr}You can add several email addresses by separating them with commas.{/tr}</i>
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}User:{/tr}</td>
			<td class="formcolor">
				<select name="subuser">
					<option value="">---</option>
					{foreach key=id item=one from=$users}
						<option value="{$one|escape}">{$one|escape}</option>
					{/foreach}
				</select>
			</td>
			<td class="formcolor" rowspan="3" valign="middle">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>{tr}Add email:{/tr}</td>
						<td>
							<input type="radio" name="addemail" value="y" />
						</td>
					</tr>
					<tr>
						<td>{tr}Add user:{/tr}</td>
						<td>
							<input type="radio" name="addemail" value="n" checked="checked" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}All users:{/tr}</td>
			<td class="formcolor"><input type="checkbox" name="addall" /></td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Users from group:{/tr}</td>
			<td class="formcolor">
				<select name="group">
					<option value="">---</option>
					{section name=x loop=$groups}
						<option value="{$groups[x]|escape}">{$groups[x]|escape}</option>
					{/section}
				</select>
				<br />
				<i>{tr}Group subscription also subscribes included groups{/tr}</i>
			</td>
		</tr>
		{if $nl_info.validateAddr eq "y"}
			<tr>
				<td class="formcolor">
					{tr}Don't send confirmation mail{/tr}
				</td>
				<td colspan="2" class="formcolor">
					<input type="checkbox" name="confirmEmail" />
				</td>
			</tr>
		{/if}
		<tr>
			<td class="formcolor">&nbsp;</td>
			<td class="formcolor" colspan="2">
				<i>{tr}The user email will be refreshed at each newsletter sending{/tr}</i>
			</td>
		</tr>
		<tr>
			<td class="formcolor">&nbsp;</td>
			<td class="formcolor" colspan="2">
				<input type="submit" name="add" value="{tr}Add{/tr}" />
			</td>
		</tr>
	</table>
</form>

{if $tiki_p_batch_subscribe_email eq "y" && $tiki_p_subscribe_email eq "y"} 	 
	<h2>{tr}Import emails from file{/tr}</h2> 	 
	<form action="tiki-admin_newsletter_subscriptions.php" method="post" enctype="multipart/form-data"> 	 
		<input type="hidden" name="nlId" value="{$nlId|escape}" /> 	 
		<table class="normal"> 	 
			<tr>
				<td class="formcolor" width="30%">{tr}File:{/tr}</td>
				<td class="formcolor" colspan="2">
					<input type="file" name="batch_subscription" />
					<br />
					<i>{tr}txt file, one e-mail per line{/tr}</i>
				</td>
			</tr> 	 
			{if $nl_info.validateAddr eq "y"}
				<td class="formcolor" width="30%">
					{tr}Don't send confirmation mails{/tr}
				</td>
				<td colspan="2" class="formcolor">
					<input type="checkbox" name="confirmEmail" />
				</td>
			{/if}
			<tr>
				<td class="formcolor">&nbsp;</td>
				<td class="formcolor" colspan="2">
					<input type="submit" name="addbatch" value="{tr}Add{/tr}" />
				</td>
			</tr> 	 
		</table> 	 
	</form>
	<h2>{tr}Import emails from wiki page{/tr}</h2>
	<form action="tiki-admin_newsletter_subscriptions.php" method="post">
		<input type="hidden" name="nlId" value="{$nlId|escape}" /> 
		<table class="normal">
			<tr>
				<td class="formcolor" width="30%">Wiki page</td>
				<td class="formcolor" colspan="2">
					<input type="text" name="wikiPageName" value="" size="60"  />
					<br />
					<i>{tr}Wiki page, one e-mail per line{/tr}</i>
				</td>
			</tr>
			{if $nl_info.validateAddr eq "y"}
				<tr>
					<td class="formcolor" width="30%">
						{tr}Don't send confirmation mails{/tr}
					</td>
					<td colspan="2" class="formcolor">
						<input type="checkbox" name="confirmEmail" />
					</td>
				</tr>
			{/if}
			<tr>
				<td class="formcolor" width="30%">&nbsp;</td>
				<td class="formcolor" colspan="2">
					<input type="submit" name="importPage" value="Add" width="30" />
				</td>
			</tr>
		</table>
	</form>
		 
{/if}

<h2>{tr}Subscribe group{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
	<input type="hidden" name="nlId" value="{$nlId|escape}" />
	<table class="normal">
		<tr>
			<td class="formcolor" width="30%">{tr}Group:{/tr}</td>
			<td class="formcolor" colspan="2">
				<select name="group">
					<option value="">---</option>
					{section name=x loop=$groups}
						<option value="{$groups[x]|escape}">{$groups[x]|escape}</option>
					{/section}
				</select>
				<br />
				<i>{tr}Included group, group users and emails will be refreshed at each newsletter sending{/tr}</i>
			</td>
		</tr>
		<tr>
			<td class="formcolor">&nbsp;</td>
			<td class="formcolor" colspan="2"><input type="submit" name="addgroup" value="{tr}Add{/tr}" /></td>
		</tr>
	</table>
</form>

<h2>{tr}Use subscribers of another newsletter{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
	<input type="hidden" name="nlId" value="{$nlId|escape}" />
	<table class="normal">
		<tr>
			<td class="formcolor" width="30%">{tr}Newsletter:{/tr}</td>
			<td class="formcolor" colspan="2">
				<select name="included">
					<option value="">---</option>
					{section name=x loop=$newsletters}
						{if $nlId ne $newsletters[x].nlId}
							<option value="{$newsletters[x].nlId|escape}">{$newsletters[x].name|escape}</option>
						{/if}
					{/section}
				</select>
				<br />
			</td>
		</tr>
		<tr>
			<td class="formcolor">&nbsp;</td>
			<td class="formcolor" colspan="2">
				<input type="submit" name="addincluded" value="{tr}Add{/tr}" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}Use emails from wiki page{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
	<input type="hidden" name="nlId" value="{$nlId|escape}" /> 
	<table class="normal">
		<tr>
			<td class="formcolor" width="30%">Wiki page</td>
			<td class="formcolor" colspan="2">
				<input type="text" name="wikiPageName" value="" size="60" />
				<br />
				<i>{tr}Emails on a wiki page which will be added at each newsletter sending, one e-mail per line{/tr}</i>
				{jq}$("input[name=wikiPageName]").tiki("autocomplete", "pagename");{/jq}
			</td>
		</tr>
		<tr>
			<td class="formcolor" width="30%">
				{tr}Don't send confirmation mails{/tr}
			</td>
			<td colspan="2" class="formcolor">
				<input type="checkbox" name="noConfirmEmail" checked="checked" />
			</td>
		</tr>
		<tr>
			<td class="formcolor" width="30%">
				{tr}Don't subscribe emails{/tr}
			</td>
			<td colspan="2" class="formcolor">
				<input type="checkbox" name="noSubscribeEmail" checked="checked" />
			</td>
		</tr>
		<tr>
			<td class="formcolor" width="30%">&nbsp;</td>
			<td class="formcolor" colspan="2">
				<input type="submit" name="addPage" value="Add" width="30" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}Subscriptions{/tr}</h2>
{* groups------------------------------------ *}
{if $nb_groups > 0}
	<table class="normal">
		<tr>
			<th>
				<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset_g|urlencode}&amp;sort_mode_g={if $sort_mode_g eq 'groupName_asc'}groupName_desc{else}groupName_asc{/if}">{tr}Group{/tr}</a>
			</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=ix loop=$groups_g}
			<tr>
				<td class="{cycle advance=false}">{$groups_g[ix].groupName|escape}</td>
				<td class="{cycle}">
					<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;remove={$groups_g[ix].nlId|urlencode}&amp;group={$groups_g[ix].groupName|urlencode}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
				</td>
			</tr>
		{/section}
	</table>
{/if}
{* /groups------------------------------------ *}

{* included------------------------------------ *}
{if $nb_included > 0}
	<table class="normal">
		<tr>
			<th>
				<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset_g|urlencode}&amp;sort_mode_i={if $sort_mode_i eq 'name_asc'}name_desc{else}name_asc{/if}">{tr}Newsletter{/tr}</a>
			</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{foreach key=incId item=incName from=$included_n}
			<tr>
				<td class="{cycle advance=false}">
					<a href="tiki-admin_newsletter_subscriptions.php?nlId={$incId|urlencode}">{$incName|escape}</a>
				</td>
				<td class="{cycle}">
					<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;remove={$nlId|urlencode}&amp;included={$incId|urlencode}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
				</td>
			</tr>
		{/foreach}
	</table>
{/if}
{* /included------------------------------------ *}

{* pages------------------------------------ *}
{if $nb_pages > 0}
	<table class="normal">
		<tr>
			<th>{tr}Wiki Page Name{/tr}</th>
			<th>{tr}Validate Addresses{/tr}</th>
			<th>{tr}Add To List{/tr}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=ix loop=$pages}
			<tr>
				<td class="{cycle advance=false}"><a href="{$pages[ix].wikiPageName|sefurl}">{$pages[ix].wikiPageName|escape}</a></td>
				<td class="{cycle advance=false}">{$pages[ix].validateAddrs|escape}</td>
				<td class="{cycle advance=false}">{$pages[ix].addToList|escape}</td>
				<td class="{cycle}">
					<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;remove={$pages[ix].nlId|urlencode}&amp;page={$pages[ix].wikiPageName|urlencode}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
				</td>
			</tr>
		{/section}
	</table>
{/if}
{* /pages------------------------------------ *}

{include file='find.tpl'}

<table class="normal">
	<tr>
		<th>
			<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}eMail{/tr} - {tr}User{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={if $sort_mode eq 'valid_desc'}valid_asc{else}valid_desc{/if}">{tr}Valid{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={if $sort_mode eq 'subscribed_desc'}subscribed_asc{else}subscribed_desc{/if}">{tr}Subscribed{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr>
			<td class="{cycle advance=false}">
				{if $channels[user].isUser == "y"}{$channels[user].email|userlink}{else}{$channels[user].email|escape}{/if}
			</td>
			<td class="{cycle advance=false}">
				{if $channels[user].valid == "n"}
					<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;valid={$channels[user].nlId|urlencode}&amp;{if $channels[user].isUser eq "y"}user{else}email{/if}={$channels[user].email|escape:"url"}" title="{tr}Valid{/tr}">{tr}No{/tr}</a>
				{else}
					{tr}Yes{/tr}
				{/if}
			</td>
			<td class="{cycle advance=false}">{$channels[user].subscribed|tiki_short_datetime}</td>
			<td class="{cycle}">
				<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;remove={$channels[user].nlId|urlencode}&amp;{if $channels[user].isUser eq "y"}subuser{else}email{/if}={$channels[user].email|escape:"url"}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
			</td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

<h2>{tr}Export Subscriber Emails{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
	<input type="hidden" name="nlId" value="{$nlId|escape}" /> 
	<table class="normal">
		<tr>
			<td class="formcolor" width="30%">&nbsp;</td>
			<td class="formcolor" colspan="2">
				<input type="submit" name="export" value="{tr}Export{/tr}" />
			</td>
		</tr>
	</table>
</form>

