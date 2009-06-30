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
<li>In the Video Demo Tab you can view a few screencast explanining different things related to Workspaces</li>
<li>In the Workspaces Dev Tools Tab, you can initiate (and other stuff) a set of Workspaces for quick look.</li>
</ul>
<strong>All sections listed here, are only during the development phase!</strong>{/tr}
{/remarksbox}
{*/if*}

<div class="cbox">
<form action="tiki-admin.php?page=fgal" method="post">

{tabset name="fgal_admin"}
	{tab name="{tr}Test{/tr}"}

<input type="hidden" name="filegalfeatures" />

<fieldset><legend>{tr}Info{/tr}</legend>

</fieldset>


{/tab}

{tab name="{tr}Demo{/tr}"}

{/tab}

{tab name="{tr}Workspaces Dev Tools{/tr}"}
{/tab}
{/tabset}

</form>
</div>
