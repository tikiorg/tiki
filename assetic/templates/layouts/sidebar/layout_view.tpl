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
		
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span2">
					{modulelist zone=left}
					{modulelist zone=right}
				</div>
				<div class="span10">
					<div class="container-fluid">
						<div class="row-fluid">
							<div class="span12">
								{modulelist zone=topbar}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12">
								{block name=mid_content}{/block}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12 well">
								{modulelist zone=bottom}
								{include file='footer.tpl'}
							</div>
						</div>
				</div>
			</div>

		</div>

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
