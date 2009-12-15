{* $Id$ *}
	<form action="tiki-admin.php?page=look"  id="look" name="look" onreset="return(confirm('{tr}Cancel Edit{/tr}'))"  class="admin" method="post">
		<div class="heading input_submit_container" style="text-align: right">
			<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
			<input type="reset" name="looksetupreset" value="{tr}Reset{/tr}" />
		</div>

		{tabset name="admin_look"}
			{tab name="{tr}Theme{/tr}"}

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="general-theme">{tr}Theme{/tr}:</label>
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
	<div class="adminoptionlabel"><label for="general-theme-options">{tr}Theme options{/tr}:</label>
		<select name="site_style_option" id="general-theme-options" {if !$style_options}disabled="disabled"{/if}>
							{if !$style_options}<option value="">{tr}None{/tr}</option>{/if}
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
	<div id="style_thumb_div"><img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="style_thumb" /></div>
</div>
{/if}							

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="change_theme" name="change_theme" {if $prefs.change_theme eq 'y'}checked="checked"{/if} onclick="flip('restrictthemes');" /></div>
	<div class="adminoptionlabel"><label for="change_theme">{tr}Users can change theme{/tr}.</label>

<div class="adminoptionboxchild" id="restrictthemes" style="display:{if $prefs.change_theme eq 'y'}block{else}none{/if};">

	<div class="adminoptionlabel" id="select_available_styles" {if count($prefs.available_styles) > 0 and $prefs.available_styles[0] ne ''}style="display:none;"{else}style="display:block;"{/if}>
		<a class="button" href="javascript:show('available_styles');hide('select_available_styles');">{tr}Restrict available themes{/tr}</a>
     </div>

	<div id="available_styles" {if count($prefs.available_styles) == 0 or $prefs.available_styles[0] eq ''}style="display:none;"{else}style="display:block;"{/if}>
		{tr}Available styles:{/tr}<br />
		<select name="available_styles[]" multiple="multiple" size="5">
			<option value=''>{tr}All{/tr}</option>
			{section name=ix loop=$styles}
			<option value="{$styles[ix]|escape}"{if $prefs.available_styles|count gt 0 and in_array($styles[ix], $prefs.available_styles)} selected="selected"{/if}>
			{$styles[ix]}
			</option>
			{/section}
		</select>
	</div>

