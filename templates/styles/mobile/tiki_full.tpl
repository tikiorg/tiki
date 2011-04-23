<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
		<title></title>
{*include file='header.tpl'*}
	</head>
	<body{*html_body_attributes*}>

{* Display a tiki page here without the side module zones or most JS includes
   for mobile ajax page loading *}
{if $prefs.feature_bidi eq 'y'}
<div dir="rtl">
{/if}
{*if $prefs.feature_ajax eq 'y'}
{include file='tiki-ajax_header.tpl'}
{/if*}
<div id="main" data-role="page">
	<div class="header_outer" data-role="header" data-theme="{$prefs.mobile_theme_header}">
		<div class="header_container">
			<div class="header_fixedwidth">
				<header class="header clearfix" id="header"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
					<div class="content clearfix modules" id="top_modules">
						{section name=homeix loop=$top_modules}
							{$top_modules[homeix].data}
						{/section}
					</div>
				</header>
			</div>
		</div>
	</div>
	<div class="middle_outer" data-role="content" data-theme="{$prefs.mobile_theme_content}">
		<div id="tiki-center">
			<div id="role_main">
				{$mid_data}
				{show_help}
			</div>
		</div>
	</div>
	<footer id="footer" data-role="footer" data-theme="{$prefs.mobile_theme_footer}">
		<div class="footer_liner">
			<div class="footerbgtrap">
				<div id="bottom_modules" class="content modules"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
					{section name=homeix loop=$bottom_modules}
						{$bottom_modules[homeix].data}
					{/section}
				</div>
			</div>
		</div>
	</footer>
	{* include for js to initialise $mid_data *}
	<div id="js_ajax_include" style="display:none;"></div>
</div>
			

{if $prefs.feature_bidi eq 'y'}
</div>
{/if}
{*include file='footer.tpl'}
<!-- Put JS at the end -->
{if $headerlib}
	{$headerlib->output_js_files()}
	{$headerlib->output_js()}
{/if*}
	</body>
</html>
