{* $Id$ *}
{strip}
<div class="cbox">
	<form action="tiki-admin.php?page=look"  id="look" name="look" onreset="return(confirm('{tr}Cancel Edit{/tr}'))"  class="admin" method="post">
		<div class="heading input_submit_container" style="text-align: right">
			<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
			<input type="reset" name="looksetupreset" value="{tr}Reset{/tr}" />
		</div>

		{if $prefs.feature_tabs eq 'y'}
			{tabs}{tr}Theme{/tr}|{tr}General Layout{/tr}|{tr}UI Effects{/tr}|{tr}Other{/tr}{/tabs}
			{cycle name=content values="1,2,3,4" print=false advance=false reset=true}
		{/if}

		<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
			{if $prefs.feature_tabs neq 'y'}
				<legend class="heading">
					<a href="#theme" onclick="flip('theme'); return false;">
						<span>{tr}Theme{/tr}</span>
					</a>
				</legend>
				<div id="theme" style="display:{if isset($smarty.session.tiki_cookie_jar.show_theme) and $smarty.session.tiki_cookie_jar.show_theme neq 'y'}none{else}block{/if};">
			{/if}
				<table class="admin">
					{if isset($thumbfile)}<tr>
						<td colspan="2">
							<div id="style_thumb_div"><img src="{$thumbfile}" id="style_thumb" /></div>
						</td>
					</tr>{/if}
					<tr>
						<td class="form" >
							<label for="general-theme">{tr}Theme{/tr}:</label>
						</td>
						<td width="67%">
							<select name="site_style" id="general-theme">
							{section name=ix loop=$styles}
								<option value="{$styles[ix]|escape}"{if $a_style eq $styles[ix]} selected="selected"{/if}>{$styles[ix]}</option>
							{/section}
							</select>
							{if $prefs.javascript_enabled eq 'n' or $prefs.feature_jquery eq 'n'}
								<input type="submit" name="changestyle" value="{tr}Go{/tr}" />
							{/if}
	  						{if $prefs.change_theme eq 'y' and ($user_prefs.theme neq '' and $prefs.site_style neq $user_prefs.theme) or ($prefs.style neq '' and $prefs.site_style neq $prefs.style)}
	  							{remarksbox type="warning" title="{tr}Admin{/tr}"}{tr}The "users can change theme" feature will override the theme displayed.{/tr}{/remarksbox}
							{/if}
							{if $prefs.site_style != $a_style}
								{remarksbox type="note" title="{tr}Note{/tr}}{tr}Theme not saved yet - click "Apply"{/tr}{/remarksbox}
							{/if}
						</td>
					</tr>
					<tr>
						<td class="form" >
							<label for="general-theme">{tr}Theme options{/tr}:</label>
						</td>
						<td width="67%">
							<select name="site_style_option" id="general-theme-options" {if !$style_options}disabled{/if}>
							{if !$style_options}<option value="">{tr}None{/tr}</option>{/if}
							{section name=ix loop=$style_options}
								<option value="{$style_options[ix]|escape}"{if $prefs.style_option eq $style_options[ix]} selected="selected"{/if}>{$style_options[ix]}</option>
							{/section}
							</select>
						</td>
					</tr>
					<tr>					
  						<td class="form">{tr}Users can change theme{/tr}:</td>
						<td>
						    <table><tr>
						    <td style="width: 20px"><input type="checkbox" name="change_theme" {if $prefs.change_theme eq 'y'}checked="checked"{/if}/></td>
						    <td>
						      <div id="select_available_styles" {if count($prefs.available_styles) > 0 and $prefs.available_styles[0] ne ''}style="display:none;"{else}style="display:block;"{/if}>
						        <a class="link" href="javascript:show('available_styles');hide('select_available_styles');">{tr}Restrict available themes{/tr}</a>
						      </div>
						      <div id="available_styles" {if count($prefs.available_styles) == 0 or $prefs.available_styles[0] eq ''}style="display:none;"{else}style="display:block;"{/if}>
						        {tr}Available styles:{/tr}<br />
						        <select name="available_styles[]" multiple="multiple" size="5">
								  <option value=''>{tr}All{/tr}</option>
						          {section name=ix loop=$styles}
						            <option value="{$styles[ix]|escape}"
						              {if $prefs.available_styles|count gt 0 and in_array($styles[ix], $prefs.available_styles)}selected="selected"{/if}>
						              {$styles[ix]}
						            </option>
						          {/section}
						        </select>
						      </div>
						    </td>
						    </tr></table>
						</td>					
					</tr>
					<tr>
						<td class="form">
							<label for="useGroupTheme">{tr}Each group can have its theme{/tr}:</label>
						</td>
						<td>
							<input type="checkbox" name="useGroupTheme" id="useGroupTheme" {if $prefs.useGroupTheme eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td class="form">
							<label for="general-slideshows">{tr}Slideshows theme{/tr}:</label>
						</td>
						<td>
							<select name="slide_style" id="general-slideshows">
							{section name=ix loop=$slide_styles}
								<option value="{$slide_styles[ix]|escape}"{if $prefs.slide_style eq $slide_styles[ix]} selected="selected"{/if}>{$slide_styles[ix]}</option>
							{/section}
							</select>
						</td>
					</tr>
					<tr>
    	   		<td class="form">
							<label for="transition_style_ver">{tr}Use transition style sheet from version{/tr}:</label>
						</td>
						<td>
							<select name="transition_style_ver" id="transition_style_ver">
								<option value="none" {if $prefs.transition_style_ver eq 'none'}selected="selected"{/if}>{tr}Never use transition css{/tr}</option>
								<option value="css_specified_only" {if $prefs.transition_style_ver eq 'css_specified_only'}selected="selected"{/if}>{tr}Use @version:x.x specified in theme css or none if not specified{/tr}</option>
								<option value="1.9" {if $prefs.transition_style_ver eq '1.9'}selected="selected"{/if}>{tr}Use @version:x.x specified in theme css or 1.9 if not specified{/tr}</option>
								<option value="2.0" {if $prefs.transition_style_ver eq '2.0'}selected="selected"{/if}>{tr}Use @version:x.x specified in theme css or 2.0 if not specified{/tr}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="form">
							{if $prefs.feature_help eq 'y'}
							<a href="{$prefs.helpurl}Edit+CSS" target="tikihelp" class="tikihelp" title="{tr}Edit CSS{/tr}">
							{/if}
							{tr}Edit CSS{/tr}
							{if $prefs.feature_help eq 'y'}</a>{/if}
						</td>
						<td>
							<input type="checkbox" name="feature_editcss" {if $prefs.feature_editcss eq 'y'}checked="checked"{/if}/>
							{if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}
								{button _text="{tr}Edit CSS{/tr}" href="tiki-edit_css.php"}
							{/if}
						</td>
					</tr>
 	     	<tr>
					<td colspan="2"><hr/></td>
				</tr>        
				<tr>
					<td class="form">{tr}Theme Control{/tr}</td>
					<td>
						<input type="checkbox" name="feature_theme_control" {if $prefs.feature_theme_control eq 'y'}checked="checked"{/if}/>
						{if $prefs.feature_theme_control eq 'y'}
							{button href="tiki-theme_control.php" _text="{tr}Theme Control{/tr}"}
						{/if}
					</td>
				</tr>
				<tr>
					<td class="form">
						{if $prefs.feature_help eq 'y'}
							<a href="{$prefs.helpurl}View+Templates" target="tikihelp" class="tikihelp" title="{tr}Template Viewing{/tr}">{/if} {tr}Tiki Template Viewing{/tr} {if $prefs.feature_help eq 'y'}</a>
						{/if}
					</td>
					<td>
						<input type="checkbox" name="feature_view_tpl" {if $prefs.feature_view_tpl eq 'y'}checked="checked"{/if}/>
						{if $prefs.feature_view_tpl eq 'y'}
							{button href="tiki-edit_templates.php" _text="{tr}View Templates{/tr}" }
						{/if}
					</td>
				</tr>
				<tr>
					<td class="form">
						{if $prefs.feature_help eq 'y'}
							<a href="{$prefs.helpurl}Edit+Templates" target="tikihelp" class="tikihelp" title="{tr}Edit Templates{/tr}">
						{/if}
						{tr}Edit Templates{/tr}
						{if $prefs.feature_help eq 'y'}</a>{/if}
					</td>
					<td>
						<input type="checkbox" name="feature_edit_templates" {if $prefs.feature_edit_templates eq 'y'}checked="checked"{/if}/>
						{if $prefs.feature_edit_templates eq 'y'}
							{button href="tiki-edit_templates.php" _text="{tr}Edit Templates{/tr}" }
						{/if}
					</td>
				</tr>
			</table>
			{if $prefs.feature_tabs neq 'y'}</div>{/if}
		</fieldset> 

{* --- General Layout options --- *}

		<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
			{if $prefs.feature_tabs neq 'y'}
				<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}">
					<a href="#layout" onclick="flip('layout'); return false;">
						<span>{tr}General Layout options{/tr}</span>
					</a>
				</legend>
				<div id="layout" style="display:{if isset($smarty.session.tiki_cookie_jar.show_layout) and $smarty.session.tiki_cookie_jar.show_layout neq 'y'}none{else}block{/if};">{/if}
				<table class="admin" width="100%">
				<tr>
					<td class="form" colspan="5">

					{* --- Shadow layer --- *}
						<fieldset class="admin">
							<legend>{tr}Shadow layer{/tr}</legend>
							<div>{tr}Enable additional general layout layers for shadows, rounded corners or other decorative styling{/tr}</div>
							<table class="admin">
							<tr>
								<td class="form">
									<label for="feature_layoutshadows">{tr}Activate{/tr}:</label>
								</td>
								<td>				
									<input class="checkbox" type="checkbox" name="feature_layoutshadows" id="feature_layoutshadows"{if $prefs.feature_layoutshadows eq 'y'} checked="checked"{/if} onclick="toggleTrTd('shd1');toggleTrTd('shd2');toggleTrTd('shd3');toggleTrTd('shd4');toggleTrTd('shd5');toggleTrTd('shd6');toggleTrTd('shd7');toggleTrTd('shd8');toggleTrTd('shd9');toggleTrTd('shd10');"  />
								</td>
							</tr>
							<tr id="shd1" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="main_shadow_start">{tr}Main shadow start{/tr}</label></td>
								<td class="form"><textarea name="main_shadow_start" id="main_shadow_start" rows="2" cols="40">{$prefs.main_shadow_start|escape}</textarea></td>
							</tr>
							<tr id="shd2" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="main_shadow_end">{tr}Main shadow end{/tr}</label></td>
								<td class="form"><textarea name="main_shadow_end" id="main_shadow_end" rows="2" cols="40">{$prefs.main_shadow_end|escape}</textarea></td>
							</tr>
							<tr id="shd3" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="header_shadow_start">{tr}Header shadow start{/tr}</label></td>
								<td class="form"><textarea name="header_shadow_start" id="header_shadow_start" rows="2" cols="40">{$prefs.header_shadow_start|escape}</textarea></td>
							</tr>
							<tr id="shd4" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="header_shadow_end">{tr}Header shadow end{/tr}</label></td>
								<td class="form"><textarea name="header_shadow_end" id="header_shadow_end" rows="2" cols="40">{$prefs.header_shadow_end|escape}</textarea></td>
							</tr>
							<tr id="shd5" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="middle_shadow_start">{tr}Middle shadow start{/tr}</label></td>
								<td class="form"><textarea name="middle_shadow_start" id="middle_shadow_start" rows="2" cols="40">{$prefs.middle_shadow_start|escape}</textarea></td>
							</tr>
							<tr id="shd6" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="middle_shadow_end">{tr}Middle shadow end{/tr}</label></td>
								<td class="form"><textarea name="middle_shadow_end" id="middle_shadow_end" rows="2" cols="40">{$prefs.middle_shadow_end|escape}</textarea></td>
							</tr>
							<tr id="shd7" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="center_shadow_start">{tr}Center shadow start{/tr}</label></td>
								<td class="form"><textarea name="center_shadow_start" id="center_shadow_start" rows="2" cols="40">{$prefs.center_shadow_start|escape}</textarea></td>
							</tr>
							<tr id="shd8" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="center_shadow_end">{tr}Center shadow end{/tr}</label></td>
								<td class="form"><textarea name="center_shadow_end" id="center_shadow_end" rows="2" cols="40">{$prefs.center_shadow_end|escape}</textarea></td>
							</tr>
							<tr id="shd9" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="footer_shadow_start">{tr}Footer shadow start{/tr}</label></td>
								<td class="form"><textarea name="footer_shadow_start" id="footer_shadow_start" rows="2" cols="40">{$prefs.footer_shadow_start|escape}</textarea></td>
							</tr>
							<tr id="shd10" {if $prefs.feature_layoutshadows ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
								<td class="form"><label for="footer_shadow_end">{tr}Footer shadow end{/tr}</label></td>
								<td class="form"><textarea name="footer_shadow_end" id="footer_shadow_end" rows="2" cols="40">{$prefs.footer_shadow_end|escape}</textarea></td>
							</tr>
							</table>
						</fieldset>					

					{* --- Customize Site Header --- *}
						<fieldset class="admin">
							<legend>
								<a href="#" title="{tr}Top{/tr}">
									<span>{tr}Custom Site Header{/tr}</span>
								</a>
							</legend>
              {remarksbox type="note"}{tr}Activate will only show content for admin. Check Publish to use content for all users.{/tr}{/remarksbox}
							<table class="admin">
							<tr>
								<td class="form">
									<label for="feature_sitemycode">{tr}Activate{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_sitemycode" id="feature_sitemycode"{if $prefs.feature_sitemycode eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitemycode">{tr}Content{/tr}:</label>
								</td>
								<td>
									<textarea name="sitemycode" rows="6" cols="40" style="width: 90%" id="sitemycode">{$prefs.sitemycode|escape}</textarea>
									<br />
									<small><em>{tr}Example{/tr}</em>: 
										{literal}{if $user neq ''}{/literal}&lt;div align="right" style="float: right; font-size: 10px"&gt;{literal}{{/literal}tr}{tr}logged as{/tr}{literal}{/tr}{/literal}: {literal}{$user}{/literal}&lt;/div&gt;{literal}{/if}{/literal}
									</small>
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitemycode_publish">{tr}Publish{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="sitemycode_publish" id="sitemycode_publish"{if $prefs.sitemycode_publish eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
        			</table>
						</fieldset>

					{* --- Customize Site Logo and Site Titile--- *}
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Site Logo and Title{/tr}</span></a>
							</legend>
							<table class="admin">
							<tr>
								<td class="form">
									<label for="feature_sitelogo">{tr}Activate{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_sitelogo" id="feature_sitelogo"{if $prefs.feature_sitelogo eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitelogo_src">{tr}Site logo source (image path){/tr}:</label>
								</td>
								<td>
									<input type="text" name="sitelogo_src" id="sitelogo_src" value="{$prefs.sitelogo_src}" size="60" style="width: 90%" />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitelogo_bgcolor">{tr}Site logo background color{/tr}:</label>
								</td>
								<td>
									<input type="text" name="sitelogo_bgcolor" id="sitelogo_bgcolor" value="{$prefs.sitelogo_bgcolor}" size="15" maxlength="15" />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitelogo_bgstyle">{tr}Site logo background style{/tr}:</label>
								</td>
								<td>
									<input type="text" name="sitelogo_bgstyle" id="sitelogo_bgstyle" value="{$prefs.sitelogo_bgstyle}" />
									<br />
									<i>{tr}Example{/tr} silver url(myStyle/img.gif) repeat</i>
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitelogo_align">{tr}Site logo alignment{/tr}:</label>
								</td>
								<td>
									<select name="sitelogo_align" id="sitelogo_align">
										<option value="left" {if $prefs.sitelogo_align eq 'left'}selected="selected"{/if}>{tr}on left side{/tr}</option>
										<option value="center" {if $prefs.sitelogo_align eq 'center'}selected="selected"{/if}>{tr}on center{/tr}</option>
										<option value="right" {if $prefs.sitelogo_align eq 'right'}selected="selected"{/if}>{tr}on right side{/tr}</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitelogo_title">{tr}Site logo title (on mouse over){/tr}:</label>
								</td>
								<td>
									<input type="text" name="sitelogo_title" id="sitelogo_title" value="{$prefs.sitelogo_title}" size="50" maxlength="200" />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="sitelogo_alt">{tr}Alt. description (e.g. for text browsers){/tr}:</label>
								</td>
								<td>
									<input type="text" name="sitelogo_alt" id="sitelogo_alt" value="{$prefs.sitelogo_alt}" size="50" maxlength="200" />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="_sitetitle">{tr}Site title{/tr}:</label>
								</td>
								<td>
									<input type="text" name="sitetitle" id="_sitetitle" value="{$prefs.sitetitle}" size="50" maxlength="200" />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="_sitesubtitle">{tr}Site subtitle{/tr}:</label>
								</td>
								<td>
									<input type="text" name="sitesubtitle" id="_sitesubtitle" value="{$prefs.sitesubtitle}" size="50" maxlength="200" />
								</td>
							</tr>
							</table>
						</fieldset>                                

					{* --- Site Search Bar --- *}
        		<fieldset>
        			<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Site Search Bar{/tr}</span></a>
							</legend>
							<table class="admin">
							<tr> 
								<td class="form">
									<label for="feature_sitesearch">{tr}Activate{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_sitesearch" id="feature_sitesearch"{if $prefs.feature_sitesearch eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							</table>
						</fieldset>

					{* --- Site Login Bar --- *}
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Site Login Bar{/tr}</span></a>
							</legend>
							<table class="admin">
							<tr> 
								<td class="form">
									<label for="feature_site_login">{tr}Activate{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_site_login" id="feature_site_login"{if $prefs.feature_site_login eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							</table>
						</fieldset>                                 

					{* --- Top Bar --- *}
						<fieldset>
							<legend>
								<span>
									<input type="checkbox" name="feature_top_bar" {if $prefs.feature_top_bar eq 'y'}checked="checked"{/if}/>
								<a href="#" title="{tr}Top{/tr}">{tr}Top Bar{/tr}</a>
								</span>
							</legend> 
							<table class="admin">
							<tr> 
								<td class="form">
									<label for="feature_sitemenu">{tr}Site menu bar{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_sitemenu" id="feature_sitemenu"{if $prefs.feature_sitemenu eq 'y'} checked="checked"{/if} />
									{tr}Note: Needs feature PHPLayers on(default), or feature CSS Menu on  {/tr}{tr}Admin{/tr}&nbsp;{$prefs.site_crumb_seper}&nbsp;{tr}Features{/tr}
								</td>
							</tr>
							<tr> 
								<td class="form">
									<label for="feature_topbar_id_menu">{tr}Menu ID{/tr}:</label>
								</td>
								<td>
									<input type="text" name="feature_topbar_id_menu" id="feature_topbar_id_menu" value="{$prefs.feature_topbar_id_menu}" size="6" maxlength="6" />
								</td>
							</tr>
							<tr> 
								<td class="form">
									<label for="feature_topbar_version">{tr}Current Version{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_topbar_version" id="feature_topbar_version"{if $prefs.feature_topbar_version eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr> 
								<td class="form">
									<label for="feature_topbar_debug">{tr}Debugger Console{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_topbar_debug" id="feature_topbar_debug"{if $prefs.feature_topbar_debug eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="feature_topbar_custom_code">{tr}Custom code{/tr}:</label>
								</td>
								<td>
									<textarea name="feature_topbar_custom_code" id="feature_topbar_custom_code" rows="6" cols="40" style="width: 90%">{$prefs.feature_topbar_custom_code}</textarea>
								</td>
							</tr>
							</table>
						</fieldset>                                
	       	</td>
				</tr>      
				<tr>
        	<td>
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>
								{if $prefs.feature_help eq 'y'}
									<a href="{$prefs.helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">
								{/if}
        				{tr}Left column{/tr}:
								{if $prefs.feature_help eq 'y'}</a>{/if}
								</span></a>
							</legend>
        			<select name="feature_left_column">
								<option value="y" {if $prefs.feature_left_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
								<option value="user" {if $prefs.feature_left_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
								<option value="n" {if $prefs.feature_left_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
							</select>
						</fieldset>
					</td>
					<td class="form" colspan="3">
					{* --- Site Breadcrumbs --- *}
						<fieldset class="admin">
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Site Breadcrumbs{/tr}</span></a>
							</legend>
							<table class="admin">
							<tr>
						    <td class="form">
									<label for="feature_breadcrumbs">{tr}Activate{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_breadcrumbs" id="feature_breadcrumbs"{if $prefs.feature_breadcrumbs eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="feature_siteloc">{tr}Site location bar{/tr}:</label>
								</td>
								<td>
									<select name="feature_siteloc" id="feature_siteloc">
										<option value="y" {if $prefs.feature_siteloc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
										<option value="page" {if $prefs.feature_siteloc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
										<option value="n" {if $prefs.feature_siteloc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="feature_siteloclabel">{tr}Prefix breadcrumbs with 'Location : '{/tr} </label>
								</td>
								<td>
									<input type="checkbox" name="feature_siteloclabel" id="feature_siteloclabel"{if $prefs.feature_siteloclabel eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="feature_sitetitle">{tr}Larger font for{/tr}:</label>
								</td>
								<td>
									<select name="feature_sitetitle" id="feature_sitetitle">
										<option value="y" {if $prefs.feature_sitetitle eq 'y'}selected="selected"{/if}>{tr}entire location{/tr}</option>
										<option value="title" {if $prefs.feature_sitetitle eq 'title'}selected="selected"{/if}>{tr}page name{/tr}</option>
										<option value="n" {if $prefs.feature_sitetitle eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="feature_sitedesc">{tr}Use page description:{/tr}</label>
								</td>
								<td>
									<select name="feature_sitedesc" id="feature_sitedesc">
										<option value="y" {if $prefs.feature_sitedesc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
										<option value="page" {if $prefs.feature_sitedesc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
										<option value="n" {if $prefs.feature_sitedesc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
									</select>
								</td>
							</tr>
							</table>
						</fieldset>
					</td>
					<td class="form">
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>
									{if $prefs.feature_help eq 'y'}
										<a href="{$prefs.helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">
									{/if}
        					{tr}Right column{/tr}:
									{if $prefs.feature_help eq 'y'}</a>{/if}
								</span></a>
							</legend>
							<select name="feature_right_column">
								<option value="y" {if $prefs.feature_right_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
								<option value="user" {if $prefs.feature_right_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
								<option value="n" {if $prefs.feature_right_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
							</select>
						</fieldset>
					</td>
				</tr>
	      {* --- Site Report Bar --- *}
				<tr>
					<td colspan="5">
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Site Report Bar{/tr}</span></a>
							</legend>
							<table class="admin">
							<tr> 
								<td class="form">
									<label for="feature_site_report">{tr}Webmaster Report{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_site_report" id="feature_site_report"{if $prefs.feature_site_report eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="feature_site_report_email">{tr}Webmaster Email{/tr}:</label>
								</td>
								<td>
									<input type="text" name="feature_site_report_email" id="feature_site_report_email" value="{$prefs.feature_site_report_email}" />
									<i>{tr}Left blank to use the default sender email{/tr}</i>
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="feature_site_send_link">{tr}Email this page{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_site_send_link" id="feature_site_send_link"{if $prefs.feature_site_send_link eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Custom Site Footer{/tr}</span></a>
							</legend>
							<table class="admin">
							<tr>
								<td class="form">
									<label for="feature_bot_logo">{tr}Activate{/tr}:</label>
								</td>
								<td>
									<input type="checkbox" name="feature_bot_logo" id="feature_bot_logo"{if $prefs.feature_bot_logo eq 'y'} checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td class="form">
									<label for="bot_logo_code">{tr}Content{/tr}:</label>
								</td>
								<td>
									<textarea id="bot_logo_code" name="bot_logo_code" rows="6" cols="40" style="width: 90%">{$prefs.bot_logo_code|escape}</textarea>
									<br />
									<small><em>{tr}Example{/tr}</em>:&lt;div style="text-align: center"&gt;&lt;small&gt;Powered by Tikiwiki&lt;/small&gt;&lt;/div&gt;</small>
								</td>
							</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Custom End of <body> Code{/tr}</span></a>
							</legend>
							<table class="admin">
							<tr>
								<td class="form">
									<label for="feature_endbody_code">{tr}Content{/tr}:</label>
								</td>
								<td>
									<textarea id="feature_endbody_code" name="feature_endbody_code" rows="6" cols="40" style="width: 90%">{$prefs.feature_endbody_code|escape}</textarea>
									<br />
									<small><em>{tr}Example{/tr}</em>{literal}{wiki}&#123;literal&#125;{GOOGLEANALYTICS(account=xxxx) /}&#123;/literal&#125;{/wiki}{/literal}</small>
								</td>
							</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="5" class="form">
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Bottom bar{/tr}</span></a>
							</legend>
							<label for="feature_bot_bar">{tr}Activate{/tr}:</label>
							<input type="checkbox" name="feature_bot_bar" {if $prefs.feature_bot_bar eq 'y'}checked="checked"{/if}/>
							<hr />
							<input type="checkbox" id="feature_bot_bar_icons" name="feature_bot_bar_icons"	{if $prefs.feature_bot_bar_icons eq 'y'}checked="checked"{/if}/>
							<label for="feature_bot_bar_icons">{tr}Bottom bar icons{/tr}</label>
							<br />
							<input type="checkbox" id="feature_bot_bar_debug" name="feature_bot_bar_debug" {if $prefs.feature_bot_bar_debug eq 'y'}checked="checked"{/if}/>
							<label for="feature_bot_bar_debug">{tr}Bottom bar debug{/tr}</label>
							<br />
							<input type="checkbox" id="feature_bot_bar_rss" name="feature_bot_bar_rss" {if $prefs.feature_bot_bar_rss eq 'y'}checked="checked"{/if}/>
							<label for="feature_bot_bar_rss">{tr}Bottom bar (RSS){/tr}</label>
<br />
							<input type="checkbox" id="feature_bot_bar_power_by_tw" name="feature_bot_bar_power_by_tw" {if $prefs.feature_bot_bar_power_by_tw eq 'y'}checked="checked"{/if}/>
							<label for="feature_bot_bar_power_by_tw">{tr}Power by{/tr} TikiWiki</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="5" class="form">
						<fieldset>
							<legend>
								<a href="#" title="{tr}Top{/tr}"><span>{tr}Pagination links{/tr}</span></a>
							</legend>

							<div class="adminoptionbox">	  
							<div class="adminoptionlabel"><label for="general-max_records">{tr}Maximum number of records in listings{/tr}:</label> <input size="5" type="text" name="maxRecords" id="general-max_records" value="{$prefs.maxRecords|escape}" /></div>
							</div>

							<input type="checkbox" name="nextprev_pagination" id="nextprev_pagination" {if $prefs.nextprev_pagination eq 'y'}checked="checked"{/if}/>
							<label for="nextprev_pagination">{tr}Use relative (next / previous) pagination links{/tr}</label>
							<hr />
							<input type="checkbox" name="direct_pagination" id="direct_pagination" {if $prefs.direct_pagination eq 'y'}checked="checked"{/if}/>
							<label for="direct_pagination">{tr}Use direct pagination links{/tr}</label>
							<div style="margin-left:20px">
							{tr}Max. number of links around the current item:{/tr}<input type="text" name="direct_pagination_max_middle_links" id="direct_pagination_max_middle_links" value="{$prefs.direct_pagination_max_middle_links}" size="4" /><br />
							{tr}Max. number of links after the first or before the last item:{/tr}<input type="text" name="direct_pagination_max_ending_links" id="direct_pagination_max_ending_links" value="{$prefs.direct_pagination_max_ending_links}" size="4" />
							</div>
							<hr />
							<input type="checkbox" name="pagination_firstlast" id="pagination_firstlast" {if $prefs.pagination_firstlast eq 'y'}checked="checked"{/if}/>
							<label for="pagination_firstlast">{tr}Display 'First' and 'Last' links{/tr}</label><br />
							<input type="checkbox" name="pagination_fastmove_links" id="pagination_fastmove_links" {if $prefs.pagination_fastmove_links eq 'y'}checked="checked"{/if}/>
							<label for="pagination_fastmove_links">{tr}Display fast move links (by 10 percent of the total number of pages) {/tr}</label><br />
							<input type="checkbox" name="pagination_hide_if_one_page" id="pagination_hide_if_one_page" {if $prefs.pagination_hide_if_one_page eq 'y'}checked="checked"{/if}/>
							<label for="pagination_hide_if_one_page">{tr}Hide pagination when there is only one page{/tr}</label><br />
							<input type="checkbox" name="pagination_icons" id="pagination_icons" {if $prefs.pagination_icons eq 'y'}checked="checked"{/if}/>
							<label for="pagination_icons">{tr}Use Icons{/tr}</label><br />
						</fieldset>
					</td>
				</tr>
			</table>
			{if $prefs.feature_tabs neq 'y'}</div>{/if}
		</fieldset>
		
{* --- UI Effects (JQuery) --- *}

		<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
			{if $prefs.feature_tabs neq 'y'}
				<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}">
					<a href="#ui_effects" onclick="flip('ui_effects'); return false;">
					<span>{tr}UI Effects{/tr}</span>
					</a>
				</legend>
				<div id="ui_effects" style="display:{if isset($smarty.session.tiki_cookie_jar.show_other) and $smarty.session.tiki_cookie_jar.show_other neq 'y'}none{else}block{/if};">
			{/if}
			<fieldset class="admin">
				<legend>
					<a href="#"><span>{tr}JQuery plugins and add-ons{/tr}</span></a>
				</legend>
				{if $prefs.feature_jquery eq 'n'}
				 	 {remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}Requires jquery feature{/tr}</em>{icon _id="arrow_right" href="tiki-admin.php?page=features"}{/remarksbox}
				{/if}
				<table>
					<tr>
						<td width=30%>
							<label for="feature_jquery_tooltips">{tr}JQuery Tooltips{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#Tooltips" desc="{tr}JQuery Tooltips: Customisable help tips{/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_tooltips" {if $prefs.feature_jquery_tooltips eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="feature_jquery_autocomplete">{tr}JQuery Autocomplete{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#Autocomplete" desc="{tr}JQuery Autocomplete{/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_autocomplete" {if $prefs.feature_jquery_autocomplete eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="feature_jquery_superfish">{tr}JQuery Superfish{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#Superfish" desc="{tr}JQuery Superfish (effects on CSS menus){/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_superfish" {if $prefs.feature_jquery_superfish eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="feature_jquery_reflection">{tr}JQuery Reflection{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#Reflection" desc="{tr}JQuery Reflection (reflection effect on images){/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_reflection" {if $prefs.feature_jquery_reflection eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="feature_jquery_cycle">{tr}JQuery Cycle (slideshow){/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#Cycle" desc="{tr}JQuery Cycle (slideshow){/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_cycle" {if $prefs.feature_jquery_cycle eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td colspan=3>
							<hr />
							<em>{tr}For future use{/tr}</em>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="feature_jquery_ui">{tr}JQuery UI{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#UI" desc="{tr}JQuery UI: More JQuery functionality{/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_ui" {if $prefs.feature_jquery_ui eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="feature_jquery_sheet">{tr}JQuery Sheet{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#Sheet" desc="{tr}JQuery Spreadsheet{/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_sheet" {if $prefs.feature_jquery_sheet eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="feature_jquery_tablesorter">{tr}JQuery Sortable Tables{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#TableSorter" desc="{tr}JQuery Sortable Tables{/tr}"}
						</td>
						<td>
							<input type="checkbox" name="feature_jquery_tablesorter" {if $prefs.feature_jquery_tablesorter eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="admin">
				<legend>
					<a><span>{tr}Standard UI effects{/tr}</span></a>
				</legend>
				<table>
					<tr>
						<td width=30%>
							<label for="jquery_effect">{tr}Effect for modules etc{/tr}</label>
						</td>
						<td width=2%>
					        {help url="JQuery#Effects" desc="{tr}Main JQuery effect{/tr}"}
						</td>
						<td>
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
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="jquery_effect_speed">{tr}Effect speed{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
							<select name="jquery_effect_speed" id="jquery_effect_speed">
					            <option value="fast" {if $prefs.jquery_effect_speed eq 'fast'}selected="selected"{/if}>
					              {tr}Fast{/tr}</option>
					            <option value="normal" {if $prefs.jquery_effect_speed eq 'normal'}selected="selected"{/if}>
					              {tr}Normal{/tr}</option>
					            <option value="slow" {if $prefs.jquery_effect_speed eq 'slow'}selected="selected"{/if}>
					              {tr}Slow{/tr}</option>
					         </select>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="jquery_effect_direction">{tr}Effect direction{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
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
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="admin">
				<legend>
					<a><span>{tr}Tab UI effects{/tr}</span></a>
				</legend>
				<table>
					<tr>
						<td width=30%>
							<label for="jquery_effect_tabs">{tr}Effect for tabs{/tr}</label>
						</td>
						<td width=2%>
							{help url="JQuery#Effects" desc="{tr}JQuery effect for tabs{/tr}"}
						</td>
						<td>
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
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="jquery_effect_tabs_speed">{tr}Effect speed for tabs{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
							<select name="jquery_effect_tabs_speed" id="jquery_effect_tabs_speed">
					            <option value="fast" {if $prefs.jquery_effect_tabs_speed eq 'fast'}selected="selected"{/if}>
					              {tr}Fast{/tr}</option>
					            <option value="normal" {if $prefs.jquery_effect_tabs_speed eq 'normal'}selected="selected"{/if}>
					              {tr}Normal{/tr}</option>
					            <option value="slow" {if $prefs.jquery_effect_tabs_speed eq 'slow'}selected="selected"{/if}>
					              {tr}Slow{/tr}</option>
					         </select>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="jquery_effect_tabs_direction">{tr}Effect direction for tabs{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
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
						</td>
					</tr>
				</table>
			</fieldset>
			{if $prefs.feature_tabs neq 'y'}</div>{/if}
		</fieldset>
{* --- Other --- *}
		<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent admin" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
			{if $prefs.feature_tabs neq 'y'}
				<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}">
					<a href="#other" onclick="flip('other'); return false;">
					<span>{tr}Other options{/tr}</span>
					</a>
				</legend>
				<div id="other" style="display:{if isset($smarty.session.tiki_cookie_jar.show_other) and $smarty.session.tiki_cookie_jar.show_other neq 'y'}none{else}block{/if};">
			{/if}
			<fieldset class="admin">
				<legend>
					<a><span>{tr}Miscellaneous{/tr}</span></a>
				</legend>
				<table>
					<tr>
						<td width=30%>
							<label for="general-feature_tabs">{tr}Use Tabs{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
							<input type="checkbox" name="feature_tabs" id="general-feature_tabs" {if $prefs.feature_tabs eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
			        		<label for="general-menu_folders">{tr}Display menus as folders{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
							<input type="checkbox" name="feature_menusfolderstyle" id="general-menu_folders" {if $prefs.feature_menusfolderstyle eq 'y'}checked="checked"{/if}/>
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="general-layout_section">{tr}Layout per section{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
							<input type="checkbox" name="layout_section" id="general-layout_section" {if $prefs.layout_section eq 'y'}checked="checked"{/if}/>
							{if $prefs.layout_section eq 'y'}<a href="tiki-admin_layout.php" class="linkbut link">{else}<span class="linkbut disabled">{/if}
							{tr}Admin layout per section{/tr}
							{if $prefs.layout_section eq 'y'}</a>{else}</span>{/if}
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="site_favicon">{tr}Favicon icon file name:{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
							<input type="text" name="site_favicon" id="site_favicon" value="{$prefs.site_favicon}" size="12" maxlength="32" />
						</td>
					</tr>
					<tr>
						<td width=30%>
							<label for="site_favicon_type">{tr}Favicon icon MIME type:{/tr}</label>
						</td>
						<td width=2%>
						</td>
						<td>
							<select name="site_favicon_type" id="site_favicon_type">
								<option value="image/png" {if $prefs.site_favicon_type eq 'image/png'}selected="selected"{/if}>{tr}image/png{/tr}</option>
								<option value="image/bmp" {if $prefs.site_favicon_type eq 'image/bmp'}selected="selected"{/if}>{tr}image/bmp{/tr}</option>
								<option value="image/x-icon" {if $prefs.site_favicon_type eq 'image/x-icon'}selected="selected"{/if}>{tr}image/x-icon{/tr}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div class="adminoptionbox">	  
								<div class="checkbox">
									<input type="checkbox" name="feature_iepngfix" id="feature_iepngfix"{if $prefs.feature_iepngfix eq 'y'} checked="checked"{/if} onclick="flip('iepngfix');" />
								</div>
								<label for="feature_iepngfix">{tr}Correct PNG images alpha transparency in IE6 (experimental){/tr}</label>
								
								<div id="iepngfix" class="adminoptionboxchild" style="display:{if $prefs.feature_iepngfix eq 'y'}block{else}none{/if};">
									<label class="above" for="iepngfix_selectors">{tr}List of CSS selectors to be fixed, each selector separated by comma{/tr}</label>
									<input class="fullwidth" id="iepngfix_selectors" type="text" name="iepngfix_selectors" size="32" value="{$prefs.iepngfix_selectors}" />
									<label class="above" for="iepngfix_elements">{tr}List of HTMLDomElements to be fixed, each element separated by comma{/tr}</label>
									<input class="fullwidth" id="iepngfix_elements" type="text" name="iepngfix_elements" size="32" value="{$prefs.iepngfix_elements}" />
								</div>
							</div>
						</td>
					</tr>
				</table>
				<fieldset>
					<legend>
						<span>{tr}Context Menus (only in file galleries so far){/tr}</span>
					</legend>
					<table>
						<tr>
							<td width=40%>
								<label for="use_context_menu_icon">{tr}Use context menus for actions (icons){/tr}</label>
							</td>
							<td width=2%>
							</td>
							<td>
								<input type="checkbox" id="use_context_menu_icon" name="use_context_menu_icon" {if $prefs.use_context_menu_icon eq 'y'}checked="checked"{/if} />
							</td>
						</tr>
						<tr>
							<td width=40%>
								<label for="use_context_menu_text">{tr}Use context menus for actions (text){/tr}</label>
							</td>
							<td width=2%>
							</td>
							<td>
								<input type="checkbox" id="use_context_menu_text" name="use_context_menu_text" {if $prefs.use_context_menu_text eq 'y'}checked="checked"{/if}/>
							</td>
						</tr>
					</table>
				</fieldset>
			</fieldset>
			{if $prefs.feature_tabs neq 'y'}</div>{/if}
		</fieldset>
		
		<div class="input_submit_container clear" style="text-align: center"><input type="submit" name="looksetup" value="{tr}Apply{/tr}" /></div>
	</form>
</div><!-- cbox end -->
{/strip}
