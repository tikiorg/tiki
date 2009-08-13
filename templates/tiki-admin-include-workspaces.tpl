{* $Id: $ *}

{remarksbox type="info" title="{tr}Welcome{/tr}"}
{tr}You have succesfully enabled  Workspaces in TikiWiki. <br /><br />
This feature is <strong>under development</strong>. If you want to get more info go to the {/tr} <a class="rbox-link" href="http://dev.tikiwiki.org/workspace">{tr}Workspaces wiki page{/tr}</a>.
<hr />
{tr}Things you can currently do: <br />
<ul>
<li>In the General Settings Tab you can configure certain aspects of Workspaces (not functional right now).</li>
<li>In the Workspaces Dev Tools Tab, you can initiate (and other stuff) a set of Workspaces.</li>
</ul>
<strong>These sections listed here could change in the future!</strong>{/tr}
{/remarksbox}

<div class="cbox">
<form action="tiki-admin.php?page=workspaces" method="post">

{tabset name="ws_admin"}
	{tab name="{tr}General Settings{/tr}"}
{/tab}

{tab name="{tr}Workspaces Dev Tools{/tr}"}
<fieldset><legend>{tr}Script Creator{/tr}</legend>
<div class="adminoptionbox">
<input type="radio" id="wscreate" name="wsdevtools" value="create" checked="checked" /><label for="wscreate">{tr}Create a set of Workspaces, and some items whithin them{/tr}.</label>
</div>
<div class="adminoptionbox">
<input type="radio" id="clearcache" name="wsdevtools" value="clearcache" /><label for="clearcache">{tr}Clear the entire cache of TikiWiki{/tr}.</label>
</div>
<div class="adminoptionbox">
<input type="radio" id="wsdelete" name="wsdevtools" value="delete" /><label for="wsdelete">{tr}Delete all Workspaces created before{/tr}.</label>
</div>
<div class="adminoptionbox">
<input type="radio" id="wsdeleteall" name="wsdevtools" value="deleteall" /><label for="wsdeleteall">{tr}Delete all Workspaces including the WS container (Not working){/tr}.</label>
</div>
</fieldset>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><div align="center"><input type="submit" name="wsoptions" value="{tr}Apply{/tr}"/></div></div>
</div>
{/tab}
{/tabset}

</form>
</div>
