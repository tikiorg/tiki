{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-look.tpl,v 1.1.2.15 2008-03-20 12:33:52 sylvieg Exp $ *}

<div class="cbox">
	<div class="cbox-title">
	<h3>{tr}{$crumbs[$crumb]->title}{/tr}
	{help crumb=$crumbs[$crumb]}</h3>
	</div>

	<form action="tiki-admin.php?page=look" class="admin" method="post">
		<div class="heading button" style="text-align: right">
			<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
			<input type="reset" name="looksetupreset" value="{tr}Reset{/tr}" />
		</div>

{if $prefs.feature_tabs eq 'y'}
	{cycle name=tabs values="1,2,3,4" print=false advance=false reset=true}
	<div class="tabs">
		<span	id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" 
				class="tabmark tabinactive"><a 
				href="#theme"
				onclick="javascript:tikitabs({cycle name=tabs},4); return false;">{tr}Theme{/tr}</a></span>
		<span	id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" 
				class="tabmark tabinactive"><a 
				href="#layout"
				onclick="javascript:tikitabs({cycle name=tabs},4); return false;">{tr}General Layout{/tr}</a></span>
		<span	id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" 
				class="tabmark tabinactive"><a 
				href="#other"
				onclick="javascript:tikitabs({cycle name=tabs},4); return false;">{tr}Other{/tr}</a></span>
	</div>
	{cycle name=content values="1,2,3,4" print=false advance=false reset=true}
{/if}

		<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
			{if $prefs.feature_tabs neq 'y'}<legend class="heading"><a 
														href="#theme"
														name="theme"
														onclick="flip('theme'); return false;"><span>{tr}Theme{/tr}</span></a></legend>
			<div id="theme" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_theme) and $smarty.session.tiki_cookie_jar.show_theme neq 'y'}none{else}block{/if};">{/if}
				<table class="admin">
					<tr>
						<td class="form" ><label for="general-theme">{tr}Theme{/tr}:</label></td>
						<td width="67%"><select name="site_style" id="general-theme">
	{section name=ix loop=$styles}
							<option value="{$styles[ix]|escape}"{if $prefs.site_style eq $styles[ix]} selected="selected"{/if}>{$styles[ix]}</option>
	{/section}
							</select></td>
					</tr><tr>
						<td class="form"><label for="general-slideshows">{tr}Slideshows theme{/tr}:</label></td>
						<td><select name="slide_style" id="general-slideshows">
	{section name=ix loop=$slide_styles}
							<option value="{$slide_styles[ix]|escape}"{if $prefs.slide_style eq $slide_styles[ix]} selected="selected"{/if}>{$slide_styles[ix]}</option>
	{/section}
							</select></td>
					</tr><tr>
        				<td class="form"><label for="transition_style_ver">{tr}Use transition style sheet from version{/tr}:</label></td>
						<td><select name="transition_style_ver" id="transition_style_ver">            
							<option value="none" {if $prefs.transition_style_ver eq 'none'}selected="selected"{/if}>{tr}Never use transition css{/tr}</option>
							<option value="css_specified_only" {if $prefs.transition_style_ver eq 'css_specified_only'}selected="selected"{/if}>{tr}Use @version:x.x specified in theme css or none if not specified{/tr}</option>
							<option value="1.8" {if $prefs.transition_style_ver eq '1.8'}selected="selected"{/if}>{tr}Use @version:x.x specified in theme css or 1.8 if not specified{/tr}</option>
							<option value="1.9" {if $prefs.transition_style_ver eq '1.9'}selected="selected"{/if}>{tr}Use @version:x.x specified in theme css or 1.9 if not specified{/tr}</option>
							</select></td>
					</tr><tr>
						<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Edit+CSS"
						target="tikihelp" class="tikihelp" title="{tr}Edit CSS{/tr}">{/if} {tr}Edit CSS{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_editcss" {if $prefs.feature_editcss eq 'y'}checked="checked"{/if}/>
		{if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}<a href="tiki-edit_css.php" class="link" title="{tr}Edit CSS{/tr}">{tr}Edit CSS{/tr}</a>{/if} </td>
	</tr>
      
      <tr><td colspan="2"><hr/></td></tr>        
        <tr>
		<td class="form"> {tr}Theme Control{/tr} </td>
		<td><input type="checkbox" name="feature_theme_control" {if $prefs.feature_theme_control eq 'y'}checked="checked"{/if}/>
		{if $prefs.feature_theme_control eq 'y'}<a href="tiki-theme_control.php" class="link" title="{tr}Theme Control{/tr}">{tr}Theme Control{/tr}</a>{/if}
		</td>
	</tr>
	<tr>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}View+Templates" target="tikihelp" class="tikihelp" title="{tr}Template Viewing{/tr}">{/if} {tr}Tiki Template Viewing{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_view_tpl" {if $prefs.feature_view_tpl eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Edit+Templates" target="tikihelp" class="tikihelp" title="{tr}Edit Templates{/tr}">{/if} {tr}Edit Templates{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_edit_templates" {if $prefs.feature_edit_templates eq 'y'}checked="checked"{/if}/>
		{if $prefs.feature_edit_templates eq 'y'}<a href="tiki-edit_templates.php" class="link" title="{tr}Edit Templates{/tr}">{tr}Edit Templates{/tr}</a>{/if} </td>
	</tr>
	<tr>
        <td class="form" > {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Site+Identity" target="tikihelp" class="tikihelp" title="{tr}Site Identity{/tr}">{/if} {tr}Site Identity{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td ><input type="checkbox" name="feature_siteidentity" {if $prefs.feature_siteidentity eq 'y'}checked="checked"{/if}/> Required for all the following features</td>
	</tr>
       </table>
       {if $prefs.feature_tabs neq 'y'}</div>{/if}
</fieldset> 

{* --- General Layout options --- *}
<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
	{if $prefs.feature_tabs neq 'y'}<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}"><a href="#layout" name="layout" onclick="flip('layout'); return false;"><span>{tr}General Layout options{/tr}</span></a></legend>
	<div id="layout" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_layout) and $smarty.session.tiki_cookie_jar.show_layout neq 'y'}none{else}block{/if};">{/if}
		<table class="admin" width="100%">
			<tr>
        		<td class="form" colspan="5">
        
	{* --- Customize Site Header --- *}
	<fieldset class="admin">
					<legend><a href="#"><span>{tr}Custom Site Header{/tr}</span></a></legend>

			<table class="admin">
				<tr>
					<td class="form"><label for="feature_sitemycode">{tr}Activate{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitemycode" id="feature_sitemycode"{if $prefs.feature_sitemycode eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode">{tr}Content{/tr}:</label></td>
					<td><textarea name="sitemycode" rows="6" style="width: 90%" id="sitemycode">{$prefs.sitemycode|escape}</textarea>
					<br />
					<small><em>{tr}Example{/tr}</em>: 
					{literal}{if $user neq ''}{/literal}&lt;div align="right" style="float: right; font-size: 10px"&gt;{literal}{{/literal}tr}{tr}logged as{/tr}{literal}{/tr}{/literal}: {literal}{$user}{/literal}&lt;/div&gt;{literal}{/if}{/literal}</small></td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode_publish">{tr}Publish{/tr}:</label></td>
					<td><input type="checkbox" name="sitemycode_publish" id="sitemycode_publish"{if $prefs.sitemycode_publish eq 'y'} checked="checked"{/if} /></td>
				</tr>
        	</table>
	</fieldset>

	{* --- Customize Site Logo --- *}
	<fieldset>
					<legend><a href="#"><span>{tr}Site Logo{/tr}</span></a></legend>

				<table class="admin">
				
				<tr>
					<td class="form"><label for="feature_sitelogo">{tr}Activate{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitelogo" id="feature_sitelogo"{if $prefs.feature_sitelogo eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_src">{tr}Site logo source (image path){/tr}:</label></td>
					<td><input type="text" name="sitelogo_src" id="sitelogo_src" value="{$prefs.sitelogo_src}" size="60" style="width: 90%" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_bgcolor">{tr}Site logo background color{/tr}:</label></td>
					<td><input type="text" name="sitelogo_bgcolor" id="sitelogo_bgcolor" value="{$prefs.sitelogo_bgcolor}" size="15" maxlength="15" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_align">{tr}Site logo alignment{/tr}:</label></td>
					<td><select name="sitelogo_align" id="sitelogo_align">
						<option value="left" {if $prefs.sitelogo_align eq 'left'}selected="selected"{/if}>{tr}on left side{/tr}</option>
						<option value="center" {if $prefs.sitelogo_align eq 'center'}selected="selected"{/if}>{tr}on center{/tr}</option>
						<option value="right" {if $prefs.sitelogo_align eq 'right'}selected="selected"{/if}>{tr}on right side{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_title">{tr}Site logo title (on mouse over){/tr}:</label></td>
					<td><input type="text" name="sitelogo_title" id="sitelogo_title" value="{$prefs.sitelogo_title}" size="50" maxlength="50" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_alt">{tr}Alt. description (e.g. for text browsers){/tr}:</label></td>
					<td><input type="text" name="sitelogo_alt" id="sitelogo_alt" value="{$prefs.sitelogo_alt}" size="50" maxlength="50" /></td>
				</tr></table>
</fieldset>                                

	{* --- Site Search Bar --- *}
        <fieldset>
        					<legend><a href="#"><span>{tr}Site Search Bar{/tr}</span></a></legend>

				<table class="admin">

				<tr> 
                                        <td class="form"><label for="feature_sitesearch">{tr}Activate{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_sitesearch" id="feature_sitesearch"{if $prefs.feature_sitesearch eq 'y'} checked="checked"{/if} /></td>
                                </tr>
                                
                                </table>
</fieldset>                                

	{* --- Top Bar --- *}
        <fieldset>
					<legend><a href="#"><span><input type="checkbox" name="feature_top_bar"
            {if $prefs.feature_top_bar eq 'y'}checked="checked"{/if}/> {tr}Top Bar{/tr}</span></a></legend>

				<table class="admin">

				<tr> 
                                        <td class="form"><label for="feature_sitemenu">{tr}Site menu bar{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_sitemenu" id="feature_sitemenu"{if $prefs.feature_sitemenu eq 'y'} checked="checked"{/if} />{tr}Note: This feature also requires PHPLayers to be turned on in {/tr}{tr}Admin{/tr}&nbsp;{$prefs.site_crumb_seper}&nbsp;{tr}Features{/tr}</td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_id_menu">{tr}Menu ID{/tr}:</label></td>
                                        <td><input type="text" name="feature_topbar_id_menu" id="feature_topbar_id_menu" value="{$prefs.feature_topbar_id_menu}" size="6" maxlength="6" /></td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_version">{tr}Current Version{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_topbar_version" id="feature_topbar_version"{if $prefs.feature_topbar_version eq 'y'} checked="checked"{/if} /></td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_date">{tr}Date{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_topbar_date" id="feature_topbar_date"{if $prefs.feature_topbar_date eq 'y'} checked="checked"{/if} /></td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_debug">{tr}Debugger Console{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_topbar_debug" id="feature_topbar_debug"{if $prefs.feature_topbar_debug eq 'y'} checked="checked"{/if} /></td>
                                </tr></table>
</fieldset>                                

        
	        	</td>
        
	</tr>      
	<tr>
	
        <td><fieldset>
					<legend><a href="#"><span>{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">{/if}
        		{tr}Left column{/tr}{if $prefs.feature_help eq 'y'}</a>{/if}
        		:</span></a></legend>
        <select name="feature_left_column">
            <option value="y" {if $prefs.feature_left_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
            <option value="user" {if $prefs.feature_left_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
            <option value="n" {if $prefs.feature_left_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
        </select>
</fieldset>
        </td><td class="form" colspan="3">
        
	{* --- Site Breadcrumbs --- *}
<fieldset class="admin">
					<legend><a href="#"><span>{tr}Site Breadcrumbs{/tr}</span></a></legend>
			<table class="admin">
				<tr>
				    <td class="form"><label for="feature_breadcrumbs">{tr}Activate{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_breadcrumbs" id="feature_breadcrumbs"{if $prefs.feature_breadcrumbs eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
                			<td class="form"><label for="feature_siteloc">{tr}Site location bar{/tr}:</label></td>
					<td><select name="feature_siteloc" id="feature_siteloc">
					<option value="y" {if $prefs.feature_siteloc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
					<option value="page" {if $prefs.feature_siteloc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
					<option value="n" {if $prefs.feature_siteloc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
					</select></td>
				</tr>
				<tr>
					<td class="form"><label for="feature_siteloclabel">{tr}Prefix breadcrumbs with 'Location : ' {/tr}</label></td>
					<td><input type="checkbox" name="feature_siteloclabel" id="feature_siteloclabel"{if $prefs.feature_siteloclabel eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
                                        <td class="form"><label for="feature_sitetitle">{tr}Larger font for{/tr}:</label></td>
                                        <td><select name="feature_sitetitle" id="feature_sitetitle">
                                        <option value="y" {if $prefs.feature_sitetitle eq 'y'}selected="selected"{/if}>{tr}entire location{/tr}</option>
                                        <option value="title" {if $prefs.feature_sitetitle eq 'title'}selected="selected"{/if}>{tr}page name{/tr}</option>
                                        <option value="n" {if $prefs.feature_sitetitle eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
                                        </select></td>
				</tr>
                                <tr>
                                        <td class="form"><label for="feature_sitedesc">{tr}Use page description:{/tr}</label></td>
					<td><select name="feature_sitedesc" id="feature_sitedesc">
					<option value="y" {if $prefs.feature_sitedesc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
					<option value="page" {if $prefs.feature_sitedesc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
					<option value="n" {if $prefs.feature_sitedesc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
					</select></td>
                                </tr>
                                </table>

</fieldset>                                
</td>      



        
        <td class="form">
      
      
	        	<fieldset>
					<legend><a href="#"><span>{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">{/if}
        		{tr}Right column{/tr}{if $prefs.feature_help eq 'y'}</a>{/if}
        		:</span></a></legend>
        		<select name="feature_right_column">
            <option value="y" {if $prefs.feature_right_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
            <option value="user" {if $prefs.feature_right_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
            <option value="n" {if $prefs.feature_right_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
        </select>
</fieldset>
      </td>
        
      </tr>
      <tr><td colspan="5">
      <fieldset>
					<legend><a href="#"><span>{tr}Custom Site Footer{/tr}</span></a></legend>

				<table class="admin">
				<tr>
					<td class="form"><label for="feature_bot_logo">{tr}Activate{/tr}:</label></td>
                                          <td><input type="checkbox" name="feature_bot_logo" id="feature_bot_logo"{if $prefs.feature_bot_logo eq 'y'} checked="checked"{/if} /></td>
				</tr>

				<tr>
					<td class="form"><label for="bot_logo_code">{tr}Content{/tr}:</label></td>
					<td><textarea name="bot_logo_code" rows="6" style="width: 90%">{$prefs.bot_logo_code|escape}</textarea>
					<br />
					<small><em>{tr}Example{/tr}</em>:&lt;div style="text-align: center"&gt;&lt;small&gt;Powered by Tikiwiki&lt;/small&gt;&lt;/div&gt;</small></td>
				</tr>
				
			</table>
</fieldset></td>
      </tr>
      <tr><td colspan="5" class="form">
        <fieldset>
            <legend><a href="#"><span><input type="checkbox" name="feature_bot_bar"
            {if $prefs.feature_bot_bar eq 'y'}checked="checked"{/if}/> {tr}Bottom bar{/tr}</span></a></legend>
            {tr}Bottom bar icons{/tr} <input type="checkbox" name="feature_bot_bar_icons"
            {if $prefs.feature_bot_bar_icons eq 'y'}checked="checked"{/if}/> | {tr}Bottom bar debug{/tr} <input type="checkbox" name="feature_bot_bar_debug"
	    {if $prefs.feature_bot_bar_debug eq 'y'}checked="checked"{/if}/> | {tr}Bottom bar{/tr} (RSS) <input type="checkbox" name="feature_bot_bar_rss"
	    {if $prefs.feature_bot_bar_rss eq 'y'}checked="checked"{/if}/>
        </fieldset>
      </td>
    </tr><tr>
        <td colspan="5" class="form">
          <fieldset>
            <legend><a href="#"><span>{tr}Pagination links{/tr}</span></a></legend>
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
            <input type="checkbox" name="pagination_icons" id="pagination_icons" {if $prefs.pagination_icons eq 'y'}checked="checked"{/if}/>
            <label for="pagination_icons">{tr}Use Icons{/tr}</label><br />
          </fieldset>
        </td>
    </tr><tr>
        <td class="form" colspan="5"><input type="checkbox" name="feature_menusfolderstyle" id="general-menu_folders"
              {if $prefs.feature_menusfolderstyle eq 'y'}checked="checked"{/if}/>
        <label for="general-menu_folders">{tr}Display menus as folders{/tr}</label>
        </td>
    </tr><tr>
        <td class="form" colspan="5"><input type="checkbox" name="feature_tabs" id="general-feature_tabs" {if $prefs.feature_tabs eq 'y'}checked="checked"{/if}/>
    	<label for="general-feature_tabs">{tr}Use Tabs{/tr}</label>
        </td>
    </tr>
    <tr>
        <td class="form" colspan="2"><input type="checkbox" name="layout_section" id="general-layout_section" {if $prefs.layout_section eq 'y'}checked="checked"{/if}/>
        <label for="general-layout_section">{tr}Layout per section{/tr}</label>
        </td>
        <td style="text-align:right" colspan="3"><a href="tiki-admin_layout.php" class="link">{tr}Admin layout per section{/tr}</a></td>
      </tr>
    
    
		</table>
	       {if $prefs.feature_tabs neq 'y'}</div>{/if}
</fieldset>

<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
	{if $prefs.feature_tabs neq 'y'}<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}"><a href="#other" name="other" onclick="flip('other'); return false;"><span>{tr}Other options{/tr}</span></a></legend>
	<div id="other" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_other) and $smarty.session.tiki_cookie_jar.show_other neq 'y'}none{else}block{/if};">{/if}
		<table class="admin" width="100%">
				<tr>
					<td class="form"><label for="use_context_menu_icon">{tr}Use context menus for actions (icons) (only in file galleries yet):{/tr}</label></td>
					<td><input type="checkbox" id="use_context_menu_icon" name="use_context_menu_icon" {if $prefs.use_context_menu_icon eq 'y'}checked="checked"{/if}/>
				</tr>
				<tr>
					<td class="form"><label for="use_context_menu_text">{tr}Use context menus for actions (text) (only in file galleries yet):{/tr}</label></td>
					<td><input type="checkbox" id="use_context_menu_text" name="use_context_menu_text" {if $prefs.use_context_menu_text eq 'y'}checked="checked"{/if}/>
				</tr>
				<tr>
					<td class="form"><label for="site_favicon">{tr}Favicon icon file name:{/tr}</label></td>
					<td><input type="text" name="site_favicon" id="site_favicon" value="{$prefs.site_favicon}" size="12" maxlength="32" /></td>
				</tr>
				<tr>
					<td class="form"><label for="site_favicon_type">{tr}Favicon icon MIME type:{/tr}</label></td>
					<td>
						<select name="site_favicon_type" id="site_favicon_type">
							<option value="image/png" {if $prefs.site_favicon_type eq 'image/png'}selected="selected"{/if}>{tr}image/png{/tr}</option>
							<option value="image/bmp" {if $prefs.site_favicon_type eq 'image/bmp'}selected="selected"{/if}>{tr}image/bmp{/tr}</option>
							<option value="image/x-icon" {if $prefs.site_favicon_type eq 'image/x-icon'}selected="selected"{/if}>{tr}image/x-icon{/tr}</option>
						</select>
					</td>
				</tr>
			</table>
	{if $prefs.feature_tabs neq 'y'}</div>{/if}
</fieldset>

		<div class="button" style="text-align: center"><input type="submit" name="looksetup" value="{tr}Apply{/tr}" /></div>
	</form>
</div><!-- cbox end -->
