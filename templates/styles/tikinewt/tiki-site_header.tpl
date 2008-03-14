{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/tikinewt/tiki-site_header.tpl,v 1.1.2.3 2008-03-14 08:13:57 chibaguy Exp $ *}
{* Template for Tikiwiki site identity header *}{if $prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}
		{eval var=$prefs.sitemycode}{* here will be parsed the custom site header code *}
		{/if}
{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align neq 'center'}
<div class="clearfix" id="sioptions">
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'left'}
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
		<div id="sitead" class="floatright">{eval var=$prefs.sitead}</div>
		{/if}
		<div id="sitelogo" class="floatleft" {if $prefs.sitelogo_bgcolor ne ''}style="background-color: {$prefs.sitelogo_bgcolor};"{/if}>
		<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
	</div>
	{/if}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'right'}
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
		<div id="sitead" class="floatleft">{eval var=$prefs.sitead}</div>
		{/if}
		<div id="sitelogo" class="floatright"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}>
		<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
	</div>
	{/if}
</div>
{/if}

{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'center'}
<div class="clearfix" id="sioptionscentered">
	{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
	<div class="floatright"><div id="bannertopright">{banner zone='topright'}</div></div>{/if}
	{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
		<div id="sitead" class="floatleft" {*style="width: 300px"*}>{eval var=$prefs.sitead}</div>
		{/if}
	<div id="sitelogo"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}><a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
	</div>	
</div>
{/if}