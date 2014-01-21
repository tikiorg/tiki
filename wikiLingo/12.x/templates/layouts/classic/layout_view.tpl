{* $Id$ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
	<head>
		{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

		<ul class="jumplinks" style="position:absolute;top:-9000px;left:-9000px;z-index:9;">
			<li><a href="#tiki-center" title="{tr}Jump to Content{/tr}">{tr}Jump to Content{/tr}</a></li>
		</ul>
		{$cookie_consent_html}
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
		{if $prefs.feature_tikitests eq 'y' and !empty($tikitest_state) and $tikitest_state neq 0}
			{include file='tiki-tests_topbar.tpl'}
		{/if}

		{if $prefs.feature_ajax eq 'y'}
			{include file='tiki-ajax_header.tpl'}
		{/if}
		
		<div id="fixedwidth" class="fixedwidth"> {* enables fixed-width layouts *}
			{if $prefs.feature_layoutshadows eq 'y'}<div id="main-shadow">{eval var=$prefs.main_shadow_start}{/if}
			<div id="main">
				{if ($prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y') and ($prefs.layout_section ne 'y' or $prefs.feature_top_bar ne 'n')}
					{if $prefs.module_zones_top eq 'fixed' or ($prefs.module_zones_top ne 'n' && ! zone_is_empty('top'))}
						{if $prefs.feature_layoutshadows eq 'y'}<div id="header-shadow">{eval var=$prefs.header_shadow_start}{/if}
							<div class="header_outer">
								<div class="header_container">
									<div class="fixedwidth header_fixedwidth">
										<header class="clearfix header" id="header"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
											{modulelist zone=top}
										</header>
									</div>	
								</div>
							</div>
						{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.header_shadow_end}</div>{/if}
					{/if}
				{/if}
				<div class="middle_outer">
					{if $prefs.feature_layoutshadows eq 'y'}<div id="middle-shadow">{eval var=$prefs.middle_shadow_start}{/if}
						<div class="clearfix fixedwidth middle" id="middle">
							{modulelist zone=topbar}
							<div class="clearfix {if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}nofullscreen{else}fullscreen{/if}" id="c1c2">
								<div class="clearfix" id="wrapper">
									<div id="col1" class="{if $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column ne 'n' && ! zone_is_empty('left') && $show_columns.left_modules ne 'n')}marginleft{/if}{if  $prefs.feature_right_column eq 'fixed' or ($prefs.feature_right_column ne 'n' && ! zone_is_empty('right') && $show_columns.right_modules ne 'n')} marginright{/if}"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
									{if $prefs.feature_layoutshadows eq 'y'}<div id="tiki-center-shadow">{eval var=$prefs.center_shadow_start}{/if}
										<div id="tiki-center" {*id needed for ajax editpage link*} class="clearfix content">
										{if ($prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y')}
											{if $prefs.feature_left_column eq 'user' or $prefs.feature_right_column eq 'user'}
												<div class="clearfix" id="showhide_columns">
													{if  $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column eq 'user' && ! zone_is_empty('left') > 0 && $show_columns.left_modules ne 'n')}
														<div style="text-align:left;float:left;" id="showhide_left_column">
															<a class="flip" title="{tr}Show/Hide Left Column{/tr}" href="#" onclick="toggleCols('col2','left'); return false">{icon _name=oleftcol _id="oleftcol" class="colflip" alt="[{tr}Show/Hide Left Column{/tr}]"}</a>
														</div>
													{/if}
													{if  $prefs.feature_right_column eq 'fixed' or ($prefs.feature_right_column eq 'user'&& ! zone_is_empty('right') && $show_columns.right_modules ne 'n')}
														<div class="clearfix" style="text-align:right;float:right" id="showhide_right_column">
															<a class="flip" title="{tr}Show/Hide Right Column{/tr}" href="#" onclick="toggleCols('col3','right'); return false">{icon _name=orightcol _id="orightcol" class="colflip" alt="[{tr}Show/Hide Right Column{/tr}]"}</a>
														</div>
													{/if}
													<br style="clear:both" />
												</div>
											{/if}
										{/if}
										{if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
											{modulelist zone=pagetop}
										{/if}
										{if (isset($section) && $section neq 'share') && $prefs.feature_share eq 'y' && $tiki_p_share eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
											<div class="share">
												<a title="{tr}Share this page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>
											</div>
										{/if}
										{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
											<div class="tellafriend">
												<a title="{tr}Email this page{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
											</div>
										{/if}
											{error_report}
											{if $display_msg}
												{remarksbox type="note" title="{tr}Notice{/tr}"}{$display_msg|escape}{/remarksbox}
											{/if}
											<div id="role_main">
												{block name=title}{/block}
												{block name=content}{/block}
												{block name=show_content}{/block}{* Help separate the page content from the whole page. Must be defined at root to work. AB *}
											</div>
											{if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
												{modulelist zone=pagebottom}
											{/if}
											{show_help}
										</div>{* end #tiki-center *}
										{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.center_shadow_end}</div>{/if}
									</div>
								</div>{* end #wrapper *}

								{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
									{if  $prefs.feature_left_column eq 'fixed' or ($prefs.feature_left_column ne 'n' && ! zone_is_empty('left') && $show_columns.left_modules ne 'n')}
										<div id="col2"{if $prefs.feature_left_column eq 'user'} style="display:{if isset($cookie.show_col2) and $cookie.show_col2 ne 'y'} none{elseif isset($ie6)} block{else} table-cell{/if};"{/if}{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
											{modulelist zone=left class="content modules"}
										</div>
									{/if}
								{/if}
							</div>{* -- END of #c1c2 -- *}
							{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
								{if  $prefs.feature_right_column eq 'fixed' or ($prefs.feature_right_column ne 'n' && ! zone_is_empty('right') && $show_columns.right_modules ne 'n') or (isset($module_pref_errors) and $module_pref_errors)}
									<div class="clearfix" id="col3"{if $prefs.feature_right_column eq 'user'} style="display:{if isset($cookie.show_col3) and $cookie.show_col3 ne 'y'} none{elseif isset($ie6)} block{else} table-cell{/if};"{/if}{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
										{block name=zone_right}
										{modulelist zone=right class="content modules"}
										{if $module_pref_errors}
											<div class="content modules">
												{remarksbox type="warning" title="{tr}Module errors{/tr}"}
													{tr}The following modules could not be loaded{/tr}
													<form method="post" action="tiki-admin.php">
														{foreach from=$module_pref_errors key=index item=pref_error}
															<p>{$pref_error.mod_name}:</p>
															{preference name=$pref_error.pref_name}
														{/foreach}
														<div class="submit">
															<input type="submit" value="{tr}Change{/tr}"/>
														</div>
													</form>
												{/remarksbox}
											</div>
										{/if}
										{/block}
									</div>
									<br style="clear:both" />
								{/if}
							{/if}
							<!--[if IE 7]><br style="clear:both; height: 0" /><![endif]-->
						</div>{* -- END of #middle -- *}
					{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.middle_shadow_end}</div>{/if}
				</div>{* end .middle_outer *}
				{if ($prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y') and ($prefs.layout_section ne 'y' or $prefs.feature_bot_bar ne 'n')}
					{if $prefs.module_zones_bottom eq 'fixed' or ($prefs.module_zones_bottom ne 'n' && ! zone_is_empty('bottom'))}
						{if $prefs.feature_layoutshadows eq 'y'}<div id="footer-shadow">{eval var=$prefs.footer_shadow_start}{/if}
							<footer class="footer" id="footer">
								<div class="footer_liner">
									<div class="fixedwidth footerbgtrap">
										{modulelist zone=bottom class="content modules" bidi=y}
									</div>
								</div>
							</footer>{* -- END of footer -- *}
						{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.footer_shadow_end}</div>{/if}
					{/if}
				{/if}
			</div>{* -- END of main -- *}{if $prefs.feature_layoutshadows eq 'y'}{eval var=$prefs.main_shadow_end}</div>{/if}
		</div> {* -- END of fixedwidth -- *}

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
