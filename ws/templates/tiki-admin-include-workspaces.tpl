{* $Id: $ *}

{*if $welcome*}
{remarksbox type="info" title="{tr}Welcome{/tr}"}
{tr}You have succesfully enabled  Workspaces in TikiWiki. This feature is under development, so please don't expect nothing if not works properly. If you want to
get more info go to the {/tr} <a class="rbox-link"
href="http://dev.tikiwiki.org/workspace">{tr}Workspaces wiki page{/tr}</a>.
<hr />
{tr}Things you can currently do: <br />
<ul>
<li>In the Test Tab you can view the Workspaces and their items.</li>
<li>In the Video Tab you can view a few screencast explanining different things related to Workspaces (soon).</li>
<li>In the Workspaces Dev Tools Tab, you can initiate (and other stuff) a set of Workspaces.</li>
</ul>
<strong>All these sections listed here will be present only during the development!</strong>{/tr}
{/remarksbox}
{*/if*}

<div class="cbox">
<form action="tiki-admin.php?page=workspaces" method="post">

{tabset name="ws_admin"}
	{tab name="{tr}Test{/tr}"}

<input type="hidden" name="filegalfeatures" />

<fieldset><legend>{tr}Info{/tr}</legend>
<div class="adminoptionbox">
<ul>
<li>Status: {if $prefs.feature_workspaces eq 'y'} <strong>Workspaces are enabled</strong> {else}<strong>Workspaces are disabled</strong>{/if}</li>
<li>Workspaces Container Id: {$prefs.ws_container}</li>
</ul>
</div>
</fieldset>

<fieldset><legend>Create a WS</legend>
<div class="adminoptionbox">

<label for="wsname">Workspace Name: </label><input type="text" name="wsname" />

<br /><br />

<input type="radio" name="groupelection" id="selectgroup" checked="checked" /><label for="selectgroup">Select an group to add to the workspace:</label>
<select name="selgroup">
{foreach from=$groups.data key=k item=v }
<option value="{$groups.data.$k.id}">{$groups.data.$k.groupName}</option>
{/foreach}
</select>
<br /><br />
<input type="radio" name="groupelection" id="creategroup" /><label for="creategroup">Create a new group to add to the workspace:</label><input type="text" name="groupname" />
<br /><br />
<input type="checkbox" name="resourceforws" id="resource" checked="checked" disabled="disabled" /><label for="resource">Create a wiki page inside this workspace</label>

</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><div align="center"><input type="submit" name="wscreate" value="{tr}Create{/tr}"/></div></div>
</div>
</fieldset>

<fieldset><legend>{tr}Inspector{/tr}</legend>
<div class="adminoptionbox">
{if $catree}

<ul>
{foreach from=$catree.data key=k item=v}
   <li>{$catree.data.$k.categId} - {$catree.data.$k.name}</li>
{/foreach}
</ul>

{/if}
</div>
</fieldset>
{/tab}

{tab name="{tr}Video{/tr}"}
<div class="adminoptionbox">
<ul>
<li>Watch this section in the next days, there will be interesting things here ;)</li>
</ul>
</div>
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
