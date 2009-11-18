{if isset($close_window) and $close_window eq 'y'}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
close();
//--><!]]>
</script>
{/if}
{* 402: need login
 * 401, 403: perm
 * 404: page does not exist
 * no_redirect_login: error antibot, system...
 * login: error login
 *}
{if !isset($errortype)}{assign var='errortype' value=''}{/if}
{capture assign=mid_data}
	{if ($errortype eq "402")}
		{include file='tiki-login.tpl'}
	{elseif ($errortype eq 401 or $errortype eq 403) and !empty($prefs.permission_denied_url)}
		{redirect url=$prefs.permission_denied_url}
	{else}
		{if $errortype eq 401 && empty($user) and  $prefs.permission_denied_login_box eq 'y'} {* permission denied *}
			{assign var='errortitle' value='{tr}Please login{/tr}' }
		{else}
			{assign var='errortitle' value='{tr}Error{/tr}' }
		{/if}
		<br />
		<div class="cbox">
			<div class="cbox-title">{icon _id=exclamation alt={tr}Error{/tr} style=vertical-align:middle} {$errortitle|default:"{tr}Error{/tr}"}</div>
			<div class="cbox-data">
				<br />
				{if ($errortype eq "404")}
					{if $prefs.feature_likePages eq 'y'}
						{if $likepages}
							<p>{tr}Perhaps you are looking for:{/tr}</p>
							<ul>
								{section name=back loop=$likepages}
								<li><a href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]|escape}</a></li>
								{/section}
							</ul>
							<br />
						{else}
				 			{tr}There are no wiki pages similar to '{$page}'{/tr}
				 			<br /><br />
				 		{/if}
					{/if}

					{if $prefs.feature_search eq 'y'}
						{if $prefs.feature_search_fulltext eq 'y'}
							{include file='tiki-searchresults.tpl' searchNoResults="false" searchStyle="menu" searchOrientation="horiz" words="$page"}
						{else}
							{include file='tiki-searchindex.tpl' searchNoResults="true"	searchStyle="menu" searchOrientation="horiz" words="$page"}
						{/if}
					{/if}

					<br />
				{else}
					{if $errortype eq 401 && empty($user) and $prefs.permission_denied_login_box eq 'y'} {* permission denied *}
						{include file='tiki-login.tpl'}
					{elseif !isset($user) and $errortype != 'no_redirect_login' and $errortype != 'login'}
						<div class="simplebox highlight">
							{tr}You are not logged in.{/tr} <a href="tiki-login_scr.php">{tr}Go to Login Page{/tr}</a>
						</div>
						<br /><br />
					{else}
						{if ( isset($msg) ) }
							<div class="simplebox error">
								{$msg}
							</div>
							<br /><br />
						{/if}
					{/if}
				{/if}
				{if isset($extraButton)}
					<div>
					{$extraButton.comment}
					{button href=$extraButton.href _text=$extraButton.text}
					</div>
					<br /><br />
				{/if}

				{if $page and $create eq 'y' and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y' or $tiki_p_edit eq 'y')}{button href="tiki-editpage.php?page=$page" _text="{tr}Create this page{/tr}"} {tr}(page will be orphaned){/tr}<br /><br />{/if}
				{if $prefs.javascript_enabled eq 'y'}{button _onclick="javascript:history.back()" _text="{tr}Go back{/tr}"}<br /><br />{/if}
				{button href=$prefs.tikiIndex _text="{tr}Return to home page{/tr}"}
			</div>
		</div>
	{/if}
{/capture}

{if isset($smarty.request.xjxfun) && $smarty.request.xjxfun eq 'loadComponent'}
{$mid_data}
{else}
{include file='tiki.tpl'}
{/if}
