{* $Id$ *}

<form action="tiki-admin.php?page=look" id="look" name="look" onreset="return(confirm("{tr}Cancel Edit{/tr}"))" class="admin" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
		<input type="reset" name="looksetupreset" value="{tr}Reset{/tr}" />
	</div>

	{tabset name="admin_look"}
		{tab name="{tr}Theme{/tr}"}

			{preference name=style default=$prefs.site_style}
			<div class="adminoptionbox">
				{if $prefs.javascript_enabled eq 'n' or $prefs.feature_jquery eq 'n'}
					<input type="submit" name="changestyle" value="{tr}Go{/tr}" />
				{/if}
			</div>

			<div class="adminoptionbox">
				{preference name=style_option default=$prefs.site_style_option}
				{if $prefs.change_theme eq 'y' and ($user_prefs.theme neq '' and $prefs.site_style neq $user_prefs.theme) or ($prefs.style neq '' and $prefs.site_style neq $prefs.style)}
					{remarksbox type="warning" title="{tr}Admin{/tr}"}{tr}The "users can change theme" feature will override the theme displayed.{/tr}{/remarksbox}
				{/if}
				
				{if $prefs.site_style != $a_style}
					{remarksbox type="note" title="{tr}Note{/tr}"}{tr}Theme not saved yet - click "Apply"{/tr}{/remarksbox}
				{/if}	
			</div>

			{if isset($thumbfile)}
				<div class="adminoptionboxchild">
					<div id="style_thumb_div">
						<img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="style_thumb" />
					</div>
				</div>
			{/if}							

			{preference name=feature_fixed_width}
			<div class="adminoptionboxchild" id="feature_fixed_width_childcontainer">
				{preference name=layout_fixed_width}
			</div>

			{preference name=change_theme}
			<div class="adminoptionboxchild" id="change_theme_childcontainer">
				{tr}Restrict available themes{/tr}
				{preference name=available_styles}
			</div>

			{preference name=useGroupTheme}
			{preference name=slide_style}
			{preference name=feature_editcss}
			{if $prefs.feature_editcss eq 'y'}
				<div class="adminoptionboxchild">
					{if $tiki_p_create_css eq 'y'}
						{button _text="{tr}Edit CSS{/tr}" href="tiki-edit_css.php"}
					{/if}
				</div>
			{/if}

			{preference name=feature_theme_control}
			{if $prefs.feature_theme_control eq 'y'}
				<div class="adminoptionboxchild">
					{button _text="{tr}Theme Control{/tr}" href="tiki-theme_control.php"}
				</div>
			{/if}

			{preference name=feature_view_tpl}
			{if $prefs.feature_view_tpl eq 'y'}
				<div class="adminoptionboxchild">
					{button href="tiki-edit_templates.php" _text="{tr}View Templates{/tr}" }
				</div>
			{/if}

			{preference name=feature_edit_templates}
			{if $prefs.feature_edit_templates eq 'y'}
				<div class="adminoptionboxchild">
					{button href="tiki-edit_templates.php" _text="{tr}Edit Templates{/tr}" }
				</div>
			{/if}

			{preference name=log_tpl}
			{preference name=smarty_compilation}
			{preference name=categories_used_in_tpl}
		{/tab}
		
		{tab name="{tr}General Layout options{/tr}"}
			{preference name=feature_html_head_base_tag}
			{preference name=feature_custom_html_head_content}

			{preference name=feature_sitelogo}
			<div class="adminoptionboxchild" id="feature_sitelogo_childcontainer">
				<fieldset>
					<legend>{tr}Logo{/tr}</legend>
					{preference name=sitelogo_src}
					{preference name=sitelogo_bgcolor}
					{preference name=sitelogo_bgstyle}
					{preference name=sitelogo_align}
					{preference name=sitelogo_title}
					{preference name=sitelogo_alt}
				</fieldset>
					
				<fieldset>
					<legend>{tr}Title{/tr}</legend>
					{preference name=sitetitle}
					{preference name=sitesubtitle}
				</fieldset>
			</div>
			{preference name=feature_top_bar}
		
			{preference name=feature_left_column}
			{preference name=feature_right_column}
		
			{preference name=module_zones_top}
			{preference name=module_zones_topbar}
			{preference name=module_zones_pagetop}
			{preference name=module_zones_pagebottom}
			{preference name=module_zones_bottom}
		
			{preference name=feature_breadcrumbs}
			<div class="adminoptionboxchild" id="feature_breadcrumbs_childcontainer">
				{preference name=feature_siteloclabel}
				{preference name=feature_siteloc}
				{preference name=feature_sitetitle}
				{preference name=feature_sitedesc}
			</div>
		
			{preference name=feature_endbody_code}
		
			{preference name=feature_bot_bar}
			<div class="adminoptionboxchild" id="feature_bot_bar_childcontainer">
			</div>

			<div class="adminoptionbox">
				<fieldset>
					<legend>{tr}Site Report Bar{/tr}</legend>
					{preference name=feature_site_report}
					{preference name=feature_site_report_email}
					{preference name=feature_site_send_link}
				</fieldset>
			</div>
		{/tab}

		{tab name="{tr}Shadow layer{/tr}"}
			{preference name=feature_layoutshadows}
			<div class="adminoptionboxchild" id="feature_layoutshadows_childcontainer">
				{preference name=main_shadow_start}
				{preference name=main_shadow_end}

				{preference name=header_shadow_start}
				{preference name=header_shadow_end}

				{preference name=middle_shadow_start}
				{preference name=middle_shadow_end}

				{preference name=center_shadow_start}
				{preference name=center_shadow_end}

				{preference name=footer_shadow_start}
				{preference name=footer_shadow_end}

				{preference name=box_shadow_start}
				{preference name=box_shadow_end}
			</div>
		{/tab}

		{tab name="{tr}Pagination links{/tr}"}
			{preference name=user_selector_threshold}
			{preference name=maxRecords}
			{preference name=nextprev_pagination}
			{preference name=direct_pagination}
			<div class="adminoptionboxchild" id="direct_pagination_childcontainer">
				{preference name=direct_pagination_max_middle_links}
				{preference name=direct_pagination_max_ending_links}
			</div>

			{preference name=pagination_firstlast}
			{preference name=pagination_fastmove_links}
			{preference name=pagination_hide_if_one_page}
			{preference name=pagination_icons}
		{/tab}
		
		{tab name="{tr}UI Effects{/tr}"}
			<div class="adminoptionbox">
				<fieldset class="admin">
					<legend>{tr}Standard UI effects{/tr}</legend>
					{preference name=jquery_effect}
					{preference name=jquery_effect_speed}
					{preference name=jquery_effect_direction}
				</fieldset>
			</div>
			
				<div class="adminoptionbox">			
					<fieldset class="admin">
						<legend>{tr}Tab UI effects{/tr}</legend>
						{preference name=jquery_effect_tabs}
						{preference name=jquery_effect_tabs_speed}
						{preference name=jquery_effect_tabs_direction}
				</fieldset>
			</div>

			<fieldset>
				<legend>{tr}Other{/tr}</legend>
				<div class="admin featurelist">
					{preference name=feature_shadowbox}
					{preference name=feature_jscalendar}
				</div>
			</fieldset>		
		{/tab}

		{tab name="{tr}Custom CSS{/tr}"}
			<fieldset>
				<legend>{tr}Theme Generator{/tr}</legend>
				{preference name="feature_themegenerator"}
				<div class="adminoptionboxchild" id="feature_themegenerator_childcontainer">
					<div class="adminoptionbox">			
						{preference name="themegenerator_theme"}
						<div  class="adminoptionboxchild" id="feature_themegenerator_childcontainer">
							
							<input type="text" name="tg_edit_theme_name" value="{$tg_edit_theme_name|escape}"{if !empty($prefs.themegenerator_theme)} style="display:none;"{/if} />
							<input type="submit" name="tg_new_theme" value="{tr}New{/tr}"{if !empty($prefs.themegenerator_theme)} style="display:none;"{/if} />
							<input type="submit" name="tg_delete_theme" value="{tr}Delete{/tr}"{if empty($prefs.themegenerator_theme)} style="display:none;"{/if} />
							{jq}$("select[name=themegenerator_theme]").change(function(){
	if ($(this)[0].selectedIndex === 0) {
		$("input[name=tg_edit_theme_name]").keyup(function(e){
			if (e.keyCode === 13 && $(this).val()) {
				$("input[name=tg_new_theme]").click();
			}
		}).show();
		$("input[name=tg_new_theme]").show();
		$("input[name=tg_delete_theme]").hide();
	}
							}).change();{/jq}
							{if $prefs.feature_jquery_ui eq "y" and $prefs.feature_ajax eq "y"}
								{button _text="{tr}Open editor{/tr}" _class="tgFloatDialog" href="#"}
							{/if}
						</div>
					</div>
					<div class="adminoptionbox">
						{if $prefs.feature_jquery_ui neq "y" or $prefs.feature_ajax neq "y"}
							<div id="themegenerator_container">
								{include file="themegen.tpl"}
								<div class="input_submit_container clear" style="text-align: center">
									<input type="submit" name="tg_preview" value="{tr}Preview Theme{/tr}">
								</div>
							</div>
							{jq}initThemeGenDialog();{/jq}
						{/if}
					</div>
				</div>
			</fieldset>
				
			<fieldset>
				<legend>{tr}Custom CSS{/tr}</legend>
				<div class="adminoptionboxchild">
					{self_link _onclick="toggle_brosho();return false;" _ajax="n"}{icon _id="bricks"}{tr}Experimental: CSS assistant (work in progress - click the x to remove){/tr}{/self_link}
				</div>
				{$headerlib->add_jsfile('lib/jquery/brosho/jquery.brosho.js')}
				{$headerlib->add_jsfile('lib/jquery_tiki/brosho/tiki_brosho.js')}
				{preference name="header_custom_css"}
			</fieldset>
		{/tab}

		{tab name="{tr}Miscellaneous{/tr}"}
			{preference name=feature_tabs}
			{preference name=layout_section}
			{if $prefs.layout_section eq 'y'}
				{button _text="{tr}Admin layout per section{/tr}" href="tiki-admin_layout.php"}
			{/if}

			{preference name=feature_iepngfix}
			<div class="adminoptionboxchild" id="feature_iepngfix_childcontainer">
				{preference name=iepngfix_selectors}
				{preference name=iepngfix_elements}
			</div>

			<div class="adminoptionbox">
				<fieldset>
					<legend>{tr}Favicon{/tr}</legend>
					{preference name=site_favicon}
					{preference name=site_favicon_type}
				</fieldset>
			</div>

			<div class="adminoptionbox">
				<fieldset class="admin">
					<legend>{tr}Context Menus{/tr} (<em>{tr}Currently used in File Galleries only{/tr}.</em>)</legend>
					{preference name=use_context_menu_icon}
					{preference name=use_context_menu_text}
				</fieldset>
			</div>

			<fieldset>
				<legend>{tr}Separators{/tr}</legend>
				{preference name=site_crumb_seper}
				<div class="adminoptionboxchild">
					<em>{tr}Examples:{/tr} &nbsp; &raquo; &nbsp; / &nbsp; &gt; &nbsp; : &nbsp; -> &nbsp; &#8594;</em>
				</div>

				{preference name=site_nav_seper}
				<div class="adminoptionboxchild">
					<em>{tr}Examples:{/tr} &nbsp; | &nbsp; / &nbsp; &brvbar; &nbsp; :</em>
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Custom Code{/tr}</legend>
				{preference name="header_custom_js"}
			</fieldset>
		{/tab}
	{/tabset}

	<div class="input_submit_container clear" style="text-align: center">
		<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
	</div>
</form>