</div>
	
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="useGroupTheme" id="useGroupTheme" {if $prefs.useGroupTheme eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="useGroupTheme">{tr}Each group can have its theme{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="general-slideshows">{tr}Slideshow theme{/tr}:</label> 
	<select name="slide_style" id="general-slideshows">
							{section name=ix loop=$slide_styles}
								<option value="{$slide_styles[ix]|escape}"{if $prefs.slide_style eq $slide_styles[ix]} selected="selected"{/if}>{$slide_styles[ix]}</option>
							{/section}
							</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_editcss" name="feature_editcss" {if $prefs.feature_editcss eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_editcss">{tr}Edit CSS{/tr}</label>
	{if $prefs.feature_help eq 'y'}
	{help url="Edit+CSS" desc="{tr}Help{/tr}"}
	{/if}
{if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}{button _text="{tr}Edit CSS{/tr}" href="tiki-edit_css.php"}{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_theme_control" name="feature_theme_control" {if $prefs.feature_theme_control eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_theme_control">{tr}Theme Control{/tr}</label>
{if $prefs.feature_theme_control eq 'y'}{button _text="{tr}Theme Control{/tr}" href="tiki-theme_control.php"}{/if}	
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_view_tpl" name="feature_view_tpl" {if $prefs.feature_view_tpl eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_view_tpl">{tr}Tiki Template Viewing{/tr}</label> {if $prefs.feature_help eq 'y'}
	{help url="View+Templates" desc="{tr}Help{/tr}"}
	{/if}

	{if $prefs.feature_view_tpl eq 'y'}{button href="tiki-edit_templates.php" _text="{tr}View Templates{/tr}" }{/if}
	
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_edit_templates" name="feature_edit_templates" {if $prefs.feature_edit_templates eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_edit_templates">{tr}Edit Templates{/tr}</label> {help url="Edit+Templates" desc="{tr}Help{/tr}"}
	
{if $prefs.feature_edit_templates eq 'y'}
							{button href="tiki-edit_templates.php" _text="{tr}Edit Templates{/tr}" }
{/if}
	</div>
</div>
			{preference name=log_tpl}


	{/tab}

	{tab name="{tr}General Layout options{/tr}"}
	{* --- General Layout options --- *}
	
{* --- Customize HTML Head Content --- *}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_custom_html_head_content">{tr}Custom HTML &lt;head&gt; Content{/tr}:</label><br /><textarea id="feature_custom_html_head_content" name="feature_custom_html_head_content" rows="6" cols="40" style="width: 90%">{$prefs.feature_custom_html_head_content|escape}</textarea>
									<br />
									<small><em>{tr}Example{/tr}:</em> {literal}&#123;if $page eq 'Slideshow'&#125;&#123;literal&#125;&lt;style type="text/css"&gt;.slideshow { height: 232px; width: 232px; }&lt;/style&gt;&#123;/literal&#125;&#123;/if&#125;{/literal}</small></div>
</div>
{* --- Customize Site Header --- *}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_sitemycode" id="feature_sitemycode"{if $prefs.feature_sitemycode eq 'y'} checked="checked"{/if} onclick="flip('customsiteheader');" /></div>
	<div class="adminoptionlabel"><label for="feature_sitemycode">{tr}Custom Site Header{/tr}</label>

<div class="adminoptionboxchild" id="customsiteheader" style="display:{if $prefs.feature_sitemycode eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitemycode">{tr}Content{/tr}:</label><br /><textarea name="sitemycode" rows="6" cols="40" style="width: 90%" id="sitemycode">{$prefs.sitemycode|escape}</textarea>
									<br />
									<small><em>{tr}Example{/tr}</em>: 
										{literal}{if $user neq ''}{/literal}&lt;div align="right" style="float: right; font-size: 10px"&gt;{literal}{{/literal}tr}{tr}logged as{/tr}{literal}{/tr}{/literal}: {literal}{$user}{/literal}&lt;/div&gt;{literal}{/if}{/literal}
									</small></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" onclick="flip('adminonlynote');" name="sitemycode_publish" id="sitemycode_publish"{if $prefs.sitemycode_publish eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="sitemycode_publish">{tr}Publish{/tr}</label>
<div id="adminonlynote" style="display:{if $prefs.sitemycode_publish eq 'y'}none{else}block{/if};"><br />
	{icon _id=information} <em>{tr}The Custom Site Header will display for the Admin only. Select <strong>Publish</strong> to display the content for </em>all<em> users.{/tr}</em>
</div>	
	</div>
</div>

</div>
	
	</div>
</div>

{* --- Customize Site Logo and Site Title--- *}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" onclick="flip('sitelogoandtitle');" name="feature_sitelogo" id="feature_sitelogo"{if $prefs.feature_sitelogo eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_sitelogo">{tr}Site Logo and Title{/tr}</label>
	
<div class="adminoptionboxchild" id="sitelogoandtitle" style="display:{if $prefs.feature_sitelogo eq 'y'}block{else}none{/if};">
<fieldset>
<legend>{tr}Logo{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitelogo_src">{tr}Logo source (image path){/tr}:</label> <input type="text" name="sitelogo_src" id="sitelogo_src" value="{$prefs.sitelogo_src}" size="60" style="width: 90%" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitelogo_bgcolor">{tr}Logo background color{/tr}:</label> <input type="text" name="sitelogo_bgcolor" id="sitelogo_bgcolor" value="{$prefs.sitelogo_bgcolor}" size="15" maxlength="15" />
<br />
		<div style="font-size: smaller; margin-left: 20px">
			<em>{tr}Examples{/tr}:</em> 1) silver 2) #fff
		</div>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitelogo_bgstyle">{tr}Logo background style{/tr}:</label> <input type="text" name="sitelogo_bgstyle" id="sitelogo_bgstyle" value="{$prefs.sitelogo_bgstyle}" />
		<div style="font-size: smaller; margin-left: 20px">
			<em>{tr}Examples{/tr}:</em><br />1) silver url(myStyle/img.gif) repeat<br />2) padding: 30px 10px; background: #fff
		</div>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitelogo_align">{tr}Logo alignment{/tr}:</label> 
		<select name="sitelogo_align" id="sitelogo_align">
										<option value="left" {if $prefs.sitelogo_align eq 'left'}selected="selected"{/if}>{tr}Left{/tr}</option>
										<option value="center" {if $prefs.sitelogo_align eq 'center'}selected="selected"{/if}>{tr}Center{/tr}</option>
										<option value="right" {if $prefs.sitelogo_align eq 'right'}selected="selected"{/if}>{tr}Right{/tr}</option>
		</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitelogo_title">{tr}Logo title (on mouse over){/tr}:</label> <input type="text" name="sitelogo_title" id="sitelogo_title" value="{$prefs.sitelogo_title}" size="50" maxlength="200" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitelogo_alt">{tr}Alt. description (e.g. for text browsers){/tr}:</label> <input type="text" name="sitelogo_alt" id="sitelogo_alt" value="{$prefs.sitelogo_alt}" size="50" maxlength="200" /></div>
