{* $Id$ *}
<form action="tiki-admin.php?page=look" id="look" name="look" class="form-horizontal labelColumns" class="admin" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="clearfix margin-bottom-md">
		{if $prefs.feature_theme_control eq y}
			{button _text="{tr}Theme Control{/tr}" href="tiki-theme_control.php" _class="btn-sm tikihelp" }
		{/if}
		{if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}
			{button _text="{tr}Edit CSS{/tr}" _class="btn-sm" href="tiki-edit_css.php"}
		{/if}
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm" name="looksetup" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
		</div>
	</div>
	{tabset name="admin_look"}
		{tab name="{tr}Theme{/tr}"}
			<h2>{tr}Theme{/tr}</h2>
			<div class="row">
				<div class="col-md-2 col-md-push-10">
					<div class="thumbnail">
						{if $thumbfile}
							<img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="theme_thumb">
						{else}
							<span>{icon name="image"}</span>
						{/if}
					</div>
				</div>
				<div class="col-md-9 col-md-pull-1 adminoptionbox">
					{preference name=theme}
					{preference name=theme_option}
				</div>
			</div>
			<div class="adminoptionbox theme_childcontainer custom_url">
				{preference name=theme_custom_url}
			</div>
			<div class="adminoptionbox">
				{preference name=theme_admin}
				{preference name=theme_option_admin}
			</div>
			{preference name=site_layout}
			{preference name=site_layout_admin}
			{preference name=site_layout_per_object}
			{preference name=theme_iconset}
			{if $prefs.javascript_enabled eq 'n' or $prefs.feature_jquery eq 'n'}
				<input type="submit" class="btn btn-default btn-sm" name="changestyle" value="{tr}Go{/tr}" />
			{/if}
			<div class="adminoptionbox">
				{if $prefs.feature_jquery_ui eq 'y'}
					{preference name=feature_jquery_ui_theme}
				{/if}
			</div>
			{preference name=change_theme}
			<div class="adminoptionboxchild" id="change_theme_childcontainer">
				{preference name=available_themes}
			</div>
			{preference name=feature_fixed_width}
			<div class="adminoptionboxchild" id="feature_fixed_width_childcontainer">
				{preference name=layout_fixed_width}
			</div>
			{preference name=useGroupTheme}
			{preference name=feature_theme_control}
			<div class="adminoptionboxchild" id="feature_theme_control_childcontainer">
				{preference name=feature_theme_control_savesession}
				{preference name=feature_theme_control_parentcategory}
				{preference name=feature_theme_control_autocategorize}
			</div>
		{/tab}
		{tab name="{tr}General Layout{/tr}"}
			<h2>{tr}General Layout{/tr}</h2>
			{preference name=feature_sitelogo}
			<div class="adminoptionboxchild" id="feature_sitelogo_childcontainer">
				<fieldset>
					<legend>{tr}Logo{/tr}</legend>
					{preference name=sitelogo_src}
					{preference name=sitelogo_icon}
					{preference name=sitelogo_bgcolor}
					{preference name=sitelogo_title}
					{preference name=sitelogo_alt}
				</fieldset>
				<fieldset>
					<legend>{tr}Title{/tr}</legend>
					{preference name=sitetitle}
					{preference name=sitesubtitle}
				</fieldset>
			</div>
			<div class="adminoptionbox">
				<fieldset>
					<legend>{tr}Module zone visibility{/tr}</legend>
					{if !$smarty.get.Zone_options}
						{remarksbox type="tip" title="{tr}Hint{/tr}"}
							Module zone visibility options may not be supported anymore from Tiki 13+, but you can still access them in case you are upgrading from an earlier version. <a href="tiki-admin.php?page=look&Zone_options=y#contentadmin_look-2" class="alert-link">Show module visibility options</a>
						{/remarksbox}
					{else}
						{preference name=module_zones_top}
						{preference name=module_zones_topbar}
						{preference name=module_zones_pagetop}
						{preference name=feature_left_column}
						{preference name=feature_right_column}
						{preference name=module_zones_pagebottom}
						{preference name=module_zones_bottom}
					{/if}
					{preference name=module_file}
					{preference name=module_zone_available_extra}
				</fieldset>
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
		{if $prefs.site_layout eq 'classic'}
			{tab name="{tr}Shadow layer{/tr}"}
				<h2>{tr}Shadow layer{/tr}</h2>
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
		{/if}
		{tab name="{tr}Pagination{/tr}"}
			<h2>{tr}Pagination{/tr}</h2>
			{preference name=user_selector_threshold}
			{preference name=maxRecords}
			{preference name=tiki_object_selector_threshold}
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
			<h2>{tr}UI Effects{/tr}</h2>
			<div class="adminoptionbox">
				<fieldset class="table">
					<legend>{tr}Standard UI effects{/tr}</legend>
					{preference name=jquery_effect}
					{preference name=jquery_effect_speed}
					{preference name=jquery_effect_direction}
				</fieldset>
			</div>
			<div class="adminoptionbox">
				<fieldset class="table">
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
					<div class="adminoptionboxchild" id="feature_shadowbox_childcontainer">
						{preference name=jquery_colorbox_theme}
					</div>
					{preference name=feature_jscalendar}
					{preference name=feature_hidden_links}
					{preference name=feature_equal_height_rows_js}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}Customization{/tr}"}
			<h2>{tr}Customization{/tr}</h2>
			<fieldset>
				<legend>{tr}Custom Codes{/tr}</legend>
				{preference name="header_custom_css" syntax="css"}
				{preference name="header_custom_less" syntax="css"}
				{preference name=feature_custom_html_head_content syntax="htmlmixed"}
				{preference name=feature_endbody_code syntax="tiki"}
				{preference name=site_google_analytics_account}
				{preference name="header_custom_js" syntax="javascript"}
				{preference name="layout_add_body_group_class"}
				{preference name=categories_add_class_to_body_tag}
			</fieldset>
			<fieldset>
				<legend>{tr}Editing{/tr}</legend>
				{preference name=feature_editcss}
				{preference name=feature_view_tpl}
				{if $prefs.feature_view_tpl eq 'y'}
					<div class="adminoptionboxchild">
						{button href="tiki-edit_templates.php" _text="{tr}View Templates{/tr}"}
					</div>
				{/if}
				{preference name=feature_edit_templates}
				{if $prefs.feature_edit_templates eq 'y'}
					<div class="adminoptionboxchild">
						{button href="tiki-edit_templates.php" _text="{tr}Edit Templates{/tr}"}
					</div>
				{/if}
			</fieldset>
		{/tab}
		{tab name="{tr}Miscellaneous{/tr}"}
			<h2>{tr}Miscellaneous{/tr}</h2>
			{preference name=feature_tabs}
			<div class="adminoptionboxchild" id="feature_tabs_childcontainer">
				{preference name=layout_tabs_optional}
			</div>
			{preference name=feature_iepngfix}
			<div class="adminoptionboxchild" id="feature_iepngfix_childcontainer">
				{preference name=iepngfix_selectors}
				{preference name=iepngfix_elements}
			</div>
			{preference name=image_responsive_class}
			<div class="adminoptionbox">
				<fieldset>
					<legend>{tr}Favicon{/tr}</legend>
					{preference name=site_favicon}
					{preference name=site_favicon_type}
				</fieldset>
			</div>
			<div class="adminoptionbox">
				<fieldset class="table">
					<legend>{tr}Context Menus{/tr} (<small>{tr}Currently used in File Galleries only{/tr}.</small>)</legend>
					{preference name=use_context_menu_icon}
					{preference name=use_context_menu_text}
				</fieldset>
			</div>
			<fieldset>
				<legend>{tr}Separators{/tr}</legend>
				{preference name=site_crumb_seper}
				<div class="adminoptionboxchild clearfix">
					<span class="col-md-8 col-md-push-4 help-block">{tr}Examples:{/tr} &nbsp; &raquo; &nbsp; / &nbsp; &gt; &nbsp; : &nbsp; -> &nbsp; &#8594;</span>
				</div>
				{preference name=site_nav_seper}
				<div class="adminoptionboxchild clearfix">
					<span class="col-md-8 col-md-push-4 help-block">{tr}Examples:{/tr} &nbsp; | &nbsp; / &nbsp; &brvbar; &nbsp; :</span>
				</div>
			</fieldset>
			{preference name=log_tpl}
			{preference name=smarty_compilation}
			{preference name=smarty_cache_perms}
			{preference name=categories_used_in_tpl}
			{preference name=feature_html_head_base_tag}
		{/tab}
	{/tabset}
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm tips" name="looksetup" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
	</div>
</form>
