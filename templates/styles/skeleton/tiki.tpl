{* $Id$ *}<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
	<head>
		{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>
		<ul class="jumplinks" style="position:absolute;top:-9000px;left:-9000px;z-index:9;">
			<li><a href="#tiki-center">{tr}Jump to Content{/tr}</a></li>
		</ul>
		{if $prefs.feature_ajax eq 'y'}
			{include file='tiki-ajax_header.tpl'}
		{/if}
		<div id="fixedwidth"> {* enables fixed-width layouts *}
			<div id="main">
				<div class="clearfix" id="header"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
					{* Site header section *}
					<div class="clearfix" id="siteheader">
						{include file='tiki-site_header.tpl'}
					</div>
				</div>
				<div class="clearfix" id="middle">
					<div class="clearfix {if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}nofullscreen{else}fullscreen{/if}" id="c1c2">
						<div class="clearfix" id="wrapper">
							<div id="col1" class="{if $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n')}marginleft{/if}{if  $prefs.feature_left_column eq 'fixed' or ($prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n')} marginright{/if}"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
								<div id="tiki-center" {*id needed for ajax editpage link*} class="clearfix content">
									{if $prefs.feature_custom_center_column_header}{* Content comes from Look and Feel admin  *}
										<div id="custom_center_column_header">
											{eval var=$prefs.feature_custom_center_column_header}
										</div>
									{/if}
									{if $display_msg}
										{remarksbox type="note" title="{tr}Notice{/tr}"}
											{$display_msg|escape}
										{/remarksbox}
									{/if}
									<div id="role_main">
										{$mid_data}  {* You can modify mid_data using tiki-show_page.tpl *}
									</div>
									{show_help}
								</div>
							</div>
						</div>
						<hr class="hidden" />{* for semantic separation of center and side columns *}
						{if  $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n')}
							<div id="col2"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
								<h2 class="hidden">Sidebar</h2>
								<div class="content">
									{section name=homeix loop=$left_modules}
										{$left_modules[homeix].data}
									{/section}
								</div>
							</div>
						{/if}
					</div>{* -- END of c1c2 -- *}
					{if  $prefs.feature_left_column eq 'fixed' or ($prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n')}
						<div class="clearfix" id="col3"{if $prefs.feature_right_column eq 'user'} style="display:{if isset($cookie.show_rightcolumn) and $cookie.show_rightcolumn ne 'y'} none{elseif isset($ie6)} block{else} table-cell{/if};"{/if}{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
							<h2 class="hidden">Sidebar</h2>
							<div class="content">
								{section name=homeix loop=$right_modules}
									{$right_modules[homeix].data}
								{/section}
							</div>
						</div>
						<br style="clear:both" />
					{/if}
					<!--[if IE 7]><br style="clear:both; height: 0" /><![endif]-->
				</div>{* -- END of middle -- *}
				{if $prefs.feature_bot_bar eq 'y'}
					<div id="footer">
						<div class="footerbgtrap">
							<div class="content"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
								{include file='tiki-bot_bar.tpl'}
							</div>
						</div>
					</div>{* -- END of footer -- *}
				{/if}
			</div>{* -- END of main -- *}
		</div> {* -- END of fixedwidth -- *}
	{include file='footer.tpl'}
	{if $prefs.feature_endbody_code}{*this code must be added just before </body>: needed by google analytics *}
		{eval var=$prefs.feature_endbody_code}
	{/if}
	{interactivetranslation}
	<!-- Put JS at the end -->
		{if $headerlib}
			{$headerlib->output_js_files()}
			{$headerlib->output_js()}
		{/if}
	</body>
</html>
