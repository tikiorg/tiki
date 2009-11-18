{* $Id$ *}
{* site identity options: logo, site title and subtitle, banner ad, custom code *}
	{* No site logo but custom code *}
	{if $prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}
		{if $prefs.feature_sitelogo neq 'y' &&  $prefs.feature_banners eq 'y' && $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
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
	{/if}
	{* Site logo left or right, and sitead or not. *}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align neq 'center'}
		<div class="clearfix" id="sioptions">
			{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'left'}
				{if $prefs.feature_banners eq 'y' && $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
					<div id="sitead" class="floatright">{eval var=$prefs.sitead}</div>
				{/if}
				<div id="sitelogo" class="floatleft"{if $prefs.sitelogo_bgcolor or $prefs.sitelogo_bgstyle ne ''} style="background-color: {$prefs.sitelogo_bgcolor}; {$prefs.sitelogo_bgstyle};"{/if}>
					{if $prefs.sitelogo_src}<a href="./" title="{tr}{$prefs.sitelogo_title}{/tr}"><img src="{$prefs.sitelogo_src}" alt="{tr}{$prefs.sitelogo_alt}{/tr}" /></a>{/if}
				</div>
				{if $prefs.sitetitle or $prefs.sitesubtitle}
					<div id="sitetitles" class="floatleft">
						<div id="sitetitle"><a href="index.php">{tr}{$prefs.sitetitle}{/tr}</a></div>
						<div id="sitesubtitle">{tr}{$prefs.sitesubtitle}{/tr}</div>
					</div>
				{/if}
			{/if}
			{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'right'}
				{if $prefs.feature_banners eq 'y' && $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
					<div id="sitead" class="floatleft">{eval var=$prefs.sitead}</div>
				{/if}
				{if $prefs.sitetitle or $prefs.sitesubtitle}
					<div id="sitetitles" class="floatright">
						<div id="sitetitle"><a href="index.php">{tr}{$prefs.sitetitle}{/tr}</a></div>
						<div id="sitesubtitle">{tr}{$prefs.sitesubtitle}{/tr}</div>
					</div>
				{/if}
				<div id="sitelogo" class="floatright"{if $prefs.sitelogo_bgcolor or $prefs.sitelogo_bgstyle ne ''} style="background-color: {$prefs.sitelogo_bgcolor}; {$prefs.sitelogo_bgstyle};"{/if}>
					{if $prefs.sitelogo_src}<a href="./" title="{tr}{$prefs.sitelogo_title}{/tr}"><img src="{$prefs.sitelogo_src}" alt="{tr}{$prefs.sitelogo_alt}{/tr}" /></a>{/if}
				</div>
			{/if}
		</div>
	{/if}

{* Site logo centered, and sitead: to work in small vertical space, ad (halfbanner) is floated left; a second bannerzone is floated right. *}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'center'}
		<div class="clearfix" id="sioptionscentered">
			{if $prefs.feature_banners eq 'y' && $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
				<div class="floatright"><div id="bannertopright">{banner zone='topright'}</div></div>
			{/if}
			{if $prefs.feature_banners eq 'y' && $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
				<div id="sitead" class="floatleft" {*style="width: 300px"*}>{eval var=$prefs.sitead}</div>
			{/if}
			<div id="sitelogo"{if $prefs.sitelogo_bgcolor or $prefs.sitelogo_bgstyle ne ''} style="background-color: {$prefs.sitelogo_bgcolor}; {$prefs.sitelogo_bgstyle};"{/if}>
				{if $prefs.sitelogo_src}<a href="./" title="{tr}{$prefs.sitelogo_title}{/tr}"><img src="{$prefs.sitelogo_src}" alt="{tr}{$prefs.sitelogo_alt}{/tr}" /></a>{/if}
			</div>
			{if $prefs.sitetitle or $prefs.sitesubtitle}
				<div id="sitetitles">
					<div id="sitetitle"><a href="index.php">{tr}{$prefs.sitetitle}{/tr}</a></div>
					<div id="sitesubtitle">{tr}{$prefs.sitesubtitle}{/tr}</div>
				</div>
			{/if}
		</div>
	{/if}

{* No sitelogo, no custom code but a sitead: ad is centered. *}
	{if $prefs.feature_sitelogo eq 'n' and !($prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y'))}
		{if $prefs.feature_banners eq 'y' && $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
			<div align="center">
				{eval var=$prefs.sitead}
			</div>
		{/if}
	{/if}



