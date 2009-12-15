{* $Id$ *}
{title help="Newsletters"}{tr}Admin newsletters{/tr}{/title}

<div class="navbar">
	{if $info.nlId}
		{button href="tiki-admin_newsletters.php" _text="{tr}Create Newsletter{/tr}"}
	{/if}
	{button href="tiki-newsletters.php" _text="{tr}List Newsletters{/tr}"}
	{button href="tiki-send_newsletters.php" _text="{tr}Send Newsletters{/tr}"}
</div>

{if $info.nlId}
	<h2>{tr}Edit Newsletter{/tr}</h2>
{else}
	<h2>{tr}Create Newsletter{/tr}</h2>
{/if}

{if $individual eq 'y'}
	<a class="link" href="tiki-objectpermissions.php?objectName={$info.name|escape:"url"}&amp;objectType=newsletter&amp;permType=newsletters&amp;objectId={$info.nlId}">{tr}There are individual permissions set for this newsletter{/tr}</a><br /><br />
{/if}

<form action="tiki-admin_newsletters.php" method="post">
	<input type="hidden" name="nlId" value="{$info.nlId|escape}" />
	<input type="hidden" name="author" value="{$user|escape}" />
	<table class="normal">
		<tr>
			<td class="formcolor">{tr}Name{/tr}:</td>
			<td class="formcolor">
				<input type="text" name="name" value="{$info.name|escape}" />
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Description{/tr}:</td>
			<td class="formcolor">
				<textarea name="description" rows="4" cols="40">{$info.description|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Users can subscribe/unsubscribe to this list{/tr}</td>
			<td class="formcolor">
				<input type="checkbox" name="allowUserSub" {if $info.allowUserSub eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Users can subscribe any email address{/tr}</td>
			<td class="formcolor">
				<input type="checkbox" name="allowAnySub" {if $info.allowAnySub eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Add unsubscribe instructions to each newsletter{/tr}</td>
			<td class="formcolor">
				<input type="checkbox" name="unsubMsg" {if $info.unsubMsg eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Validate email addresses{/tr}</td>
			<td class="formcolor">
				<input type="checkbox" name="validateAddr" {if $info.validateAddr eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Allow customized text message to be sent with the html version{/tr}</td>
			<td class="formcolor">
				<input type="checkbox" name="allowTxt" {if $info.allowTxt eq 'y'}checked="checked"{/if} />
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
<h2>{tr}Newsletters{/tr}</h2>

{if $channels or ($find ne '')}
  {include file='find.tpl'}
{/if}

<table class="normal">
	<tr>
		<th>
			<a href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nlId_desc'}nlId_asc{else}nlId_desc{/if}">{tr}ID{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Newsletter{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}Author{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}Users{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'editions_desc'}editions_asc{else}editions_desc{/if}">{tr}Editions{/tr}</a>
		</th>
		<th>
			<a>{tr}Drafts{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastSent_desc'}lastSent_asc{else}lastSent_desc{/if}">{tr}Last Sent{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr>
			<td class="{cycle advance=false}">{$channels[user].nlId}</td>
			<td class="{cycle advance=false}">
				<a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nlId={$channels[user].nlId}" title="{tr}Edit{/tr}">{$channels[user].name}</a>
				<div class="subcomment">{$channels[user].description}</div>
			</td>
			<td class="{cycle advance=false}">{$channels[user].author}</td>
			<td class="{cycle advance=false}">{$channels[user].users} ({$channels[user].confirmed})</td>
			<td class="{cycle advance=false}">{$channels[user].editions}</td>
			<td class="{cycle advance=false}">{$channels[user].drafts}</td>
			<td class="{cycle advance=false}">{$channels[user].lastSent|tiki_short_datetime}</td>
			<td class="{cycle}">
				{if ($channels[user].tiki_p_admin eq 'y') or ($channels[user].tiki_p_assign_perm_newsletters eq 'y')}
					<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=newsletter&amp;permType=newsletters&amp;objectId={$channels[user].nlId}" title="{tr}Assign Permissions{/tr}">
						<img width="16" height="16" alt="{tr}Assign Permissions{/tr}" src="pics/icons/key{if $channels[user].individual eq 'y'}_active{/if}.png" />
					</a>
				{/if}
				<a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nlId={$channels[user].nlId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
				<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$channels[user].nlId}" title="{tr}Subscriptions{/tr}">{icon _id='group' alt='{tr}Subscriptions{/tr}'}</a>
				<a class="link" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}" title="{tr}Send Newsletter{/tr}">{icon _id='email' alt="{tr}Send Newsletter{/tr}"}</a>
				<a class="link" href="tiki-newsletter_archives.php?nlId={$channels[user].nlId}" title="{tr}Archives{/tr}">{icon _id='database' alt="{tr}Archives{/tr}"}</a>
				<a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].nlId}" title="{tr}Remove{/tr}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
			</td>
		</tr>
	{sectionelse}
		<tr>
			<td class="odd" colspan="10">{tr}No records{/tr}</td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
