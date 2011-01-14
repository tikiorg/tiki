{* $Id$ *}<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>
		<ul class="jumplinks" style="position:absolute;top:-9000px;left:-9000px;z-index:9;">
			<li><a href="#tiki-center" title="{tr}Jump to Content{/tr}">{tr}Jump to Content{/tr}</a></li>
		</ul>

{if $prefs.feature_fullscreen eq 'y' and $filegals_manager eq '' and $print_page ne 'y'}
		<div id="fullscreenbutton">
	{if $smarty.session.fullscreen eq 'n'}
		{self_link fullscreen="y" _ajax='n' _icon=application_get _title="{tr}Fullscreen{/tr}"}{/self_link}
	{else}
		{self_link fullscreen="n" _ajax='n' _icon=application_put _title="{tr}Cancel Fullscreen{/tr}"}{/self_link}
	{/if}
		</div>
{/if}

{* TikiTest ToolBar *}
{if $prefs.feature_tikitests eq 'y' and $tikitest_state neq 0}
	{include file='tiki-tests_topbar.tpl'}
{/if}

{if $prefs.feature_ajax eq 'y'}
	{include file='tiki-ajax_header.tpl'}
{/if}
	<div id="fixedwidth"> {* enables fixed-width layouts *}
		{if $prefs.feature_layoutshadows eq 'y'}
			<div id="main-shadow">{eval var=$prefs.main_shadow_start}
		{/if}
	<div id="main">
		{if ($prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y') }
			{if $prefs.feature_layoutshadows eq 'y'}
				<div id="header-shadow">{eval var=$prefs.header_shadow_start}
			{/if}
					<header class="clearfix" id="header"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
						{* Site header section *}
						<div class="content clearfix modules" id="top_modules">
							{section name=homeix loop=$top_modules}
								{$top_modules[homeix].data}
							{/section}
						</div>
				{*<div class="clearfix" id="siteheader">
		{include file='tiki-site_header.tpl'}
				</div>TODO remove *}
			</header>{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.header_shadow_end}</div>{/if}
{/if}

			{if $prefs.feature_layoutshadows eq 'y'}<div id="middle-shadow">{eval var=$prefs.middle_shadow_start}{/if}<div class="clearfix" id="middle">
				<div class="clearfix {if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}nofullscreen{else}fullscreen{/if}" id="c1c2">
					<div class="clearfix" id="wrapper">
						<div id="col1" class="{if $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n')}marginleft{/if}{if  $prefs.feature_right_column eq 'fixed' or ($prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n')} marginright{/if}"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>

{if $smarty.session.fullscreen neq 'y'}
	{if $prefs.feature_left_column eq 'user' or $prefs.feature_right_column eq 'user'}
							<div class="clearfix" id="showhide_columns">
		{if  $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column eq 'user' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n')}
								<div style="text-align:left;float:left;">
									<a class="flip" title="{tr}Show/Hide Left Column{/tr}" href="#" onclick="toggleCols('col2','left'); return false">{icon _name=oleftcol _id="oleftcol" class="colflip" alt="[{tr}Show/Hide Left Column{/tr}]"}</a>
								</div>
		{/if}
		{if  $prefs.feature_right_column eq 'fixed' or ($prefs.feature_right_column eq 'user'&& $right_modules|@count > 0 && $show_columns.right_modules ne 'n')}
								<div class="clearfix" style="text-align:right;float:right">
									<a class="flip" title="{tr}Show/Hide Right Column{/tr}" href="#" onclick="toggleCols('col3','right'); return false">{icon _name=orightcol _id="orightcol" class="colflip" alt="[{tr}Show/Hide Right Column{/tr}]"}</a>
								</div>
		{/if}
								<br style="clear:both" />
							</div>
	{/if}
{/if}

{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
							<div class="share">
								<a title="{tr}Share this page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>
							</div>
{/if}
{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
							<div class="tellafriend">
								<a title="{tr}Email this page{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
							</div>
{/if}

							{if $prefs.feature_layoutshadows eq 'y'}<div id="tiki-center-shadow">{eval var=$prefs.center_shadow_start}{/if}<div id="tiki-center" {*id needed for ajax editpage link*} class="clearfix content">
							{if $prefs.feature_custom_center_column_header}{* Content comes from Look and Feel admin  *}
								<div id="custom_center_column_header">{eval var=$prefs.feature_custom_center_column_header}</div>
							{/if}
{if $display_msg}
	{remarksbox type="note" title="{tr}Notice{/tr}"}{$display_msg|escape}{/remarksbox}
{/if}
<div id="role_main">
{$mid_data}  {* You can modify mid_data using tiki-show_page.tpl *}
</div>
{show_help}
							</div>{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.center_shadow_end}</div>{/if}
						</div>
					</div>

{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
					<hr class="hidden" />{* for semantic separation of center and side columns *}
	{if  $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n')}
					<aside id="col2"{if $prefs.feature_left_column eq 'user'} style="display:{if isset($cookie.show_col2) and $cookie.show_col2 ne 'y'} none{elseif isset($ie6)} block{else} table-cell{/if};"{/if}{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
						<h2 class="hidden">Sidebar</h2>
						<div id="left_modules" class="content modules">
		{section name=homeix loop=$left_modules}
			{$left_modules[homeix].data}
		{/section}
						</div>
					</aside>
	{/if}
{/if}
				</div>{* -- END of c1c2 -- *}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
	{if  $prefs.feature_right_column eq 'fixed' or ($prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n')}
				<aside class="clearfix" id="col3"{if $prefs.feature_right_column eq 'user'} style="display:{if isset($cookie.show_col3) and $cookie.show_col3 ne 'y'} none{elseif isset($ie6)} block{else} table-cell{/if};"{/if}{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
					<h2 class="hidden">Sidebar</h2>
					<div id="right_modules" class="content modules">
		{if $module_pref_errors}
			{remarksbox type="warning" title="{tr}Module errors{/tr}"}
				{tr}The following modules could not be loaded{/tr}
				<p>
				{foreach from=$module_pref_errors key=index item=pref_error}
					<b>{$pref_error.mod_name}:</b><br />
					{tr}Preference was not set:{/tr} '{$pref_error.pref_name}'<br />
				{/foreach}
				</p>
			{/remarksbox}
		{/if}
		{section name=homeix loop=$right_modules}
			{$right_modules[homeix].data}
		{/section}
					</div>
				</aside>
				<br style="clear:both" />
	{/if}
{/if}
		<!--[if IE 7]><br style="clear:both; height: 0" /><![endif]-->
			</div>{* -- END of middle -- *}{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.middle_shadow_end}</div>{/if}

{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
	{if $prefs.feature_bot_bar eq 'y'}
			{if $prefs.feature_layoutshadows eq 'y'}<div id="footer-shadow">{eval var=$prefs.footer_shadow_start}{/if}<footer id="footer">
				<div class="footerbgtrap">
					<div id="bottom_modules" class="content modules"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
						{section name=homeix loop=$bottom_modules}
							{$bottom_modules[homeix].data}
						{/section}
						{include file='tiki-bot_bar.tpl'}
					</div>
				</div>
			</footer>{* -- END of footer -- *}{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.footer_shadow_end}</div>{/if}
	{/if}
{/if}

		</div>{* -- END of main -- *}{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.main_shadow_end}</div>{/if}
	</div> {* -- END of fixedwidth -- *}

{include file='footer.tpl'}

{if $prefs.socialnetworks_user_firstlogin == 'y'}
	{include file='tiki-socialnetworks_firstlogin_launcher.tpl'}
{/if}

{if $prefs.feature_endbody_code}{*this code must be added just before </body>: needed by google analytics *}
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
