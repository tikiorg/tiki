<script type="text/javascript">
{literal}

  function previous_site_style() {
	var select = document.getElementById('general-theme');
	if (select.selectedIndex > 0) {
		select.selectedIndex--;
	}
  }

  function next_site_style() {
	var select = document.getElementById('general-theme');
	if (select.selectedIndex < select.length-1) {
		select.selectedIndex++;
	}
  }

{/literal}
</script>

<div class="rbox">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
	<div class="rbox-data" name="tip">
	{tr}Please also see:{/tr}
	<a class="rbox-link" href="tiki-admin.php?page=siteid">{tr}Site Identity{/tr}</a>
	</div>
</div>


<div class="cbox">
  <div class="cbox-title">
    <h3>{tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}</h3>
  </div>


      <form action="tiki-admin.php?page=theme" method="post">
      <fieldset>
      <legend class="heading">{tr}Theme{/tr}</legend>
        <table class="admin">
      
		<tr>
        <td class="form" ><label for="general-theme">{tr}Theme{/tr}:</label></td>
        <td width="67%"><select name="site_style" id="general-theme">
            {section name=ix loop=$styles}
              <option value="{$styles[ix]|escape}"
                {if $prefs.site_style eq $styles[ix]}selected="selected"{/if}>
                {$styles[ix]}</option>
            {/section}
            </select>
            <input type="submit" name="style" value="{tr}Change style only{/tr}" />
        </td>
      </tr><tr>
        <td class="form"><label for="general-slideshows">{tr}Slideshows theme{/tr}:</label></td>
        <td><select name="slide_style" id="general-slideshows">
            {section name=ix loop=$slide_styles}
              <option value="{$slide_styles[ix]|escape}"
                {if $prefs.slide_style eq $slide_styles[ix]}selected="selected"{/if}>
                {$slide_styles[ix]}</option>
            {/section}
            </select>
        </td>
	</tr>	<tr>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=CssEditDev" target="tikihelp" class="tikihelp" title="{tr}Edit CSS{/tr}">{/if} {tr}Edit CSS{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
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
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=EditTemplatesDoc" target="tikihelp" class="tikihelp" title="{tr}Template Viewing{/tr}">{/if} {tr}Tiki Template Viewing{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_view_tpl" {if $prefs.feature_view_tpl eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=EditTemplatesDoc" target="tikihelp" class="tikihelp" title="{tr}Edit Templates{/tr}">{/if} {tr}Edit Templates{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_edit_templates" {if $prefs.feature_edit_templates eq 'y'}checked="checked"{/if}/>
		{if $prefs.feature_edit_templates eq 'y'}<a href="tiki-edit_templates.php" class="link" title="{tr}Edit Templates{/tr}">{tr}Edit Templates{/tr}</a>{/if} </td>
	</tr>
        </table>
       </fieldset> 

{* --- General Layout options --- *}
<fieldset class="admin">
	<legend class="heading">{tr}General Layout options{/tr}</legend>
	<table class="admin" width="100%">
		<tr>
        <td class="form">
	        	{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">{/if}
        		{tr}Left column{/tr}{if $prefs.feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><select name="feature_left_column">
            <option value="y" {if $prefs.feature_left_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
            <option value="user" {if $prefs.feature_left_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
            <option value="n" {if $prefs.feature_left_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
        </select></td>
        <td>&nbsp;</td>
        <td class="form">{tr}Layout per section{/tr}</td>
        <td><input type="checkbox" name="layout_section"
            {if $prefs.layout_section eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">{/if}
        		{tr}Right column{/tr}
        		{if $prefs.feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><select name="feature_right_column">
            <option value="y" {if $prefs.feature_right_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
            <option value="user" {if $prefs.feature_right_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
            <option value="n" {if $prefs.feature_right_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
        </select></td>
        <td>&nbsp;</td>
        <td align="center" colspan="2"><a href="tiki-admin_layout.php" 
            class="link">{tr}Admin layout per section{/tr}</a></td>
      </tr><tr>
        <td class="form">{tr}Top bar{/tr}</td>
        <td><input type="checkbox" name="feature_top_bar"
            {if $prefs.feature_top_bar eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
        <td class="form">{tr}Bottom bar{/tr}</td>
        <td><input type="checkbox" name="feature_bot_bar"
            {if $prefs.feature_bot_bar eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
      <td class="form">{tr}Bottom bar icons{/tr}</td>
        <td><input type="checkbox" name="feature_bot_bar_icons"
            {if $prefs.feature_bot_bar_icons eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
        <td class="form">{tr}Bottom bar debug{/tr}</td>
        <td><input type="checkbox" name="feature_bot_bar_debug"
	    {if $prefs.feature_bot_bar_debug eq 'y'}checked="checked"{/if}/></td>
	<td colspan="3">&nbsp;</td>
      </tr><tr>
        <td class="form">{tr}Bottom bar{/tr} (RSS)</td>
        <td><input type="checkbox" name="feature_bot_bar_rss"
	    {if $prefs.feature_bot_bar_rss eq 'y'}checked="checked"{/if}/></td>
	<td colspan="3">&nbsp;</td>
      </tr>    <tr>
    	<td class="form">{tr}Use Tabs{/tr}</td>
        <td><input type="checkbox" name="feature_tabs" {if $prefs.feature_tabs eq 'y'}checked="checked"{/if}/></td>
	<td colspan="3">&nbsp;</td>
    </tr>
      </table>
</fieldset>
<div class="button" style="text-align: center"><input type="submit" name="themesetup" value="{tr}Save{/tr}" /></div>
        

	</form>

</div>
