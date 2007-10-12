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

<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	<div class="rbox-data" name="tip">
	
	
	Please also see:
	<a class="rbox-link" href="tiki-admin.php?page=siteid">{tr}Site Identity{/tr}</a>
	
	

	</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>


      <form action="tiki-admin.php?page=theme" method="post">
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
            &nbsp;<a href="javascript:previous_site_style();" title="{tr}Prev{/tr}"><img src="img/icons2/nav_dot_right.gif" alt="&#9665;" height="11" width="8" border="0" /></a>
            <a href="javascript:next_site_style();" title="{tr}Next{/tr}"><img src="img/icons2/nav_dot_left.gif" alt="&#9655" height="11" width="8" border="0" /></a>&nbsp;
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
	</tr><tr>
	<td class="form" >&nbsp;</td><td>{if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}<a href="tiki-edit_css.php" class="link" title="{tr}Edit CSS{/tr}">{tr}Edit CSS{/tr}</a>{/if}</td>
      </tr>
      
      <tr><td colspan="2"><hr/></td></tr>        
    <tr>
    	<td class="form">{tr}Use Tabs{/tr}</td>
        <td><input type="checkbox" name="feature_tabs" {if $prefs.feature_tabs eq 'y'}checked="checked"{/if}/></td>
    </tr>
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
	<tr>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=CssEditDev" target="tikihelp" class="tikihelp" title="{tr}Edit CSS{/tr}">{/if} {tr}Edit CSS{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_editcss" {if $prefs.feature_editcss eq 'y'}checked="checked"{/if}/>
		{if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}<a href="tiki-edit_css.php" class="link" title="{tr}Edit CSS{/tr}">{tr}Edit CSS{/tr}</a>{/if} </td>
	</tr>
	<tr>

		
          <td colspan="2" class="button"><input type="submit" name="themesetup" value="{tr}Save{/tr}" /></td>
		  
        </tr>
        </table>
      </form>

</div>

