{strip}
{if isset($close_window) and $close_window eq 'y'}
{jq}
close();
{/jq}
{/if}
{* 402: need login
 * 401, 403: perm
 * 404: page does not exist
 * no_redirect_login: error antibot, system...
 * login: error login
 * Usually available variables : $errortitle, $msg, $errortype
 * If $commenttype is 'note' and $msg is set, the $msg will be shown in a nicer non-treatening remarksbox
 *}
{if !isset($errortype)}{assign var='errortype' value=''}{/if}
{capture assign=mid_data}

	{if ($errortype eq "402")}
		{include file='tiki-login.tpl'}
	{elseif ($errortype eq 401 or $errortype eq 403) and !empty($prefs.permission_denied_url) and $prefs.permission_denied_login_box ne 'y'}
		{redirect url=$prefs.permission_denied_url}
	{else}

		{if ($errortype eq 401 || $errortype eq 403) && empty($user) and $prefs.permission_denied_login_box eq 'y'} {* permission denied *}
			{assign var='errortitle' value="{tr}Please log in{/tr}"}
		{else}
			{assign var='errortitle' value="{tr}Error{/tr}"}
		{/if}

		{if $errortype eq "404" and isset($file_error)}
			{remarksbox type='errors' title="{tr}File error{/tr}"}
				{$file_error}
			{/remarksbox}
		{elseif $errortype eq "404" and isset($page)}
			{remarksbox type='errors' title=$errortitle}
				{tr}Page not found{/tr}<br>{$page|escape}
			{/remarksbox}
			{if $prefs.feature_likePages eq 'y'}
				{if $likepages}
					<p>{tr}Perhaps you are looking for:{/tr}</p>
					<ul>
						{section name=back loop=$likepages}
						<li><a href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]|escape}</a></li>
						{/section}
					</ul>
				{else}
					{remarksbox type="tip" title="{tr}Information{/tr}"}
						{tr}There are no wiki pages similar to '{$page|escape}'{/tr}
					{/remarksbox}
				{/if}
			{/if}

			{if ($prefs.feature_search eq 'y' or $prefs.feature_search_fulltext eq 'y') && $tiki_p_search eq 'y'}
				{if $prefs.feature_search_fulltext eq 'y'}
					{include file='tiki-searchresults.tpl' searchNoResults="false" searchStyle="menu" searchOrientation="horiz" words="$page"}
				{else}
					{include file='tiki-searchindex_form.tpl' searchNoResults="true" searchStyle="menu" searchOrientation="horiz" words="$page" filter=$filter}
				{/if}
			{/if}
		{elseif $commenttype eq "note" and isset($msg)}
			{remarksbox type='note' title=$title}
				{$msg}
			{/remarksbox}
		{else}
			{if isset($token_error)}
				{remarksbox type='errors' title="{tr}Token Error{/tr}"}
					{$token_error}
				{/remarksbox}
			{elseif ($errortype eq 401 || $errortype eq 403) && empty($user) and $prefs.permission_denied_login_box eq 'y'} {* permission denied *}
				{remarksbox type='errors' title=$errortitle}
					{tr}Permission denied{/tr}
				{/remarksbox}
				{include file='tiki-login.tpl'}
			{elseif !isset($user) and $errortype != 'no_redirect_login' and $errortype != 'login' and empty($msg)}
				{remarksbox type='errors' title=$errortitle}
					{tr}You are not logged in.{/tr} <a href="tiki-login_scr.php">{tr}Go to Log in Page{/tr}</a>
				{/remarksbox}
			{else}
				{remarksbox type='errors' title=$errortitle}
					{$msg}
					{if !empty($required_preferences)}
						{remarksbox type='note' title="{tr}Settings{/tr}" close="n"}
						<form method="post" action="tiki-admin.php" role="form" class="form">
							<input type="hidden" name="ticket" value="{$ticket|escape}">
							{foreach from=$required_preferences item=pref}
								{preference name=$pref}
							{/foreach}
							<div class="text-center">
								<input type="submit" class="btn btn-primary btn-sm" value="{tr}Apply{/tr}">
							</div>
						</form>
						{/remarksbox}
					{/if}
				{/remarksbox}
			{/if}
		{/if}

		{if isset($extraButton)}
			{remarksbox type='errors' title=$errortitle}
			{$extraButton.comment}
			{button href=$extraButton.href _text=$extraButton.text}
			{/remarksbox}
		{/if}

		{if isset($page) and $page and $create eq 'y' and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y' or $tiki_p_edit eq 'y')}
			{button href="tiki-editpage.php?page=$page" _text="{tr}Create this page{/tr}"} {tr}(page will be orphaned){/tr}
			<br><br>
		{/if}

		{* Hide the error navigation on the homepage *}
		{if !isset($page) or $prefs.site_wikiHomePage neq $page}
			{if $prefs.javascript_enabled eq 'y'}
				{button _type="link" _icon_name="arrow-left" _onclick="javascript:history.back();return false;" _text="{tr}Go back{/tr}" _ajax="n"}
				<br><br>
			{/if}

			{button href=$prefs.tikiIndex _type="link" _icon_name="home" _text="{tr}Return to home page{/tr}"}
		{/if}
	{/if}
{/capture}

{include file='tiki.tpl'}
{/strip}
