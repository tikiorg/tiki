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
		
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
<div id="fixedwidth" class="fixedwidth"> {* enables fixed-width layouts *}
	<table width="100%" height="100%" cellpadding="0" cellspacing="0" id="main">
	<tr>
		<td id="main-header" colspan="5">
			<header class="clearfix header" id="header"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
				<div class="wrapper">
					<div class="content clearfix modules" id="top_modules">
						{section name=homeix loop=$top_modules}
							{$top_modules[homeix].data}
						{/section}
					</div>
				</div>
			</header>
		</td>
	</tr>
	<tr>
		<td class="topbar_modules_container" colspan="5">
		{*	<div class="wrapper">*}
				<div class="topbar_modules_container">
					<div class="wrapper">
						<div class="content clearfix modules" id="topbar_modules">
							{section name=homeix loop=$topbar_modules}
								{$topbar_modules[homeix].data}
							{/section}
						</div>
					</div>
				</div>
		{*	</div> *}			
		</td>
	</tr>
	<tr>
		<td id="leftborder">
			<img src="styles/milkyway/borderlspacer.gif" alt=" " />
		</td>
		{if $prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
    	<td id="leftcolumn" valign="top" {if $prefs.feature_left_column eq 'user'}
		style="display:{if isset($cookie.show_leftcolumn) and $cookie.show_leftcolumn ne 'y'}none{else}table-cell;_display:block{/if};"{/if}>
			<aside id="col2"{if $prefs.feature_left_column eq 'user'} style="display:{if isset($cookie.show_col2) and $cookie.show_col2 ne 'y'} none{elseif isset($ie6)} block{else} table-cell{/if};"{/if}{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
											<div id="left_modules" class="content modules">
												{section name=homeix loop=$left_modules}
													{$left_modules[homeix].data}
												{/section}
											</div>
										</aside>
		</td>
		{/if}
    	<td id="centercolumn" valign="top">
		{/if}
		<div id="col1">
			{if $smarty.session.fullscreen neq 'y'}
			<div id="tiki-center" {*id needed for ajax editpage link*} class="clearfix content">
      		{if $prefs.feature_left_column eq 'user' or $prefs.feature_right_column eq 'user'}
        	<div id="showhide_columns">
      			{if $prefs.feature_left_column eq 'user' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
				<div style="text-align:left;float:left;"><a class="flip" href="#" onClick="toggleCols('leftcolumn','table-cell'); return false">{icon _name=oleftcol _id="oleftcol" class="colflip" alt="[{tr}Show/Hide Left Menus{/tr}]"}</a></div>
				{/if}
				{if $prefs.feature_right_column eq 'user'&& $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
				<div style="text-align:right;float:right;"><a class="flip" href="#" onClick="toggleCols('rightcolumn','table-cell'); return false">{icon _name=orightcol _id="orightcol" class="colflip" alt="[{tr}Show/Hide Right Menus{/tr}]"}</a>
				</div>
				{/if}
        		<br clear="all" />
			</div>
			{/if}
			{/if}
			{if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && $pagetop_modules|@count > 0)}
											<div class="content clearfix modules" id="pagetop_modules">
												{section name=homeix loop=$pagetop_modules}
													{$pagetop_modules[homeix].data}
												{/section}
											</div>
										{/if}
										{if $section neq 'share' && $prefs.feature_share eq 'y' && $tiki_p_share eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
											<div class="share">
												<a title="{tr}Share this page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>
											</div>
										{/if}
										{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
											<div class="tellafriend">
												<a title="{tr}Email this page{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
											</div>
										{/if}
										
											{if $display_msg}
												{remarksbox type="note" title="{tr}Notice{/tr}"}{$display_msg|escape}{/remarksbox}
											{/if}
											<div id="role_main">
												{$mid_data}  {* You can modify mid_data using tiki-show_page.tpl *}
											</div>
											{if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && $pagebottom_modules|@count > 0)}
												<div class="content clearfix modules" id="pagebottom_modules">
													{section name=homeix loop=$pagebottom_modules}
														{$pagebottom_modules[homeix].data}
													{/section}
												</div>
											{/if}
											{show_help}
										</div>{* end #tiki-center *}
		{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
		</td>
		{if $prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
		<td id="rightcolumn" valign="top" {if $prefs.feature_right_column eq 'user'} 
			style="display:{if isset($cookie.show_rightcolumn) and $cookie.show_rightcolumn ne 'y'}none{else}table-cell;_display:block{/if};" {/if}>
			<aside class="clearfix" id="col3"{if $prefs.feature_right_column eq 'user'} style="display:{if isset($cookie.show_col3) and $cookie.show_col3 ne 'y'} none{elseif isset($ie6)} block{else} table-cell{/if};"{/if}{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
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
		</td>
		{/if}
    	<td id="rightborder"><img src="styles/milkyway/borderrspacer.gif" alt=" " /></td>
	</tr>
	{if $prefs.module_zones_bottom eq 'fixed' or ($prefs.module_zones_bottom ne 'n' && $bottom_modules|@count > 0)}
	<tr class="footer">
		<td colspan="5" id="footer_td">
			<footer id="footer">
								<div class="footer_liner">
									<div class="fixedwidth footerbgtrap">
										<div id="bottom_modules" class="content modules"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
											{section name=homeix loop=$bottom_modules}
												{$bottom_modules[homeix].data}
											{/section}
										</div>
									</div>
								</div>
							</footer>{* -- END of footer -- *}
		</td>
	</tr>
{/if}
</table>
{/if}
{include file='footer.tpl'}

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