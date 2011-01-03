{title help="Newsletters"}{tr}Newsletters{/tr}{/title}

{if $tiki_p_admin_newsletters eq "y"}
	<div class="navbar">
		{button href="tiki-admin_newsletters.php" _text="{tr}Admin Newsletters{/tr}"}
	</div>
{/if}

{if $subscribed eq 'y'}
	<div class="simplebox highlight">
		{tr}Thanks for your subscription. You will receive an email soon to confirm your subscription. No newsletters will be sent to you until the subscription is confirmed.{/tr}
	</div>
{/if}

{if $unsub eq 'y'}
	<div class="highlight">
		{tr}Your email address was removed from the list of subscriptors.{/tr}</div>
{elseif $unsub eq 'f'}
	<div class="simplebox error">{tr}Removal of your email address failed.{/tr}</div>
{/if}

{if $confirm eq 'y'}
	<table class="normal">
		<tr>
			<th colspan="2" class="highlight">{tr}Subscription confirmed!{/tr}</th>
		</tr>
		<tr>
			<td class="even">{tr}Name:{/tr}</td>
			<td class="even">{$nl_info.name|escape}</td>
		</tr>
		<tr>
			<td class="even">{tr}Description:{/tr}</td>
			<td class="even">{$nl_info.description|escape|nl2br}</td>
		</tr>
	</table>
	<br />
{elseif $confirm eq 'f'}
	<div class="simplebox error">{tr}Subscription failed.{/tr}</div>
	<br />
{/if}

{if $subscribe eq 'y'}
	<h2>
		{tr}Subscribe to Newsletter{/tr}
	</h2>
	<form method="post" action="tiki-newsletters.php">
		<input type="hidden" name="nlId" value="{$nlId|escape}" />
		<table class="normal">
			<tr>
				<td class="even">{tr}Name:{/tr}</td>
				<td class="even"><strong>{$nl_info.name|escape}</strong></td>
			</tr>
			<tr>
				<td class="even">{tr}Description:{/tr}</td>
				<td class="even">{$nl_info.description|escape|nl2br}</td>
			</tr>
			{if ($nl_info.allowUserSub eq 'y') or ($tiki_p_admin_newsletters eq 'y')}
				{if $tiki_p_subscribe_email eq 'y' and (($nl_info.allowAnySub eq 'y' and $user) || !$user)}
					<tr>
						<td class="even">{tr}Email:{/tr}</td>
						<td class="even"><input type="text" name="email" size="40" value="{$email|escape}" /></td>
					</tr>
				{else}
					<input type="hidden" name="email" value="{$email|escape}" />
				{/if}
				{if !$user and $prefs.feature_antibot eq 'y'}
					{include file='antibot.tpl' tr_style="formcolor"}
				{/if}
				<tr>
					<td class="even">&nbsp;</td>
					<td class="even">
						<input type="submit" name="subscribe" value="{tr}Subscribe to this Newsletter{/tr}" />
					</td>
				</tr>
			{/if}
		</table>
	</form>
{/if}

{if $showlist eq 'y'}
	<h2>{tr}Available Newsletters{/tr}</h2>

	{if $channels or $find ne''}
		{include file='find.tpl'}
	{/if}

	<table class="normal">
		<tr>
			<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Newsletter{/tr}{/self_link}</th>
			<th style="width:80px">{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$channels}
			{if $channels[user].tiki_p_subscribe_newsletters eq 'y'}
				<tr class="{cycle}">
					<td>
						<a class="tablename" href="tiki-newsletters.php?nlId={$channels[user].nlId}&amp;info=1" title="{tr}Subscribe to Newsletter{/tr}">{$channels[user].name|escape}</a>
						<div class="subcomment">{$channels[user].description|escape|nl2br}</div>
					</td>
					<td>
						{if $channels[user].tiki_p_subscribe_newsletters eq 'y'}
							<a class="link" href="tiki-newsletters.php?nlId={$channels[user].nlId}&amp;info=1" title="{tr}Subscribe to Newsletter{/tr}">{icon _id='newspaper_add' alt="{tr}Subscribe to Newsletter{/tr}"}</a>
						{/if}
						{if $channels[user].tiki_p_send_newsletters eq 'y'}
							<a class="link" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}" title="{tr}Send Newsletter{/tr}">{icon _id='email' alt="{tr}Send Newsletter{/tr}"}</a>
						{/if}
						{if $channels[user].tiki_p_admin_newsletters eq 'y'}
							<a class="link" href="tiki-admin_newsletters.php?nlId={$channels[user].nlId}&amp;cookietab=2#anchor2"
							title="{tr}Admin{/tr}">{icon _id='wrench' alt="{tr}Admin{/tr}"}</a>
						{/if}
					</td>
				</tr>
			{/if}
		{sectionelse}
			<tr><td class="odd" colspan="2"><strong>{tr}No records found.{/tr}</strong></td></tr>
		{/section}
	</table>

	{pagination_links cant=$cant offset=$offset step=$maxRecords}{/pagination_links}
{/if}
