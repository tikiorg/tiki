<div class="cbox">
<div class="cbox-title">{tr}Webmail{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=webmail" method="post">
<table >
<tr><td class="form">{tr}Allow viewing HTML mails?{/tr}:</td><td><input type="checkbox" name="webmail_view_html" {if $webmail_view_html eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Maximum size for each attachment{/tr}:</td><td><select name="webmail_max_attachment">
<option value="500000" {if $webmail_max_attachment eq 500000}selected="selected"{/if}>500Kb</option>
<option value="1000000" {if $webmail_max_attachment eq 1000000}selected="selected"{/if}>1Mb</option>
<option value="1500000" {if $webmail_max_attachment eq 1500000}selected="selected"{/if}>1.5Mb</option>
<option value="2000000" {if $webmail_max_attachment eq 2000000}selected="selected"{/if}>2Mb</option>
<option value="2500000" {if $webmail_max_attachment eq 2500000}selected="selected"{/if}>2.5Mb</option>
<option value="3000000" {if $webmail_max_attachment eq 3000000}selected="selected"{/if}>3Mb</option>
<option value="100000000" {if $webmail_max_attachment eq 100000000}selected="selected"{/if}>Unlimited</option>
</select></tr>
<tr><td align="center" colspan="2"><input type="submit" name="webmail" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>





