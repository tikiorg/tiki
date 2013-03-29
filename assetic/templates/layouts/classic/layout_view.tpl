{* $Id: tiki.tpl 45260 2013-03-21 19:41:19Z lphuberdeau $ *}<!DOCTYPE html>
<!doctype html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
	<head>
		{include file='header.tpl'}
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body{html_body_attributes}>
		{$cookie_consent_html}

		{if $prefs.feature_ajax eq 'y'}
			{include file='tiki-ajax_header.tpl'}
		{/if}
		
		<div class="container">
			<div class="row">
				<div class="span12">
					{modulelist zone=top}
				</div>
			</div>
			<div class="row">
				<div class="span12">
					{modulelist zone=topbar}
				</div>
			</div>

			<div class="row">
				{if zone_is_empty('left') and zone_is_empty('right')}
					<div class="span12">
						{block name=mid_content}{/block}
					</div>
				{elseif zone_is_empty('left')}
					<div class="span10">
						{block name=mid_content}{/block}
					</div>
					<div class="span2">
						{modulelist zone=right}
					</div>
				{elseif zone_is_empty('right')}
					<div class="span2">
						{modulelist zone=left}
					</div>
					<div class="span10">
						{block name=mid_content}{/block}
					</div>
				{else}
					<div class="span2">
						{modulelist zone=left}
					</div>
					<div class="span8">
						{block name=mid_content}{/block}
					</div>
					<div class="span2">
						{modulelist zone=right}
					</div>
				{/if}
			</div>

			<div class="row">
				<div class="span12 well">
					{modulelist zone=bottom}
				</div>
			</div>
		</div>

		{include file='footer.tpl'}
		{if isset($prefs.socialnetworks_user_firstlogin) && $prefs.socialnetworks_user_firstlogin == 'y'}
			{include file='tiki-socialnetworks_firstlogin_launcher.tpl'}
		{/if}

		{if $prefs.site_google_analytics_account}
			{wikiplugin _name=googleanalytics account=$prefs.site_google_analytics_account}{/wikiplugin}
		{/if}
		{if $prefs.feature_endbody_code}
			{eval var=$prefs.feature_endbody_code}
		{/if}
		{interactivetranslation}
		<!-- Put JS at the end -->
		{if $headerlib}
			{$headerlib->output_js_config()}
			{$headerlib->output_js_files()}
			{$headerlib->output_js()}
		{/if}
	</body>
</html>
{if !empty($smarty.request.show_smarty_debug)}
	{debug}
{/if}
