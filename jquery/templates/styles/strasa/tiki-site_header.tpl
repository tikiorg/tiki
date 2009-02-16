{* $Id$ *}
<div id="header-top">
{if $prefs.feature_sitemenu eq 'y'}
	<div id="tiki-top" class="clearfix">
	{if $prefs.feature_phplayers eq 'y'}
		{phplayers id=$prefs.feature_topbar_id_menu type=horiz}
	{else}
		{menu id=$prefs.feature_topbar_id_menu type=horiz css=y}
	{/if}
	</div>
{/if}

{* Custom code ... *}
{if $prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}
	{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}{* ... and a banner *}
		<div id="sitead" class="floatright">
			{eval var=$prefs.sitead}
		</div>
		<div id="customcodewith_ad">
			{eval var=$prefs.sitemycode}{* here will be parsed the 400px-wide custom site header code *}
		</div>
	{else}
		<div id="customcode">
		{eval var=$prefs.sitemycode}
		</div>
	{/if}
{else}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align neq 'center'}{* Site logo left or right, and sitead or not. *}
		<div class="clearfix" id="sioptions">
		{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'left'}
			{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
				<div id="sitead" class="floatright">{eval var=$prefs.sitead}</div>
			{/if}
			<div id="sitelogo" class="floatleft" {if $prefs.sitelogo_bgcolor ne ''}style="background-color: {$prefs.sitelogo_bgcolor};"{/if}>
				{if $prefs.sitelogo_src}<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>{/if}
				<div id="sitetitles">
					<div id="sitetitle"><a href="index.php">{$prefs.sitetitle}</a></div>
					<div id="sitesubtitle">{$prefs.sitesubtitle}</div>
				</div>
			</div>
			{/if}
		{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'right'}
			{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
				<div id="sitead" class="floatleft">{eval var=$prefs.sitead}</div>
			{/if}
			<div id="sitelogo" class="floatright"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}>
				{if $prefs.sitelogo_src}<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>{/if}
				<div id="sitetitles">
					<div id="sitetitle"><a href="index.php">{$prefs.sitetitle}</a></div>
					<div id="sitesubtitle">{$prefs.sitesubtitle}</div>
				</div>
			</div>
		{/if}
		</div>
	{/if}

	{* Sitelogo centered, and sitead: to work in small vertical space, ad (halfbanner) is floated left; a second bannerzone is floated right. *}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'center'}
	<div class="clearfix" id="sioptionscentered">
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
		<div id="bannertopright" class="floatright"><div style="width: 234px; height: 60px; background: yellow">banner zone='topright'</div></div>
		<div id="sitead" class="floatleft" {*style="width: 300px"*}>{eval var=$prefs.sitead}</div>
		{/if}
		<div id="sitelogo"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}>{if $prefs.sitelogo_src}<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>{/if}
			<div id="sitetitles">
				<div id="sitetitle"><a href="index.php">{$prefs.sitetitle}</a></div>
				<div id="sitesubtitle">{$prefs.sitesubtitle}</div>
			</div>
		</div>	
	</div>
	{/if}

	{* No sitelogo but a sitead: ad is centered. *}
	{if $prefs.feature_sitelogo neq 'y'}
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
			<div align="center">
				{eval var=$prefs.sitead}
			</div>
		{/if}
	{/if}
{/if}
</div>
{if $filegals_manager ne 'y' and $print_page ne 'y'}
	{if $prefs.feature_site_login eq 'y'}
		{include file="tiki-site_header_login.tpl"}
	{/if}
{/if}