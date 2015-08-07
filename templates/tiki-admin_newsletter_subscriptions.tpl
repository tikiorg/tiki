{* $Id$ *}
{assign var=nlId_urlencoded value=$nlId|urlencode}
{title url="tiki-admin_newsletter_subscriptions.php?nlId=$nlId_urlencoded"}{tr}Admin newsletter subscriptions{/tr}{/title}

<div class="t_navbar btn-group form-group">
	{button href="tiki-newsletters.php" class="btn btn-default" _icon_name="list" _text="{tr}List{/tr}"}
	{assign var="nlid_encod" value=$nlId|urlencode}
	{button href="tiki-admin_newsletters.php?nlId=$nlid_encod" class="btn btn-default" _icon_name="edit" _text="{tr}Edit{/tr}"}
	{button href="tiki-admin_newsletters.php" class="btn btn-default" _icon_name="cog" _text="{tr}Admin{/tr}"}
	{button href="tiki-send_newsletters.php?nlId=$nlid_encod" class="btn btn-default" _icon_name="envelope" _text="{tr}Send{/tr}"}
</div>

<div class="table-responsive">
	<table class="table table-striped table-hover">
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
</div>

{tabset name='tabs_newsletter_subscriptions'}

	{tab name="{tr}Subscriptions{/tr}"}
		<h2>{tr}Subscriptions{/tr}</h2>
		{* groups------------------------------------ *}
		{if $nb_groups > 0}
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tr>
						<th>
							<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset_g|urlencode}&amp;sort_mode_g={if $sort_mode_g eq 'groupName_asc'}groupName_desc{else}groupName_asc{/if}">
								{tr}Group{/tr}
							</a>
						</th>
						<th></th>
					</tr>

					{section name=ix loop=$groups_g}
						<tr>
							<td class="text">
								{$groups_g[ix].groupName|escape}
								{if count($groups_g[ix].additional_groups)}
									<div>
										{tr}Groups included through inheritance:{/tr}
										{foreach from=$groups_g[ix].additional_groups item=groupName}
											{$groupName|escape}
										{/foreach}
									</div>
								{/if}
							</td>
							<td class="action">
								<a class="tips" title=":{tr}Remove{/tr}" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;remove={$groups_g[ix].nlId|urlencode}&amp;group={$groups_g[ix].groupName|urlencode}">
									{icon name='remove'}
								</a>
							</td>
						</tr>
					{/section}
				</table>
			</div>
		{/if}
		{* /groups------------------------------------ *}

		{* included------------------------------------ *}
		{if $nb_included > 0}
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tr>
						<th>
							<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset_g|urlencode}&amp;sort_mode_i={if $sort_mode_i eq 'name_asc'}name_desc{else}name_asc{/if}">{tr}Newsletter{/tr}</a>
						</th>
						<th></th>
					</tr>

					{foreach key=incId item=incName from=$included_n}
						<tr>
							<td class="text">
								<a href="tiki-admin_newsletter_subscriptions.php?nlId={$incId|urlencode}">{$incName|escape}</a>
							</td>
							<td class="action">
								<a class="tips" title=":{tr}Remove{/tr}" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;remove={$nlId|urlencode}&amp;included={$incId|urlencode}">
									{icon name='remove'}
								</a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
		{/if}
		{* /included------------------------------------ *}

		{* pages------------------------------------ *}
		{if $nb_pages > 0}
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tr>
						<th>{tr}Wiki Page Name{/tr}</th>
						<th>{tr}Validate Addresses{/tr}</th>
						<th>{tr}Add To List{/tr}</th>
						<th></th>
					</tr>

					{section name=ix loop=$pages}
						<tr>
							<td class="text"><a href="{$pages[ix].wikiPageName|sefurl}">{$pages[ix].wikiPageName|escape}</a></td>
							<td class="text">{$pages[ix].validateAddrs|escape}</td>
							<td class="text">{$pages[ix].addToList|escape}</td>
							<td class="action">
								<a class="tips" title=":{tr}Remove{/tr}" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;remove={$pages[ix].nlId|urlencode}&amp;page={$pages[ix].wikiPageName|urlencode}">
									{icon name='remove'}
								</a>
							</td>
						</tr>
					{/section}
				</table>
			</div>
		{/if}
		{* /pages------------------------------------ *}

		{include file='find.tpl'}

		<form method="post" action="tiki-admin_newsletter_subscriptions.php">
			<input type="hidden" name="nlId" value="{$nlId|escape}">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tr>
					<th>
						{select_all checkbox_names='checked[]'}
					</th>
					<th>
						<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr} - {tr}User{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={if $sort_mode eq 'valid_desc'}valid_asc{else}valid_desc{/if}">{tr}Valid{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={if $sort_mode eq 'subscribed_desc'}subscribed_asc{else}subscribed_desc{/if}">{tr}Subscribed{/tr}</a>
					</th>
					<th></th>
				</tr>

				{section name=user loop=$channels}
					<tr>
						<td class="checkbox-cell">
							<input type="checkbox" name="checked[]" value="{$channels[user].code}" {if $smarty.request.checked and in_array($channels[user].code, $smarty.request.checked)}checked="checked"{/if}>
						</td>
						<td class="username">
							{if $channels[user].isUser == "y"}
								{$channels[user].email|userlink}
							{else}
								{$channels[user].email|escape}
							{/if}
						</td>
						<td class="text">
							{if $channels[user].valid == "n"}
								<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;valid={$channels[user].nlId|urlencode}&amp;{if $channels[user].isUser eq "y"}user{else}email{/if}={$channels[user].email|escape:"url"}" title="{tr}Valid{/tr}">{tr}No{/tr}</a>
							{elseif $channels[user].valid == "x"}
								{tr}Unsubscribed{/tr}
							{else}
								{tr}Yes{/tr}
							{/if}
						</td>
						<td class="date">{$channels[user].subscribed|tiki_short_datetime}</td>
						<td class="action">
							<a class="tips" title=":{tr}Remove{/tr}" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId|urlencode}&amp;offset={$offset|urlencode}&amp;sort_mode={$sort_mode|urlencode}&amp;remove={$channels[user].nlId|urlencode}&amp;{if $channels[user].isUser eq "y"}subuser{else}email{/if}={$channels[user].email|escape:"url"}">
								{icon name='remove'}
							</a>
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=5}
				{/section}
			</table>
		</div>

		{if $channels}
			<div align="left">
				{tr}Perform action with checked:{/tr}
				<input type="image" name="delsel" src='img/icons/cross.png' alt="{tr}Delete{/tr}" title="{tr}Delete{/tr}">
			</div>
		{/if}

		</form>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{tab name="{tr}Add subscribers{/tr}"}

		<h2>{tr}Add subscribers{/tr}</h2>
		<form action="tiki-admin_newsletter_subscriptions.php" method="post" class="form-horizontal">
			<input type="hidden" name="nlId" value="{$nlId|escape}">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Email{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<textarea cols="70" rows="6" wrap="soft" name="email" class="form-control"></textarea>
					<div class="small-hint">
						{tr}You can add several email addresses by separating them with commas.{/tr}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}User{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<select name="subuser" class="form-control">
						<option value="">---</option>
						{foreach key=id item=one from=$users}
							<option value="{$one|escape}">{$one|escape}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Add email{/tr}</label>
				<div class="col-sm-3 col-sm-offset-1 margin-bottom-sm">
					<input type="radio" name="addemail" value="y">
				</div>
				<label class="col-sm-2 control-label">{tr}Add user{/tr}</label>
				<div class="col-sm-3 margin-bottom-sm">
					<input type="radio" name="addemail" value="n" checked="checked">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}All users{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="checkbox" name="addall">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Users from group{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<select name="group" class="form-control">
						<option value="">---</option>
						{section name=x loop=$groups}
							<option value="{$groups[x]|escape}">{$groups[x]|escape}</option>
						{/section}
					</select>
					<div class="small-hint">
						{tr}Group subscription also subscribes included groups{/tr}
					</div>
				</div>
			</div>
			{if $nl_info.validateAddr eq "y"}
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Don't send confirmation email{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="checkbox" name="confirmEmail" checked="checked">
					<div class="small-hint">
						{tr}The user email will be refreshed at each newsletter sending{/tr}
					</div>
				</div>
			</div>
			{/if}
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="submit" class="btn btn-primary btn-sm" name="add" value="{tr}Add{/tr}">
				</div>
			</div>
		</form>

		{if $tiki_p_batch_subscribe_email eq "y" && $tiki_p_subscribe_email eq "y"}
			<h2>{tr}Import emails from file{/tr}</h2>
			<form action="tiki-admin_newsletter_subscriptions.php" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="nlId" value="{$nlId|escape}">
				<div class="form-group">
					<label class="col-sm-3 control-label">{tr}File:{/tr}</label>
					<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
						<input type="file" name="batch_subscription">
						<div class="small-hint">
							{tr}.txt file, one email per line{/tr}
						</div>
					</div>
				</div>
				{if $nl_info.validateAddr eq "y"}
				<div class="form-group">
					<label class="col-sm-3 control-label">{tr}Don't send confirmation emails{/tr}</label>
					<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
						<input type="checkbox" name="confirmEmail" checked="checked">
					</div>
				</div>
				{/if}
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
						<input type="submit" class="btn btn-primary btn-sm" name="addbatch" value="{tr}Add{/tr}">
					</div>
				</div>
			</form>
			<h2>{tr}Import emails from wiki page{/tr}</h2>
			<form action="tiki-admin_newsletter_subscriptions.php" method="post" class="form-horizontal">
				<input type="hidden" name="nlId" value="{$nlId|escape}">
				<div class="form-group">
					<label class="col-sm-3 control-label">Wiki page</label>
					<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
						<input type="text" name="wikiPageName" value="" size="60" class="form-control">
						<div class="small-hint">
							{tr}Wiki page, one email per line{/tr}
						</div>
					</div>
				</div>
				{if $nl_info.validateAddr eq "y"}
				<div class="form-group">
					<label class="col-sm-3 control-label">{tr}Don't send confirmation emails{/tr}</label>
					<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
						<input type="checkbox" name="confirmEmail" checked="checked">
					</div>
				</div>
				{/if}
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
						<input type="submit" class="btn btn-primary btn-sm" name="importPage" value="{tr}Add{/tr}">
					</div>
				</div>
			</form>
		{/if}

		<h2>{tr}Subscribe group{/tr}</h2>
		<form action="tiki-admin_newsletter_subscriptions.php" method="post" class="form-horizontal">
			<input type="hidden" name="nlId" value="{$nlId|escape}">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Group{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<select name="group" class="form-control">
						<option value="">---</option>
						{section name=x loop=$groups}
							<option value="{$groups[x]|escape}">{$groups[x]|escape}</option>
						{/section}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Including group inheritance{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="checkbox" name="include_groups" value="y"/>
					<div class="small-hint">
						{tr}Including group, group users and emails will be refreshed at each newsletter sending{/tr}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="submit" class="btn btn-primary btn-sm" name="addgroup" value="{tr}Add{/tr}">
				</div>
			</div>
		</form>

		<h2>{tr}Use subscribers of another newsletter{/tr}</h2>
		<form action="tiki-admin_newsletter_subscriptions.php" method="post" class="form-horizontal">
			<input type="hidden" name="nlId" value="{$nlId|escape}">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Newsletter:{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<select name="included" class="form-control">
						<option value="">---</option>
						{section name=x loop=$newsletters}
							{if $nlId ne $newsletters[x].nlId}
								<option value="{$newsletters[x].nlId|escape}">{$newsletters[x].name|escape}</option>
							{/if}
						{/section}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="submit" class="btn btn-primary btn-sm" name="addincluded" value="{tr}Add{/tr}">
				</div>
			</div>
		</form>

		<h2>{tr}Use emails from wiki page{/tr}</h2>
		<form action="tiki-admin_newsletter_subscriptions.php" method="post" class="form-horizontal">
			<input type="hidden" name="nlId" value="{$nlId|escape}">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Wiki page{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="text" name="wikiPageName" value="" size="60" class="form-control">
					<div class="small-hint">
						{tr}Emails on a wiki page which will be added at each newsletter sending, one email per line{/tr}
					</div>
					{autocomplete element='input[name=wikiPageName]' type='pagename'}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Don't send confirmation emails{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="checkbox" name="noConfirmEmail" checked="checked">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Don't subscribe emails{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="checkbox" name="noSubscribeEmail" checked="checked">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="submit" class="btn btn-primary btn-sm" name="addPage" value="{tr}Add{/tr}">
				</div>
			</div>
		</form>
	{/tab}


	{tab name="{tr}Export Subscriber Emails{/tr}"}
		<h2>{tr}Export Subscriber Emails{/tr}</h2>
		<br>
		<form action="tiki-admin_newsletter_subscriptions.php" method="post" class="form-horizontal">
			<input type="hidden" name="nlId" value="{$nlId|escape}">
			<div class="form-group">
				<div class="col-sm-12 margin-bottom-sm">
					<input type="submit" class="btn btn-primary btn-sm" name="export" value="{tr}Export{/tr}">
				</div>
			</div>
		</form>
	{/tab}

{/tabset}
