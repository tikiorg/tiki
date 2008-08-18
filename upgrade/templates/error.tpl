{if $close_window eq 'y'}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
close();
//--><!]]>
</script>
{/if}
{capture assign=mid_data}
	{if ($errortype eq "402")}
		{include file=tiki-login.tpl}
	{elseif $errortype eq 401 and !empty($prefs.permission_denied_url)}
		{php}global $prefs; header('Location:'.$prefs['permission_denied_url']); exit; {/php}
	{else}
		<br />
		<div class="cbox">
			<div class="cbox-title">{icon _id=exclamation alt={tr}Error{/tr} style=vertical-align:middle} {$errortitle|default:"{tr}Error{/tr}"}</div>
			<div class="cbox-data">
				<br />
				{if ($errortype eq "404")}
					{if $prefs.feature_likePages eq 'y'}
						{if $likepages}
							{tr}Perhaps you were looking for:{/tr}
							<ul>
								{section name=back loop=$likepages}
								<li><a href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
								{/section}
							</ul>
							<br />
						{else}
				 			{tr}There are no wiki pages similar to '{$page}'{/tr}
				 			<br /><br />
				 		{/if}
					{/if}

					{if $prefs.feature_search eq 'y'}
						{include
							file="tiki-searchindex.tpl"
							searchNoResults="true"
							searchStyle="menu"
							searchOrientation="horiz"
							words="$page"
						}
					{/if}

					<br />
				{else}
					{if ( isset($msg) ) }
						<div class="simplebox error">
							{$msg}
						</div>
						<br /><br />
					{/if}
					{if $errortype eq 401 && empty($user) and  $prefs.permission_denied_login_box eq 'y'} {* permission denied *}
						{include file=tiki-login.tpl}
					{elseif !isset($user) }
						<div class="simplebox highlight">
							{tr}You are not logged in.{/tr} <a href="tiki-login_scr.php">{tr}Go to Login Page{/tr}</a>
						</div>
						<br /><br />
					{/if}
				{/if}
				{if $page and $create eq 'y' and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y' or $tiki_p_edit eq 'y')}<a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Create this page{/tr}</a> {tr}(page will be orphaned){/tr}<br /><br />{/if}
				<a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br /><br />
				<a href="{$prefs.tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
			</div>
		</div>
	{/if}
{/capture}

{if isset($smarty.request.xajax) && $smarty.request.xajax eq 'loadComponent'}
{xajax_response content=$mid_data}
{else}
{include file=tiki.tpl}
{/if}
