<a name="trackers"></a>
{include file="tiki-admin-include-anchors-empty.tpl"}
<div class="cbox">
<div class="cbox-title">{tr}Trackers{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="t_use_db" value="y" {if $t_use_db eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td><input type="radio" name="t_use_db" value="n" {if $t_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<input type="text" name="t_use_dir" value="{$t_use_dir}" /> </tr>
<tr><td align="center" colspan="2"><input type="submit" name="trkset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>

