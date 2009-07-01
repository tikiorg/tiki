{* $Id: $ *}

{*if $welcome*}
{remarksbox type="info" title="{tr}Welcome{/tr}"}
{tr}You have enabled succesfully Workspaces in TikiWiki. This feature is
in heavily development, so don't expect to work properly. If you want to
get more info, please go to {/tr} <a class="rbox-link"
href="http://dev.tikiwiki.org/workspace">{tr}Workspaces page{/tr}</a>.
<hr />
{tr}Things you can do currently: <br />
<ul>
<li>In the Test Tab you can view the Workspaces and its items.</li>
<li>In the Video Tab you can view a few screencast explanining different things related to Workspaces (soon).</li>
<li>In the Workspaces Dev Tools Tab, you can initiate (and other stuff) a set of Workspaces for quick look.</li>
</ul>
<strong>All sections listed here, are only during the development phase!</strong>{/tr}
{/remarksbox}
{*/if*}

<div class="cbox">
<form action="tiki-admin.php?page=workspaces" method="post">

{tabset name="fgal_admin"}
	{tab name="{tr}Test{/tr}"}

<input type="hidden" name="filegalfeatures" />




{/tab}

{tab name="{tr}Video{/tr}"}
<div class="adminoptionbox">
<ul>
<li>Some useful videos: <br /></li>
</ul>
</div>
{/tab}

{tab name="{tr}Workspaces Dev Tools{/tr}"}
<fieldset><legend>{tr}Info{/tr}</legend>
<div class="adminoptionbox">
<ul>
<li>Status: {if $prefs.feature_workspaces eq 'y'} <strong>Workspaces are enabled</strong> {else}<strong>Workspaces are disabled</strong>{/if}</li>
<li>Workspaces Container Id: {$prefs.ws_container}</li>
</ul>
</div>
</fieldset>
<fieldset><legend>{tr}Script Creator{/tr}</legend>
<div class="adminoptionbox">
<input type="radio" id="wscreate" name="wsdevtools" value="create" checked="checked" /><label for="wscreate">{tr}Make a set of Workspaces, and some items inside it. NOTE: before you run this you should create 5 wiki pages with the names "Wiki1",
 "Wiki2", "Wiki3", "Wiki4" and "Wiki5", and two groups called "G1" and "G2"{/tr}.</label>
</div>
<div class="adminoptionbox">
<input type="radio" id="wsdelete" name="wsdevtools" value="delete" /><label for="wsdelete">{tr}Delete all Workspaces created before{/tr}.</label>
</div>
</fieldset>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><div align="center"><input type="submit" name="wsoptions" value="{tr}Apply{/tr}"/></div></div>
</div>
{/tab}
{/tabset}

</form>
</div>
