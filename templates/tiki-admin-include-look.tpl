{* $Id$ *}

<form action="tiki-admin.php?page=look"  id="look" name="look" onreset="return(confirm('{tr}Cancel Edit{/tr}'))"  class="admin" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
		<input type="reset" name="looksetupreset" value="{tr}Reset{/tr}" />
	</div>

	{tabset name="admin_look"}
		{tab name="{tr}Theme{/tr}"}
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="general-theme">{tr}Theme{/tr}:</label>
					<select name="site_style" id="general-theme">
						{section name=ix loop=$styles}
							<option value="{$styles[ix]|escape}"{if $a_style eq $styles[ix]} selected="selected"{/if}>{$styles[ix]}</option>
						{/section}
					</select>
					{if $prefs.feature_help eq 'y'}
						{help url="Themes" desc="{tr}Themes{/tr}"}
					{/if}
					{if $prefs.javascript_enabled eq 'n' or $prefs.feature_jquery eq 'n'}
						<input type="submit" name="changestyle" value="{tr}Go{/tr}" />
					{/if}
				</div>
			</div>

			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="general-theme-options">{tr}Theme options{/tr}:</label>
					<select name="site_style_option" id="general-theme-options" {if !$style_options}disabled="disabled"{/if}>
						{if !$style_options}
							<option value="">{tr}None{/tr}</option>
						{/if}
						{section name=ix loop=$style_options}
							<option value="{$style_options[ix]|escape}"{if $prefs.style_option eq $style_options[ix]} selected="selected"{/if}>{$style_options[ix]}</option>
						{/section}
					</select>	
				</div>
				{if $prefs.change_theme eq 'y' and ($user_prefs.theme neq '' and $prefs.site_style neq $user_prefs.theme) or ($prefs.style neq '' and $prefs.site_style neq $prefs.style)}
					{remarksbox type="warning" title="{tr}Admin{/tr}"}{tr}The "users can change theme" feature will override the theme displayed.{/tr}{/remarksbox}
				{/if}
				
				{if $prefs.site_style != $a_style}
					{remarksbox type="note" title="{tr}Note{/tr}}{tr}Theme not saved yet - click "Apply"{/tr}{/remarksbox}
				{/if}	
			</div>
							
			{if isset($thumbfile)}
				<div class="adminoptionboxchild">
					<div id="style_thumb_div">
						<img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="style_thumb" />
					</div>
				</div>
			{/if}							

			{preference name=change_theme}
			<div class="adminoptionboxchild" id="change_theme_childcontainer">
				{tr}Restrict available themes{/tr}
				<br />
				{tr}Available styles:{/tr}
				<select name="available_styles[]" multiple="multiple" size="5">
					<option value=''>{tr}All{/tr}</option>
					{section name=ix loop=$styles}
						<option value="{$styles[ix]|escape}"{if $prefs.available_styles|count gt 0 and in_array($styles[ix], $prefs.available_styles)} selected="selected"{/if}>
							{$styles[ix]}
						</option>
					{/section}
				</select>
			</div>

			{preference name=useGroupTheme}
			
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="general-slideshows">{tr}Slideshow theme{/tr}:</label> 
					<select name="slide_style" id="general-slideshows">
						{section name=ix loop=$slide_styles}
							<option value="{$slide_styles[ix]|escape}"{if $prefs.slide_style eq $slide_styles[ix]} selected="selected"{/if}>{$slide_styles[ix]}</option>
						{/section}
					</select>
				</div>
			</div>

			{preference name=feature_editcss}
			<div class="adminoptionboxchild" id="feature_editcss_childcontainer">
				{if $tiki_p_create_css eq 'y'}
					{button _text="{tr}Edit CSS{/tr}" href="tiki-edit_css.php"}
				{/if}
			</div>

			{preference name=feature_theme_control}
			<div class="adminoptionboxchild" id="feature_theme_control_childcontainer">
				{button _text="{tr}Theme Control{/tr}" href="tiki-theme_control.php"}

			</div>

			{preference name=feature_view_tpl}
			<div class="adminoptionboxchild" id="feature_view_tpl_childcontainer">
				{button href="tiki-edit_templates.php" _text="{tr}View Templates{/tr}" }
			</div>

			{preference name=feature_edit_templates}
			<div class="adminoptionboxchild" id="feature_edit_templates_childcontainer">
				{button href="tiki-edit_templates.php" _text="{tr}Edit Templates{/tr}" }
			</div>
			{preference name=log_tpl}
		{/tab}
		
		{tab name="{tr}General Layout options{/tr}"}
			{* --- General Layout options --- *}
			{* --- Customize HTML Head Content --- *}
			{preference name=feature_custom_html_head_content}
			
			{* --- Customize Site Header --- *}
			{preference name=feature_sitemycode}
			<div class="adminoptionboxchild" id="feature_sitemycode_childcontainer">
				{icon _id=information}
				<em>{tr}The Custom Site Header will display for the Admin only. Select <strong>Publish</strong> to display the content for </em>all<em> users.{/tr}</em>
				{preference name=sitemycode}
				{preference name=sitemycode_publish}
			</div>

		{* --- Customize Site Logo and Site Title--- *}
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
		{preference name=feature_sitesearch}
		{preference name=feature_site_login}
		{* --- Top Bar --- *}
		{preference name=feature_top_bar}
		<div class="adminoptionboxchild" id="feature_top_bar_childcontainer">
			{preference name=feature_sitemenu}
			<div class="adminoptionboxchild" id="feature_sitemenu_childcontainer">
				{preference name=feature_topbar_id_menu}
			</div>
			{preference name=feature_topbar_version}
			{preference name=feature_topbar_debug}
			{preference name=feature_topbar_custom_code}
		</div>
		
		{preference name=feature_custom_center_column_header}
		{preference name=feature_left_column}
		{preference name=feature_Right_column}
		
		{* --- Site Breadcrumbs --- *}
		{preference name=feature_breadcrumbs}
		<div class="adminoptionboxchild" id="feature_breadcrumbs_childcontainer">
			{preference name=feature_siteloclabel}
			{preference name=feature_siteloc}
			{preference name=feature_sitetitle}
			{preference name=feature_sitedesc}
		</div>
		
		{preference name=feature_bot_logo}
		<div class="adminoptionboxchild" id="feature_bot_logo_childcontainer">
			{preference name=bot_logo_code}
		</div>

		{preference name=feature_endbody_code}
		
		{preference name=feature_bot_bar}
		<div class="adminoptionboxchild" id="feature_bot_bar_childcontainer">
			{preference name=feature_bot_bar_icons}
			{preference name=feature_bot_bar_debug}
			{preference name=feature_bot_bar_rss}
			{preference name=feature_bot_bar_power_by_tw}
		</div>

		{* --- Site Report Bar --- *}
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
		{* --- Shadow layer --- *}
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
		{* --- UI Effects (JQuery) --- *}
		<div class="adminoptionbox">	
			<fieldset class="admin">
				<legend>
					<a href="#"><span>{tr}JQuery plugins and add-ons{/tr}</span></a>
				</legend>	
				{if $prefs.feature_jquery eq 'n'}
					{remarksbox type="warning" title="{tr}Warning{/tr}"}
						{tr}Requires jquery feature{/tr}</em>{icon _id="arrow_right" href="tiki-admin.php?page=features"}{/remarksbox}
				{/if}

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="feature_jquery_tooltips" name="feature_jquery_tooltips" {if $prefs.feature_jquery_tooltips eq 'y'}checked="checked"{/if}/>
					</div>
					<div class="adminoptionlabel">
						<label for="feature_jquery_tooltips">{tr}Tooltips{/tr}</label>
						{if $prefs.feature_help eq 'y'} 
							{help url="JQuery#Tooltips" desc="{tr}JQuery Tooltips: Customisable help tips{/tr}"}
						{/if}
					</div>
				</div>
				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="feature_jquery_autocomplete" name="feature_jquery_autocomplete" {if $prefs.feature_jquery_autocomplete eq 'y'}checked="checked"{/if}/>
					</div>
					<div class="adminoptionlabel">
						<label for="feature_jquery_autocomplete">{tr}Autocomplete{/tr}</label>
						{if $prefs.feature_help eq 'y'}
							{help url="JQuery#Autocomplete" desc="{tr}JQuery Autocomplete{/tr}"}
						{/if}
					</div>
				</div>
				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="feature_jquery_superfish" name="feature_jquery_superfish" {if $prefs.feature_jquery_superfish eq 'y'}checked="checked"{/if}/>
					</div>
					<div class="adminoptionlabel">
						<label for="feature_jquery_superfish">{tr}Superfish{/tr}</label>
						{if $prefs.feature_help eq 'y'}
							{help url="JQuery#Superfish" desc="{tr}JQuery Superfish (effects on CSS menus){/tr}"}
						{/if}
					</div>
				</div>
				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="feature_jquery_reflection" name="feature_jquery_reflection" {if $prefs.feature_jquery_reflection eq 'y'}checked="checked"{/if}/>
					</div>
					<div class="adminoptionlabel">
						<label for="feature_jquery_reflection">{tr}Reflection{/tr}</label>
						{if $prefs.feature_help eq 'y'}
							{help url="JQuery#Reflection" desc="{tr}JQuery Reflection (reflection effect on images){/tr}"}
						{/if}
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="feature_jquery_ui" name="feature_jquery_ui" {if $prefs.feature_jquery_ui eq 'y'}checked="checked"{/if}/>
					</div>
					<div class="adminoptionlabel">
						<label for="feature_jquery_ui">{tr}JQuery UI{/tr}</label>
						{if $prefs.feature_help eq 'y'}
							{help url="JQuery#UI" desc="{tr}JQuery UI: More JQuery functionality{/tr}"}
						{/if}
					</div>
				</div>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="feature_jquery_ui_theme">{tr}JQuery UI Theme{/tr}: </label>
						<select name="feature_jquery_ui_theme" id="feature_jquery_ui_theme">
							<option value="black-tie" {if $prefs.feature_jquery_ui_theme eq 'black-tie'}selected="selected"{/if}>black-tie</option>
							<option value="blitzer" {if $prefs.feature_jquery_ui_theme eq 'blitzer'}selected="selected"{/if}>blitzer</option>
							<option value="cupertino" {if $prefs.feature_jquery_ui_theme eq 'cupertino'}selected="selected"{/if}>cupertino</option>
							<option value="dot-luv" {if $prefs.feature_jquery_ui_theme eq 'dot-luv'}selected="selected"{/if}>dot-luv</option>
							<option value="excite-bike" {if $prefs.feature_jquery_ui_theme eq 'excite-bike'}selected="selected"{/if}>excite-bike</option>
							<option value="hot-sneaks" {if $prefs.feature_jquery_ui_theme eq 'hot-sneaks'}selected="selected"{/if}>hot-sneaks</option>
							<option value="humanity" {if $prefs.feature_jquery_ui_theme eq 'humanity'}selected="selected"{/if}>humanity</option>
							<option value="mint-choc" {if $prefs.feature_jquery_ui_theme eq 'mint-choc'}selected="selected"{/if}>mint-choc</option>
							<option value="redmond" {if $prefs.feature_jquery_ui_theme eq 'redmond'}selected="selected"{/if}>redmond</option>
							<option value="smoothness" {if $prefs.feature_jquery_ui_theme eq 'smoothness'}selected="selected"{/if}>smoothness</option>
							<option value="south-street" {if $prefs.feature_jquery_ui_theme eq 'south-street'}selected="selected"{/if}>south-street</option>
							<option value="start" {if $prefs.feature_jquery_ui_theme eq 'start'}selected="selected"{/if}>start</option>
							<option value="swanky-purse" {if $prefs.feature_jquery_ui_theme eq 'swanky-purse'}selected="selected"{/if}>swanky-purse</option>
							<option value="trontastic" {if $prefs.feature_jquery_ui_theme eq 'trontastic'}selected="selected"{/if}>trontastic</option>
							<option value="ui-darkness" {if $prefs.feature_jquery_ui_theme eq 'ui-darkness'}selected="selected"{/if}>ui-darkness</option>
							<option value="ui-lightness" {if $prefs.feature_jquery_ui_theme eq 'ui-lightness'}selected="selected"{/if}>ui-lightness</option>
							<option value="vader" {if $prefs.feature_jquery_ui_theme eq 'vader'}selected="selected"{/if}>vader</option>
						</select>
						{if $prefs.feature_help eq 'y'}
							{help url="JQuery#UI" desc="{tr}JQuery UI Theme: Themes for look and feel of JQuery UI widgets{/tr}"}
						{/if}
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						{icon _id=information} <em>{tr}For future use{/tr}:</em>
						<div class="adminoptionboxchild">	
							<div class="adminoptionbox">
								<div class="adminoption">
									<input type="checkbox" id="feature_jquery_cycle" name="feature_jquery_cycle" {if $prefs.feature_jquery_cycle eq 'y'}checked="checked"{/if}/>
								</div>
								<div class="adminoptionlabel">
									<label for="feature_jquery_cycle">{tr}Cycle{/tr} ({tr}slideshow{/tr})</label>
									{if $prefs.feature_help eq 'y'} 
										{help url="JQuery#Cycle" desc="{tr}JQuery Cycle (slideshow){/tr}"}
									{/if}
								</div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoption">
									<input type="checkbox" id="feature_jquery_sheet" name="feature_jquery_sheet" {if $prefs.feature_jquery_sheet eq 'y'}checked="checked"{/if}/>
								</div>
								<div class="adminoptionlabel">
									<label for="feature_jquery_sheet">{tr}JQuery Sheet{/tr}</label>
									{if $prefs.feature_help eq 'y'}
										{help url="JQuery#Sheet" desc="{tr}JQuery Spreadsheet{/tr}"}
									{/if}
								</div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoption">
									<input type="checkbox" id="feature_jquery_tablesorter" name="feature_jquery_tablesorter" {if $prefs.feature_jquery_tablesorter eq 'y'}checked="checked"{/if}/>
								</div>
								<div class="adminoptionlabel">
									<label for="feature_jquery_tablesorter">{tr}JQuery Sortable Tables{/tr}</label>
									{if $prefs.feature_help eq 'y'} 
										{help url="JQuery#TableSorter" desc="{tr}JQuery Sortable Tables{/tr}"}
									{/if}
								</div>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="adminoptionbox">
			<fieldset class="admin">
				<legend>
					<a><span>{tr}Standard UI effects{/tr}</span></a>
				</legend>
				<div class="adminoptionbox">
					<div class="adminoption"></div>
						<div class="adminoptionlabel">
							<label for="jquery_effect">{tr}Effect for modules{/tr}:</label> 
							<select name="jquery_effect" id="jquery_effect">
								<option value="none" {if $prefs.jquery_effect_tabs eq 'none'}selected="selected"{/if}>{tr}None{/tr}</option>
								<option value="" {if $prefs.jquery_effect eq ''}selected="selected"{/if}>{tr}Default{/tr}</option>
								<option value="slide" {if $prefs.jquery_effect eq 'slide'}selected="selected"{/if}>{tr}Slide{/tr}</option>
								<option value="fade" {if $prefs.jquery_effect eq 'fade'}selected="selected"{/if}>{tr}Fade{/tr}</option>
								{if $prefs.feature_jquery_ui eq 'y'}
									<option value="blind_ui" {if $prefs.jquery_effect eq 'blind_ui'}selected="selected"{/if}>{tr}Blind (UI){/tr}</option>
									<option value="clip_ui" {if $prefs.jquery_effect eq 'clip_ui'}selected="selected"{/if}>{tr}Clip (UI){/tr}</option>
									<option value="drop_ui" {if $prefs.jquery_effect eq 'drop_ui'}selected="selected"{/if}>{tr}Drop (UI){/tr}</option>
									<option value="explode_ui" {if $prefs.jquery_effect eq 'explode_ui'}selected="selected"{/if}>{tr}Explode (UI){/tr}</option>
									<option value="fold_ui" {if $prefs.jquery_effect eq 'fold_ui'}selected="selected"{/if}>{tr}Fold (UI){/tr}</option>
									<option value="puff_ui" {if $prefs.jquery_effect eq 'puff_ui'}selected="selected"{/if}>{tr}Puff (UI){/tr}</option>
									<option value="slide_ui" {if $prefs.jquery_effect eq 'slide_ui'}selected="selected"{/if}>{tr}Slide (UI){/tr}</option>
								{/if}
							</select>
							{if $prefs.feature_help eq 'y'} 
								{help url="JQuery#Effects" desc="{tr}Main JQuery effect{/tr}"}
							{/if}
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoption"></div>
						<div class="adminoptionlabel">
							<label for="jquery_effect_speed">{tr}Speed{/tr} :</label> 
							<select name="jquery_effect_speed" id="jquery_effect_speed">
								<option value="fast" {if $prefs.jquery_effect_speed eq 'fast'}selected="selected"{/if}>{tr}Fast{/tr}</option>
								<option value="normal" {if $prefs.jquery_effect_speed eq 'normal'}selected="selected"{/if}>{tr}Normal{/tr}</option>
								<option value="slow" {if $prefs.jquery_effect_speed eq 'slow'}selected="selected"{/if}>{tr}Slow{/tr}</option>
							</select>
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoption"></div>
						<div class="adminoptionlabel">
							<label for="jquery_effect_direction">{tr}Direction{/tr} :</label> 
							<select name="jquery_effect_direction" id="jquery_effect_direction">
								<option value="vertical" {if $prefs.jquery_effect_direction eq 'vertical'}selected="selected"{/if}>{tr}Vertical{/tr}</option>
								<option value="horizontal" {if $prefs.jquery_effect_direction eq 'horizontal'}selected="selected"{/if}>{tr}Horizontal{/tr}</option>
								<option value="left" {if $prefs.jquery_effect_direction eq 'left'}selected="selected"{/if}>{tr}Left{/tr}</option>
								<option value="right" {if $prefs.jquery_effect_direction eq '"right"'}selected="selected"{/if}>{tr}Right{/tr}</option>
								<option value="up" {if $prefs.jquery_effect_direction eq 'up'}selected="selected"{/if}>{tr}Up{/tr}</option>
								<option value="down" {if $prefs.jquery_effect_direction eq 'down'}selected="selected"{/if}>{tr}Down{/tr}</option>
							</select>
						</div>
					</div>
				</fieldset>
			</div>
			
			<div class="adminoptionbox">			
				<fieldset class="admin">
					<legend><a><span>{tr}Tab UI effects{/tr}</span></a></legend>
					<div class="adminoptionbox">
						<div class="adminoption"></div>
						<div class="adminoptionlabel">
							<label for="jquery_effect_tabs">{tr}Effect for tabs{/tr}:</label> 
							<select name="jquery_effect_tabs" id="jquery_effect_tabs">
								<option value="none" {if $prefs.jquery_effect_tabs eq 'none'}selected="selected"{/if}>{tr}None{/tr}</option>
								<option value="normal" {if $prefs.jquery_effect_tabs eq 'normal'}selected="selected"{/if}>{tr}Normal{/tr}</option>
								<option value="slide" {if $prefs.jquery_effect_tabs eq 'slide'}selected="selected"{/if}>{tr}Slide{/tr}</option>
								<option value="fade" {if $prefs.jquery_effect_tabs eq 'fade'}selected="selected"{/if}>{tr}Fade{/tr}</option>
								{if $prefs.feature_jquery_ui eq 'y'}
									<option value="blind_ui" {if $prefs.jquery_effect_tabs eq 'blind_ui'}selected="selected"{/if}>{tr}Blind (UI){/tr}</option>
									<option value="clip_ui" {if $prefs.jquery_effect_tabs eq 'clip_ui'}selected="selected"{/if}>{tr}Clip (UI){/tr}</option>
									<option value="drop_ui" {if $prefs.jquery_effect_tabs eq 'drop_ui'}selected="selected"{/if}>{tr}Drop (UI){/tr}</option>
									<option value="explode_ui" {if $prefs.jquery_effect_tabs eq 'explode_ui'}selected="selected"{/if}>{tr}Explode (UI){/tr}</option>
									<option value="fold_ui" {if $prefs.jquery_effect_tabs eq 'fold_ui'}selected="selected"{/if}>{tr}Fold (UI){/tr}</option>
									<option value="puff_ui" {if $prefs.jquery_effect_tabs eq 'puff_ui'}selected="selected"{/if}>{tr}Puff (UI){/tr}</option>
									<option value="slide_ui" {if $prefs.jquery_effect_tabs eq 'slide_ui'}selected="selected"{/if}>{tr}Slide (UI){/tr}</option>
								{/if}
							</select>
							{if $prefs.feature_help eq 'y'} 
								{help url="JQuery#Effects" desc="{tr}JQuery effect for tabs{/tr}"}
							{/if}
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label for="jquery_effect_tabs_speed">{tr}Speed{/tr}</label>: 
							<select name="jquery_effect_tabs_speed" id="jquery_effect_tabs_speed">
								<option value="fast" {if $prefs.jquery_effect_tabs_speed eq 'fast'}selected="selected"{/if}>{tr}Fast{/tr}</option>
								<option value="normal" {if $prefs.jquery_effect_tabs_speed eq 'normal'}selected="selected"{/if}>{tr}Normal{/tr}</option>
								<option value="slow" {if $prefs.jquery_effect_tabs_speed eq 'slow'}selected="selected"{/if}>{tr}Slow{/tr}</option>
							</select>
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label for="jquery_effect_tabs_direction">{tr}Direction{/tr}: </label>
							<select name="jquery_effect_tabs_direction" id="jquery_effect_tabs_direction">
								<option value="vertical" {if $prefs.jquery_effect_tabs_direction eq 'vertical'}selected="selected"{/if}>{tr}Vertical{/tr}</option>
								<option value="horizontal" {if $prefs.jquery_tabs_effect_direction eq 'horizontal'}selected="selected"{/if}>{tr}Horizontal{/tr}</option>
								<option value="left" {if $prefs.jquery_effect_tabs_direction eq 'left'}selected="selected"{/if}>{tr}Left{/tr}</option>
								<option value="right" {if $prefs.jquery_effect_tabs_direction eq '"right"'}selected="selected"{/if}>{tr}Right{/tr}</option>
								<option value="up" {if $prefs.jquery_effect_tabs_direction eq 'up'}selected="selected"{/if}>{tr}Up{/tr}</option>
								<option value="down" {if $prefs.jquery_effect_tabs_direction eq 'down'}selected="selected"{/if}>{tr}Down{/tr}</option>
							</select>
						</div>
					</div>
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

	{tab name="{tr}Miscellaneous{/tr}"}
		{* --- Other --- *}
		<div class="adminoptionbox">
			<div class="adminoption">	
				<input type="checkbox" name="feature_tabs" id="general-feature_tabs" {if $prefs.feature_tabs eq 'y'}checked="checked"{/if}/>
			</div>
			<div class="adminoptionlabel">
				<label for="general-feature_tabs">{tr}Use Tabs{/tr}</label>
			</div>
		</div>
		<div class="adminoptionbox">
			<div class="adminoption">
				<input type="checkbox" name="layout_section" id="general-layout_section" {if $prefs.layout_section eq 'y'}checked="checked"{/if}/>
			</div>
			<div class="adminoptionlabel">
				<label for="general-layout_section">{tr}Layout per section{/tr}</label>
				{if $prefs.layout_section eq 'y'}
					<br />
					{button _text="{tr}Admin layout per section{/tr}" href="tiki-admin_layout.php"}
				{/if}
			</div>
		</div>

		<div class="adminoptionbox">
			<div class="adminoption">
				<input type="checkbox" name="feature_iepngfix" id="feature_iepngfix"{if $prefs.feature_iepngfix eq 'y'} checked="checked"{/if} onclick="flip('iepngfix');" />
			</div>
			<div class="adminoptionlabel">
				<label for="feature_iepngfix">{tr}Correct PNG images alpha transparency in IE6 (experimental){/tr}</label>
				<div id="iepngfix" class="adminoptionboxchild" style="display:{if $prefs.feature_iepngfix eq 'y'}block{else}none{/if};">
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label class="above" for="iepngfix_selectors">{tr}CSS selectors to be fixed{/tr}:</label>
							<input id="iepngfix_selectors" type="text" name="iepngfix_selectors" size="32" value="{$prefs.iepngfix_selectors}" />
							<br />
							<em>{tr}Separate multiple elements with a comma (&nbsp;,&nbsp;){/tr}.</em>
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label class="above" for="iepngfix_elements">{tr}HTMLDomElements to be fixed{/tr}:</label> 
							<input id="iepngfix_elements" type="text" name="iepngfix_elements" size="32" value="{$prefs.iepngfix_elements}" />
							<br />
							<em>{tr}Separate multiple elements with a comma (&nbsp;,&nbsp;){/tr}.</em>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="adminoptionbox">
			<fieldset>
				<legend>{tr}Favicon{/tr}</legend>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="site_favicon">{tr}Favicon icon file name:{/tr}</label> 
						<input type="text" name="site_favicon" id="site_favicon" value="{$prefs.site_favicon}" size="12" maxlength="32" />
					</div>
				</div>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="site_favicon_type">{tr}Favicon icon MIME type:{/tr}</label> 
						<select name="site_favicon_type" id="site_favicon_type">
							<option value="image/png" {if $prefs.site_favicon_type eq 'image/png'}selected="selected"{/if}>{tr}image/png{/tr}</option>
							<option value="image/bmp" {if $prefs.site_favicon_type eq 'image/bmp'}selected="selected"{/if}>{tr}image/bmp{/tr}</option>
							<option value="image/x-icon" {if $prefs.site_favicon_type eq 'image/x-icon'}selected="selected"{/if}>{tr}image/x-icon{/tr}</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>

		<div class="adminoptionbox">
			<fieldset class="admin">
				<legend>
					<span>{tr}Context Menus{/tr}</span>
				</legend>
				<em>{tr}Currently used in File Galleries only{/tr}.</em>
				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="use_context_menu_icon" name="use_context_menu_icon" {if $prefs.use_context_menu_icon eq 'y'}checked="checked"{/if} />
					</div>
					<div class="adminoptionlabel">
						<label for="use_context_menu_icon">{tr}Use context menus for actions (icons){/tr}</label>
					</div>
				</div>
				
				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="use_context_menu_text" name="use_context_menu_text" {if $prefs.use_context_menu_text eq 'y'}checked="checked"{/if}/>
					</div>
					<div class="adminoptionlabel">
						<label for="use_context_menu_text">{tr}Use context menus for actions (text){/tr}</label>.
					</div>
				</div>
			</fieldset>
		</div>

		<fieldset>
			<legend>{tr}Separators{/tr}</legend>
			{preference name=site_crumb_seper}
			<div class="adminoptionboxchild">
				<em>{tr}Examples{/tr}: &nbsp; &raquo; &nbsp; / &nbsp; &gt; &nbsp; : &nbsp; -> &nbsp; &#8594;</em>
			</div>

			{preference name=site_nav_seper}
			<div class="adminoptionboxchild">
				<em>{tr}Examples{/tr}: &nbsp; | &nbsp; / &nbsp; &brvbar; &nbsp; :</em>
			</div>
		</fieldset>
	{/tab}
{/tabset}

<div class="input_submit_container clear" style="text-align: center">
	<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
</div>
</form>
