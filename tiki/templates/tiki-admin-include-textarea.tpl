<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	<div class="rbox-data" name="tip">
	
	Text area (that apply throughout many features)

	</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>


      <form action="tiki-admin.php?page=textarea" method="post">
        <table class="admin">
        <tr>
   
   		<td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Smiley" target="tikihelp" class="tikihelp" title="{tr}Allow Smileys{/tr}">{/if} {tr}Allow Smileys{/tr} {if $feature_help eq 'y'}</a>{/if} </td>
        	<td><input type="checkbox" name="feature_smileys" {if $feature_smileys eq 'y'}checked="checked"{/if}/></td>
       	</tr>
	<tr>
		<td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=AutoLinks" target="tikihelp" class="tikihelp" title="{tr}AutoLinks{/tr}">{/if} {tr}AutoLinks{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_autolinks" {if $feature_autolinks eq 'y'}checked="checked"{/if}/></td>
       	</tr>
	<tr>
	        <td class="form"> <label for="general-ext_links">{tr}Open external links in new window{/tr}:</label></td>
	        <td><input type="checkbox" name="popupLinks" id="general-ext_links" {if $popupLinks eq 'y'}checked="checked"{/if}/></td>
       	</tr>
	<tr>	
		
          <td colspan="2" class="button"><input type="submit" name="textareasetup" value="{tr}Save{/tr}" /></td>
		  
        </tr>
        </table>
      </form>

</div>