</div>
</fieldset>
<fieldset><legend>{tr}Title{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="_sitetitle">{tr}Site title{/tr}:</label> <input type="text" name="sitetitle" id="_sitetitle" value="{$prefs.sitetitle}" size="50" maxlength="200" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="_sitesubtitle">{tr}Subtitle{/tr}:</label> <input type="text" name="sitesubtitle" id="_sitesubtitle" value="{$prefs.sitesubtitle}" size="50" maxlength="200" /></div>
</div>
</fieldset>
</div>
	</div>
</div>
{* --- Site Search Bar --- *}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_sitesearch" id="feature_sitesearch"{if $prefs.feature_sitesearch eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_sitesearch">{tr}Search Bar{/tr}</label></div>
</div>
{* --- Site Login Bar --- *}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_site_login" id="feature_site_login"{if $prefs.feature_site_login eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_site_login">{tr}Login Bar{/tr}</label></div>
</div>
{* --- Top Bar --- *}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_top_bar" name="feature_top_bar" {if $prefs.feature_top_bar eq 'y'}checked="checked"{/if} onclick="flip('usetopbar');" /></div>
	<div class="adminoptionlabel"><label for="feature_top_bar">{tr}Top Bar{/tr}</label>

<div class="adminoptionboxchild" id="usetopbar" style="display:{if $prefs.feature_top_bar eq 'y'}block{else}none{/if};">	

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_sitemenu" id="feature_sitemenu"{if $prefs.feature_sitemenu eq 'y'} checked="checked"{/if} onclick="flip('sitemenuid');" /></div>
	<div class="adminoptionlabel"><label for="feature_sitemenu">{tr}Site menu bar{/tr}</label>
{if ($prefs.feature_phplayers ne 'y') or ($prefs.feature_cssmenus ne 'y')}<br />{icon _id=information} <em>{tr}Requires CSS Menus or PHPLayers{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.</em>{/if}

<div class="adminoptionboxchild" id="sitemenuid" style="display:{if $prefs.feature_sitemenu eq 'y'}block{else}none{/if};">
	<div class="adminoptionlabel"><label for="feature_topbar_id_menu">{tr}Menu ID{/tr}:</label> <input type="text" name="feature_topbar_id_menu" id="feature_topbar_id_menu" value="{$prefs.feature_topbar_id_menu}" size="6" maxlength="6" /></div>
</div>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_topbar_version" id="feature_topbar_version"{if $prefs.feature_topbar_version eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_topbar_version">{tr}Display current Tiki version{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_topbar_debug" id="feature_topbar_debug"{if $prefs.feature_topbar_debug eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_topbar_debug">{tr}Debugger Console{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_topbar_custom_code">{tr}Custom code{/tr}:</label><br /><textarea name="feature_topbar_custom_code" id="feature_topbar_custom_code" rows="6" cols="40" style="width: 90%">{$prefs.feature_topbar_custom_code}</textarea></div>
</div>

</div>

	</div>
</div>
{* --- Custom Center Column Header --- *}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_custom_center_column_header">{tr}Custom Center Column Header{/tr}</label>:<br /><textarea id="feature_custom_center_column_header" name="feature_custom_center_column_header" rows="6" cols="40" style="width: 90%">{$prefs.feature_custom_center_column_header|escape}</textarea><br />
	<small><em>{tr}Example{/tr}:</em> {literal}&#123;if $page eq 'Travel'&#125; &#123;banner zone=5&#125;&#123;/if&#125;{/literal}</small>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_left_column">{tr}Left column{/tr}:</label>
		<select name="feature_left_column" id="feature_left_column">
								<option value="y" {if $prefs.feature_left_column eq 'y'}selected="selected"{/if}>{tr}only if module{/tr}</option>
								<option value="fixed" {if $prefs.feature_left_column eq 'fixed'}selected="selected"{/if}>{tr}always{/tr}</option>
								<option value="user" {if $prefs.feature_left_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
								<option value="n" {if $prefs.feature_left_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
		</select>
		{if $prefs.feature_help eq 'y'} {help url="Users+Flip+Columns" desc="{tr}Users can Flip Columns{/tr}"}{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_right_column">{tr}Right column{/tr}</label>: 
		<select name="feature_right_column" id="feature_right_column">
								<option value="y" {if $prefs.feature_right_column eq 'y'}selected="selected"{/if}>{tr}only if module{/tr}</option>
								<option value="fixed" {if $prefs.feature_right_column eq 'fixed'}selected="selected"{/if}>{tr}always{/tr}</option>
								<option value="user" {if $prefs.feature_right_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
								<option value="n" {if $prefs.feature_right_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
		</select> 
		{if $prefs.feature_help eq 'y'} {help url="Users+Flip+Columns" desc="{tr}Users can Flip Columns{/tr}"}{/if}
	</div>
</div>
{* --- Site Breadcrumbs --- *}
				{preference name=feature_breadcrumbs}

<div class="adminoptionboxchild" id="feature_breadcrumbs_childcontainer">
				
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_siteloclabel" id="feature_siteloclabel"{if $prefs.feature_siteloclabel eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_siteloclabel">{tr}Prefix breadcrumbs with &quot;Location : &quot;{/tr} .</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_siteloc">{tr}Site location bar{/tr}:</label>
		<select name="feature_siteloc" id="feature_siteloc">
										<option value="y" {if $prefs.feature_siteloc eq 'y'}selected="selected"{/if}>{tr}Top of page{/tr}</option>
										<option value="page" {if $prefs.feature_siteloc eq 'page'}selected="selected"{/if}>{tr}Top of center column{/tr}</option>
										<option value="n" {if $prefs.feature_siteloc eq 'n'}selected="selected"{/if}>{tr}None{/tr}</option>
		</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_sitetitle">{tr}Larger font for{/tr}:</label> 
		<select name="feature_sitetitle" id="feature_sitetitle">
										<option value="y" {if $prefs.feature_sitetitle eq 'y'}selected="selected"{/if}>{tr}Entire location{/tr}</option>
										<option value="title" {if $prefs.feature_sitetitle eq 'title'}selected="selected"{/if}>{tr}Page name{/tr}</option>
										<option value="n" {if $prefs.feature_sitetitle eq 'n'}selected="selected"{/if}>{tr}None{/tr}</option>
		</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_sitedesc">{tr}Use page description:{/tr}</label>
		<select name="feature_sitedesc" id="feature_sitedesc">
										<option value="y" {if $prefs.feature_sitedesc eq 'y'}selected="selected"{/if}>{tr}Top of page{/tr}</option>
										<option value="page" {if $prefs.feature_sitedesc eq 'page'}selected="selected"{/if}>{tr}Top of center column{/tr}</option>
										<option value="n" {if $prefs.feature_sitedesc eq 'n'}selected="selected"{/if}>{tr}None{/tr}</option>
		</select>
{if $prefs.feature_wiki_description	ne 'y'}<br />{icon _id=information} <em>{tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=wiki" title="{tr}Features{/tr}">{tr}Enable now{/tr}.</a></em>{/if}
	</div>
</div>

</div>
	

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_bot_logo" id="feature_bot_logo"{if $prefs.feature_bot_logo eq 'y'} checked="checked"{/if} onclick="flip('usesitefooter');" /></div>
	<div class="adminoptionlabel"><label for="feature_bot_logo">{tr}Custom Site Footer{/tr}</label>

<div class="adminoptionboxchild" id="usesitefooter" style="display:{if $prefs.feature_bot_logo eq 'y'}block{else}none{/if};">	

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label>Content:<br /><textarea id="bot_logo_code" name="bot_logo_code" rows="6" cols="40" style="width: 90%">{$prefs.bot_logo_code|escape}</textarea></label><br /><small><em>{tr}Example{/tr}</em>:&lt;div style="text-align: center"&gt;&lt;small&gt;Powered by Tikiwiki&lt;/small&gt;&lt;/div&gt;</small></div>
</div>

</div>
	
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_endbody_code">{tr}Custom End of &lt;body&gt; Code{/tr}:</label><br />
	<textarea id="feature_endbody_code" name="feature_endbody_code" rows="6" cols="40" style="width: 90%">{$prefs.feature_endbody_code|escape}</textarea><br />
	<small><em>{tr}Example{/tr}: </em>{literal}{wiki}&#123;literal&#125;{GOOGLEANALYTICS(account=xxxx) /}&#123;/literal&#125;{/wiki}{/literal}</small>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_bot_bar" name="feature_bot_bar" {if $prefs.feature_bot_bar eq 'y'}checked="checked"{/if} onclick="flip('usebottombar');" /></div>
	<div class="adminoptionlabel"><label for="feature_bot_bar">{tr}Bottom bar{/tr}</label>

<div class="adminoptionboxchild" id="usebottombar" style="display:{if $prefs.feature_bot_bar eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_bot_bar_icons" name="feature_bot_bar_icons"	{if $prefs.feature_bot_bar_icons eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_bot_bar_icons">{tr}Bottom bar icons{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_bot_bar_debug" name="feature_bot_bar_debug" {if $prefs.feature_bot_bar_debug eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_bot_bar_debug">{tr}Bottom bar debug{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_bot_bar_rss" name="feature_bot_bar_rss" {if $prefs.feature_bot_bar_rss eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_bot_bar_rss">{tr}Bottom bar (RSS){/tr}</label></div>
</div>

{preference name=feature_bot_bar_power_by_tw}

</div>
	
	</div>
</div>

{* --- Site Report Bar --- *}
<div class="adminoptionbox">
<fieldset><legend>{tr}Site Report Bar{/tr}</legend>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_site_report" id="feature_site_report"{if $prefs.feature_site_report eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_site_report">{tr}Webmaster Report{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_site_report_email">{tr}Webmaster Email{/tr}:</label> <input type="text" name="feature_site_report_email" id="feature_site_report_email" value="{$prefs.feature_site_report_email}" />
	<br /><em>{tr}Leave <strong>blank</strong> to use the default sender email{/tr}</em>.
	{if empty($prefs.sender_email)} <span class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</span>{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_site_send_link" id="feature_site_send_link"{if $prefs.feature_site_send_link eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_site_send_link">{tr}Email this page{/tr}</label></div>
</div>

</fieldset>
</div>

{/tab}

{tab name="{tr}Shadow layer{/tr}"}

{* --- Shadow layer --- *}
<div class="adminoptionbox">
	<div class="adminoption"><input class="checkbox" type="checkbox" name="feature_layoutshadows" id="feature_layoutshadows"{if $prefs.feature_layoutshadows eq 'y'} checked="checked"{/if} onclick="flip('shadowlayers');"  /></div>
	<div class="adminoptionlabel"><label for="feature_layoutshadows">{tr}Shadow layer{/tr}</label>
	<br /><em>{tr}Additional layers for shadows, rounded corners or other decorative styling{/tr}.</em>

<div class="adminoptionboxchild" id="shadowlayers" style="display:{if $prefs.feature_layoutshadows eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="main_shadow_start">{tr}Main shadow start{/tr}:</label><br /><textarea name="main_shadow_start" id="main_shadow_start" rows="2" cols="40">{$prefs.main_shadow_start|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="main_shadow_end">{tr}Main shadow end{/tr}:</label><br /><textarea name="main_shadow_end" id="main_shadow_end" rows="2" cols="40">{$prefs.main_shadow_end|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="header_shadow_start">{tr}Header shadow start{/tr}</label><br /><textarea name="header_shadow_start" id="header_shadow_start" rows="2" cols="40">{$prefs.header_shadow_start|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="header_shadow_end">{tr}Header shadow end{/tr}</label>:<br /> <textarea name="header_shadow_end" id="header_shadow_end" rows="2" cols="40">{$prefs.header_shadow_end|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="middle_shadow_start">{tr}Middle shadow start{/tr}</label>: <br /><textarea name="middle_shadow_start" id="middle_shadow_start" rows="2" cols="40">{$prefs.middle_shadow_start|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="middle_shadow_end">{tr}Middle shadow end{/tr}</label>: <br /><textarea name="middle_shadow_end" id="middle_shadow_end" rows="2" cols="40">{$prefs.middle_shadow_end|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="center_shadow_start">{tr}Center shadow start{/tr}</label>: <br /><textarea name="center_shadow_start" id="center_shadow_start" rows="2" cols="40">{$prefs.center_shadow_start|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="center_shadow_end">{tr}Center shadow end{/tr}</label>:<br /><textarea name="center_shadow_end" id="center_shadow_end" rows="2" cols="40">{$prefs.center_shadow_end|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="footer_shadow_start">{tr}Footer shadow start{/tr}</label>:<br /><textarea name="footer_shadow_start" id="footer_shadow_start" rows="2" cols="40">{$prefs.footer_shadow_start|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="footer_shadow_end">{tr}Footer shadow end{/tr}</label>:<br /><textarea name="footer_shadow_end" id="footer_shadow_end" rows="2" cols="40">{$prefs.footer_shadow_end|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="box_shadow_start">{tr}Module (box) shadow start{/tr}</label>:<br /><textarea name="box_shadow_start" id="box_shadow_start" rows="2" cols="40">{$prefs.box_shadow_start|escape}</textarea></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="box_shadow_end">{tr}Module (box) shadow end{/tr}</label>:<br /><textarea name="box_shadow_end" id="box_shadow_end" rows="2" cols="40">{$prefs.box_shadow_end|escape}</textarea></div>
</div>

</div>
	
	</div>
</div>

{/tab}


	{tab name="{tr}Pagination links{/tr}"}


<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-max_records">{tr}Maximum number of records in listings{/tr}:</label> <input size="5" type="text" name="maxRecords" id="general-max_records" value="{$prefs.maxRecords|escape}" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="nextprev_pagination" id="nextprev_pagination" {if $prefs.nextprev_pagination eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="nextprev_pagination">{tr}Use relative (next / previous) pagination links{/tr}</label>.</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="direct_pagination" id="direct_pagination" {if $prefs.direct_pagination eq 'y'}checked="checked"{/if} onclick="flip('usedirectpagination');" /></div>
	<div class="adminoptionlabel"><label for="direct_pagination">{tr}Use direct pagination links{/tr}</label>.
	
<div class="adminoptionboxchild" id="usedirectpagination" style="display:{if $prefs.direct_pagination eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label>{tr}Max. number of links around the current item:{/tr}<input type="text" name="direct_pagination_max_middle_links" id="direct_pagination_max_middle_links" value="{$prefs.direct_pagination_max_middle_links}" size="4" /></label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label>{tr}Max. number of links after the first or before the last item:{/tr}<input type="text" name="direct_pagination_max_ending_links" id="direct_pagination_max_ending_links" value="{$prefs.direct_pagination_max_ending_links}" size="4" /></label></div>
</div>

</div>
	
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="pagination_firstlast" id="pagination_firstlast" {if $prefs.pagination_firstlast eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="pagination_firstlast">{tr}Display 'First' and 'Last' links{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="pagination_fastmove_links" id="pagination_fastmove_links" {if $prefs.pagination_fastmove_links eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="pagination_fastmove_links">{tr}Display fast move links (by 10 percent of the total number of pages) {/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="pagination_hide_if_one_page" id="pagination_hide_if_one_page" {if $prefs.pagination_hide_if_one_page eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="pagination_hide_if_one_page">{tr}Hide pagination when there is only one page{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="pagination_icons" id="pagination_icons" {if $prefs.pagination_icons eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="pagination_icons">{tr}Use Icons{/tr}</label></div>
</div>						


{/tab}
		
	{tab name="{tr}UI Effects{/tr}"}
	{* --- UI Effects (JQuery) --- *}
<div class="adminoptionbox">	
			<fieldset class="admin">
				<legend>
					<a href="#"><span>{tr}JQuery plugins and add-ons{/tr}</span></a>
				</legend>	
				{if $prefs.feature_jquery eq 'n'}
				 	 {remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}Requires jquery feature{/tr}</em>{icon _id="arrow_right" href="tiki-admin.php?page=features"}{/remarksbox}
				{/if}

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_tooltips" name="feature_jquery_tooltips" {if $prefs.feature_jquery_tooltips eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_tooltips">{tr}Tooltips{/tr}</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Tooltips" desc="{tr}JQuery Tooltips: Customisable help tips{/tr}"}{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_autocomplete" name="feature_jquery_autocomplete" {if $prefs.feature_jquery_autocomplete eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_autocomplete">{tr}Autocomplete{/tr}</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Autocomplete" desc="{tr}JQuery Autocomplete{/tr}"}{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_superfish" name="feature_jquery_superfish" {if $prefs.feature_jquery_superfish eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_superfish">{tr}Superfish{/tr}</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Superfish" desc="{tr}JQuery Superfish (effects on CSS menus){/tr}"}{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_reflection" name="feature_jquery_reflection" {if $prefs.feature_jquery_reflection eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_reflection">{tr}Reflection{/tr}</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Reflection" desc="{tr}JQuery Reflection (reflection effect on images){/tr}"}{/if}
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_ui" name="feature_jquery_ui" {if $prefs.feature_jquery_ui eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_ui">{tr}JQuery UI{/tr}</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#UI" desc="{tr}JQuery UI: More JQuery functionality{/tr}"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_jquery_ui_theme">{tr}JQuery UI Theme{/tr}: </label>
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
	{if $prefs.feature_help eq 'y'} {help url="JQuery#UI" desc="{tr}JQuery UI Theme: Themes for look and feel of JQuery UI widgets{/tr}"}{/if}</div>
</div>


<div class="adminoptionbox">
	<div class="adminoptionlabel">{icon _id=information} <em>{tr}For future use{/tr}:</em>

<div class="adminoptionboxchild">	

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_cycle" name="feature_jquery_cycle" {if $prefs.feature_jquery_cycle eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_cycle">{tr}Cycle{/tr} ({tr}slideshow{/tr})</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Cycle" desc="{tr}JQuery Cycle (slideshow){/tr}"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_sheet" name="feature_jquery_sheet" {if $prefs.feature_jquery_sheet eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_sheet">{tr}JQuery Sheet{/tr}</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Sheet" desc="{tr}JQuery Spreadsheet{/tr}"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_jquery_tablesorter" name="feature_jquery_tablesorter" {if $prefs.feature_jquery_tablesorter eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_jquery_tablesorter">{tr}JQuery Sortable Tables{/tr}</label>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#TableSorter" desc="{tr}JQuery Sortable Tables{/tr}"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"><label for=""></label>
	{if $prefs.feature_help eq 'y'} {/if}</div>
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
	<div class="adminoptionlabel"><label for="jquery_effect">{tr}Effect for modules{/tr}:</label> 
		<select name="jquery_effect" id="jquery_effect">
					            <option value="none" {if $prefs.jquery_effect_tabs eq 'none'}selected="selected"{/if}>
					              {tr}None{/tr}</option>
					            <option value="" {if $prefs.jquery_effect eq ''}selected="selected"{/if}>
					              {tr}Default{/tr}</option>
					            <option value="slide" {if $prefs.jquery_effect eq 'slide'}selected="selected"{/if}>
					              {tr}Slide{/tr}</option>
					            <option value="fade" {if $prefs.jquery_effect eq 'fade'}selected="selected"{/if}>
					              {tr}Fade{/tr}</option>
					            {if $prefs.feature_jquery_ui eq 'y'}
					            <option value="blind_ui" {if $prefs.jquery_effect eq 'blind_ui'}selected="selected"{/if}>
					              {tr}Blind (UI){/tr}</option>
					            <option value="clip_ui" {if $prefs.jquery_effect eq 'clip_ui'}selected="selected"{/if}>
					              {tr}Clip (UI){/tr}</option>
					            <option value="drop_ui" {if $prefs.jquery_effect eq 'drop_ui'}selected="selected"{/if}>
					              {tr}Drop (UI){/tr}</option>
					            <option value="explode_ui" {if $prefs.jquery_effect eq 'explode_ui'}selected="selected"{/if}>
					              {tr}Explode (UI){/tr}</option>
					            <option value="fold_ui" {if $prefs.jquery_effect eq 'fold_ui'}selected="selected"{/if}>
					              {tr}Fold (UI){/tr}</option>
					            <option value="puff_ui" {if $prefs.jquery_effect eq 'puff_ui'}selected="selected"{/if}>
					              {tr}Puff (UI){/tr}</option>
					            <option value="slide_ui" {if $prefs.jquery_effect eq 'slide_ui'}selected="selected"{/if}>
					              {tr}Slide (UI){/tr}</option>
					            {/if}
		</select>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Effects" desc="{tr}Main JQuery effect{/tr}"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"><label for="jquery_effect_speed">{tr}Speed{/tr} :</label> 
		<select name="jquery_effect_speed" id="jquery_effect_speed">
					            <option value="fast" {if $prefs.jquery_effect_speed eq 'fast'}selected="selected"{/if}>
					              {tr}Fast{/tr}</option>
					            <option value="normal" {if $prefs.jquery_effect_speed eq 'normal'}selected="selected"{/if}>
					              {tr}Normal{/tr}</option>
					            <option value="slow" {if $prefs.jquery_effect_speed eq 'slow'}selected="selected"{/if}>
					              {tr}Slow{/tr}</option>
		</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"><label for="jquery_effect_direction">{tr}Direction{/tr} :</label> 
		<select name="jquery_effect_direction" id="jquery_effect_direction">
					            <option value="vertical" {if $prefs.jquery_effect_direction eq 'vertical'}selected="selected"{/if}>
					              {tr}Vertical{/tr}</option>
					            <option value="horizontal" {if $prefs.jquery_effect_direction eq 'horizontal'}selected="selected"{/if}>
					              {tr}Horizontal{/tr}</option>
					            <option value="left" {if $prefs.jquery_effect_direction eq 'left'}selected="selected"{/if}>
					              {tr}Left{/tr}</option>
					            <option value="right" {if $prefs.jquery_effect_direction eq '"right"'}selected="selected"{/if}>
					              {tr}Right{/tr}</option>
					            <option value="up" {if $prefs.jquery_effect_direction eq 'up'}selected="selected"{/if}>
					              {tr}Up{/tr}</option>
					            <option value="down" {if $prefs.jquery_effect_direction eq 'down'}selected="selected"{/if}>
					              {tr}Down{/tr}</option>
		</select>
	</div>
</div>
			</fieldset>
</div>
<div class="adminoptionbox">			
			<fieldset class="admin">
				<legend>
					<a><span>{tr}Tab UI effects{/tr}</span></a>
				</legend>
				
<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"><label for="jquery_effect_tabs">{tr}Effect for tabs{/tr}:</label> 
		<select name="jquery_effect_tabs" id="jquery_effect_tabs">
					            <option value="none" {if $prefs.jquery_effect_tabs eq 'none'}selected="selected"{/if}>
					              {tr}None{/tr}</option>
					            <option value="normal" {if $prefs.jquery_effect_tabs eq 'normal'}selected="selected"{/if}>
					              {tr}Normal{/tr}</option>
					            <option value="slide" {if $prefs.jquery_effect_tabs eq 'slide'}selected="selected"{/if}>
					              {tr}Slide{/tr}</option>
					            <option value="fade" {if $prefs.jquery_effect_tabs eq 'fade'}selected="selected"{/if}>
					              {tr}Fade{/tr}</option>
					            {if $prefs.feature_jquery_ui eq 'y'}
					            <option value="blind_ui" {if $prefs.jquery_effect_tabs eq 'blind_ui'}selected="selected"{/if}>
					              {tr}Blind (UI){/tr}</option>
					            <option value="clip_ui" {if $prefs.jquery_effect_tabs eq 'clip_ui'}selected="selected"{/if}>
					              {tr}Clip (UI){/tr}</option>
					            <option value="drop_ui" {if $prefs.jquery_effect_tabs eq 'drop_ui'}selected="selected"{/if}>
					              {tr}Drop (UI){/tr}</option>
					            <option value="explode_ui" {if $prefs.jquery_effect_tabs eq 'explode_ui'}selected="selected"{/if}>
					              {tr}Explode (UI){/tr}</option>
					            <option value="fold_ui" {if $prefs.jquery_effect_tabs eq 'fold_ui'}selected="selected"{/if}>
					              {tr}Fold (UI){/tr}</option>
					            <option value="puff_ui" {if $prefs.jquery_effect_tabs eq 'puff_ui'}selected="selected"{/if}>
					              {tr}Puff (UI){/tr}</option>
					            <option value="slide_ui" {if $prefs.jquery_effect_tabs eq 'slide_ui'}selected="selected"{/if}>
					              {tr}Slide (UI){/tr}</option>
					            {/if}
		</select>
	{if $prefs.feature_help eq 'y'} {help url="JQuery#Effects" desc="{tr}JQuery effect for tabs{/tr}"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="jquery_effect_tabs_speed">{tr}Speed{/tr}</label>: 
		<select name="jquery_effect_tabs_speed" id="jquery_effect_tabs_speed">
					            <option value="fast" {if $prefs.jquery_effect_tabs_speed eq 'fast'}selected="selected"{/if}>
					              {tr}Fast{/tr}</option>
					            <option value="normal" {if $prefs.jquery_effect_tabs_speed eq 'normal'}selected="selected"{/if}>
					              {tr}Normal{/tr}</option>
					            <option value="slow" {if $prefs.jquery_effect_tabs_speed eq 'slow'}selected="selected"{/if}>
					              {tr}Slow{/tr}</option>
		</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="jquery_effect_tabs_direction">{tr}Direction{/tr}: </label>
		<select name="jquery_effect_tabs_direction" id="jquery_effect_tabs_direction">
					            <option value="vertical" {if $prefs.jquery_effect_tabs_direction eq 'vertical'}selected="selected"{/if}>
					              {tr}Vertical{/tr}</option>
					            <option value="horizontal" {if $prefs.jquery_tabs_effect_direction eq 'horizontal'}selected="selected"{/if}>
					              {tr}Horizontal{/tr}</option>
					            <option value="left" {if $prefs.jquery_effect_tabs_direction eq 'left'}selected="selected"{/if}>
					              {tr}Left{/tr}</option>
					            <option value="right" {if $prefs.jquery_effect_tabs_direction eq '"right"'}selected="selected"{/if}>
					              {tr}Right{/tr}</option>
					            <option value="up" {if $prefs.jquery_effect_tabs_direction eq 'up'}selected="selected"{/if}>
					              {tr}Up{/tr}</option>
					            <option value="down" {if $prefs.jquery_effect_tabs_direction eq 'down'}selected="selected"{/if}>
					              {tr}Down{/tr}</option>
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
	<div class="adminoption"><input type="checkbox" name="feature_tabs" id="general-feature_tabs" {if $prefs.feature_tabs eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="general-feature_tabs">{tr}Use Tabs{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="layout_section" id="general-layout_section" {if $prefs.layout_section eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="general-layout_section">{tr}Layout per section{/tr}</label>
	{if $prefs.layout_section eq 'y'}<br />{button _text="{tr}Admin layout per section{/tr}" href="tiki-admin_layout.php"}{/if}
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_iepngfix" id="feature_iepngfix"{if $prefs.feature_iepngfix eq 'y'} checked="checked"{/if} onclick="flip('iepngfix');" /></div>
	<div class="adminoptionlabel"><label for="feature_iepngfix">{tr}Correct PNG images alpha transparency in IE6 (experimental){/tr}</label>
								<div id="iepngfix" class="adminoptionboxchild" style="display:{if $prefs.feature_iepngfix eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label class="above" for="iepngfix_selectors">{tr}CSS selectors to be fixed{/tr}:</label>
		<input id="iepngfix_selectors" type="text" name="iepngfix_selectors" size="32" value="{$prefs.iepngfix_selectors}" />
		<br /><em>{tr}Separate multiple elements with a comma (&nbsp;,&nbsp;){/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label class="above" for="iepngfix_elements">{tr}HTMLDomElements to be fixed{/tr}:</label> 
		<input id="iepngfix_elements" type="text" name="iepngfix_elements" size="32" value="{$prefs.iepngfix_elements}" />
		<br /><em>{tr}Separate multiple elements with a comma (&nbsp;,&nbsp;){/tr}.</em></div>
</div>
								</div>
	</div>
</div>


<div class="adminoptionbox">
<fieldset><legend>{tr}Favicon{/tr}</legend>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="site_favicon">{tr}Favicon icon file name:{/tr}</label> <input type="text" name="site_favicon" id="site_favicon" value="{$prefs.site_favicon}" size="12" maxlength="32" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="site_favicon_type">{tr}Favicon icon MIME type:{/tr}</label> 
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
	<div class="adminoption"><input type="checkbox" id="use_context_menu_icon" name="use_context_menu_icon" {if $prefs.use_context_menu_icon eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="use_context_menu_icon">{tr}Use context menus for actions (icons){/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="use_context_menu_text" name="use_context_menu_text" {if $prefs.use_context_menu_text eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="use_context_menu_text">{tr}Use context menus for actions (text){/tr}</label>.</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"><label for=""></label>
	{if $prefs.feature_help eq 'y'} {/if}</div>
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
