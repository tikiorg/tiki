{* $Id$ *}

{title help="Shoutbox"}{tr}Shoutbox{/tr}{/title}

{if $tiki_p_admin_shoutbox eq 'y'}
	<div class="t_navbar">
		<a href="tiki-admin_shoutbox_words.php" class="btn btn-link" title="List">
			{icon name="list"} {tr}Banned Words{/tr}
		</a>
	{*	{button href="tiki-admin_shoutbox_words.php" class="btn btn-default" _text="{tr}Banned Words{/tr}"} *}
	</div>

	<h2>{tr}Change shoutbox general settings{/tr}</h2>
	<form action="tiki-shoutbox.php" method="post" class="form-horizontal">
		<div class="checkbox">
            <label class="control-label col-md-offset-3">
                <input type="checkbox" name="shoutbox_autolink" value="on"{if $prefs.shoutbox_autolink eq 'y'} checked="checked"{/if}>
                {tr}auto-link urls{/tr}
            </label>
		</div>
        <div class="text-center">
			<input type="submit" class="btn btn-default btn-sm" name="shoutbox_admin" value="{tr}Save{/tr}">
        </div>
    </form>
{/if}

{if $tiki_p_post_shoutbox eq 'y'}
	<h2>{tr}Post or edit a message{/tr}</h2>
	{if $msg}
		<div class="simplebox highlight">{$msg}</div>
	{/if}
	{js_maxlength textarea=message maxlength=255}
	<form class="form-horizontal" action="tiki-shoutbox.php" method="post" onsubmit="return verifyForm(this);">
		<input type="hidden" name="msgId" value="{$msgId|escape}">
		<div class="form-group">
            <label class="control-label col-md-3" for="message">{tr}Message:{/tr}</label>
			<div class="col-md-9">
				<textarea class="form-control" name="message" id="message">{$message|escape}</textarea>
				{if $prefs.feature_socialnetworks eq 'y' && $user neq ''}
					{if $prefs.socialnetworks_twitter_consumer_key neq ''}
						<div class="checkbox">
                            <input type="checkbox" name="tweet" id="tweet" value='1'>
                            {tr}Tweet with Twitter{/tr}
                        </div>
					{/if}
					{if $prefs.socialnetworks_facebook_application_id neq ''}
						<div class="checkbox">
                            <input type="checkbox" name="facebook" id="facebook" value='1'>
                            {tr}Post on my Facebook wall{/tr}
                        </div>
					{/if}
				{/if}
            </div>
        </div>
		{if $prefs.feature_antibot eq 'y' && $user eq ''}
			{include file='antibot.tpl'}
		{/if}
		<div class="text-center">
			<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
        </div>
	</form>
{/if}

<h2>{tr}Messages{/tr}</h2>

{include file='find.tpl'}

{section name=user loop=$channels}
	<div class="shoutboxmsg">
		<b><a href="tiki-user_information.php?view_user={$channels[user].user}">{$channels[user].user}</a></b>, {$channels[user].timestamp|tiki_long_date}, {$channels[user].timestamp|tiki_long_time}

		{if $tiki_p_admin_shoutbox eq 'y' || $channels[user].user == $user}
			<a href="tiki-shoutbox.php?find={$find}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;msgId={$channels[user].msgId}" class="tips" title=":{tr}Edit{/tr}">
				{icon name='edit'}
			</a>
			<a href="tiki-shoutbox.php?find={$find}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].msgId}" class="tips" title=":{tr}Remove{/tr}">
				{icon name='remove'}
			</a>
		{/if}
		<br>
		{$channels[user].message}
	</div>
{/section}

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
