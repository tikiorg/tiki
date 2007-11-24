{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-forum_import.tpl,v 1.1.2.1 2007-11-24 19:39:25 kerrnel22 Exp $ *}

<h1><a class="pagetitle" href="tiki-forum_import.php">{tr}Forum Importer{/tr}</a>
</h1>
<a title="{tr}Admin Forums{/tr}" href="tiki-admin_forums.php" class="linkbut">{tr}Admin forums{/tr}</a>
<a title="{tr}Configure/Options{/tr}" href="tiki-admin.php?page=forums"><img src="img/icons/config.gif"
border="0" width="16" height="16" alt='{tr}Configure/Options{/tr}' /></a>
<a title="{tr}List Forums{/tr}" href="tiki-forums.php" class="linkbut">{tr}List forums{/tr}</a>
<p> </p>

{*
 * If this is a new import, start by selecting the import method and we'll
 * go from there. 
 *}
{if $step eq 'new'}
{* This part of the tool is not ready yet, so let's hide it for now...

<div class="cbox">
<div class="cbox-title">{tr}Import Forum Contents from Tiki's DB and Server{/tr}</div>
<div class="cbox-data">
	<form action="tiki-forum_import.php" method="post">
	<input type="hidden" name="step1" value="true">
	<input type="hidden" name="import" value="same">
	<table class="normal">
		<tr><td class="formcolor">{tr}Forum Type{/tr}:</td><td class="formcolor">
		<select name="forum">
		
		{cycle values="odd,even" print=false}
		{section name=ftype loop=$fi_types}
		<option value="{$fi_types[ftype]}">{$fi_types[ftype]}</option>
		{/section}

		</select>
		</td></tr>
		<tr><td class="formcolor">{tr}DB Prefix{/tr}:</td><td class="formcolor">
		<input type="text" name="prefix" value="{$fi_prefixes[0]}" />
		</td></tr>
	</table>
	<br />
	<div align="center">
		<input type="submit" value="Get Forum List">
	</div>
	<br />
	</form>
</div>
</div>
<br />
<div class="cbox">
<div class="cbox-title">{tr}Import from Another DB or Server{/tr}</div>
<div class="cbox-data">
	<form action="tiki-forum_import.php" method="post">
	<input type="hidden" name="step1" value="true">
	<input type="hidden" name="import" value="other">
	<table class="normal">
		<tr><td class="formcolor">{tr}Server{/tr}:</td><td class="formcolor">
		<input type="text" name="server" />
		</td><td class="formcolor">{tr}DB Name{/tr}:</td><td class="formcolor">
		<input type="text" name="dbname" />
                </td></tr>
		<tr><td class="formcolor">{tr}Forum Type{/tr}:</td><td class="formcolor">
		<select name="forum">

		</select>
		</td><td class="formcolor">{tr}DB Prefix{/tr}:</td><td class="formcolor">
		<input type="text" name="prefix" />
		</td></tr>
		<tr><td class="formcolor">{tr}Username{/tr}:</td><td class="formcolor">
		<input type="text" name="username" />
		</td><td class="formcolor">{tr}Password{/tr}:</td><td class="formcolor">
		<input type="text" name="password" />
                </td></tr>
	</table>
	<br />
	<div align="center">
		<input type="submit" value="Test Connection">
	</div>
	<br />
	</form>
</div>
</div>
<div align="center">
	<b>...OR...</b>
</div>
<br />

End hiding of unfinished section... *}
<div class="cbox">
<div class="cbox-title">{tr}Import from a Local SQL File{/tr}</div>
<div class="cbox-data">
	<form action="tiki-forum_import.php" method="post">
	<input type="hidden" name="step1" value="true">
	<input type="hidden" name="import" value="sql">
	<table class="normal">
		<tr><td class="formcolor">{tr}Forum Type{/tr}:</td><td class="formcolor">
		<select name="ftype">
		
		{* List all forums that are supported. *}
		{cycle values="odd,even" print=false}
		{section name=ftype loop=$fi_types}
		<option value="{$fi_types[ftype]}">{$fi_types[ftype]}</option>
		{/section}

		</select>
		</td></tr>
		<tr><td class="formcolor">{tr}DB Prefix{/tr}:</td><td class="formcolor">
		<input type="text" name="prefix" value="{$fi_prefixes[0]}" />
		</td></tr>
		<tr><td class="formcolor">{tr}Local SQL Filename on Server (path will be stripped){/tr}:<br />
		<i>{tr}Must be in tikiroot/{$tmpdir} or tikiroot/img/wiki_up{/tr}</i></td><td class="formcolor">
		<input type="text" name="server" />
	<br />
	</table>
	<div align="center">
		<input type="submit" value="Get Forum List">
	</div>
	<br />
	</form>
</div>
</div>

{elseif $step eq 'test'}

<div class="cbox">
<div class="cbox-title">{tr}Verification{/tr}</div>
<div class="cbox-data">
	<form action="tiki-forum_import.php" method="post">
	{if $passed eq 'true'}
	  <input type="hidden" name="step2" value="true">
	{else}
	  <input type="hidden" name="step0" value="true">
	{/if}
	<input type="hidden" name="import" value="{$iMethod}">
	<input type="hidden" name="ftype" value="{$fi_type}">
	<input type="hidden" name="prefix" value="{$fi_prefix}">
	<input type="hidden" name="server" value="{$server}">

	{tr}<b>File must be an SQL file and exist in either <i>$tikiroot/temp</i> or <i>$tikiroot/img/wiki_up</i>.</b>{/tr}
	<p> </p>
	{if $passed eq 'true'}
	    File found: {$server}.
	{else}
	  {tr}File was not specified, or could not be found in either location.  Please put the file in either directory and Go Back.  If the file exists in both locations, the $tikiroot/{$filecheck} location will be preferred.{/tr}
	{/if}

	<p> </p>
	<div align="center">
		{if $passed eq 'true'}
		  <input type="submit" value="Proceed">
		{else}
		  <input type="submit" value="Go Back">
		{/if}
	</div>
	<br />
	</form>
</div>
</div>

{elseif $step eq 'select'}

<form action="tiki-forum_import.php" method="post">
<div class="cbox">
<div class="cbox-title">{tr}Select a Forum You Wish to Move (ONE at a time!){/tr}</div>
<div class="cbox-data">
	<input type="hidden" name="step3" value="true">
	<input type="hidden" name="import" value="{$iMethod}">
	<input type="hidden" name="ftype" value="{$fi_type}">
	<input type="hidden" name="prefix" value="{$fi_prefix}">
	<input type="hidden" name="server" value="{$server}">

	<table class="normal">
	<tr>
	<th>Select</th>
	<th>Forum Name</th>
	<th>Posts</th>
	</tr>

	{cycle values="odd,even" print=false}
	{section name=fforum loop=$fromForums}
	<tr>
	<td class="{cycle advance=false}"><input type="radio" name="fForumid" value="{$fromForums[fforum].id}"></td>
	<td class="{cycle advance=false}">{$fromForums[fforum].name}</td>
	<td class="{cycle advance=true}">{$fromForums[fforum].comments}</td>
	</tr>
	{/section}
	</table>
</div>
</div>
<p> </p>
<div class="cbox">
<div class="cbox-title">{tr}Which Forum Do You Wish to Import this Into?{/tr}</div>
<div class="cbox-data">
	<table class="normal">
	<tr>
	<th>Select</th>
	<th>Forum Name</th>
	<th>Posts</th>
	</tr>

	{cycle values="odd,even" print=false}
	{section name=tforum loop=$toForums}
	<tr>
	<td class="{cycle advance=false}"><input type="radio" name="tForumid" value="{$toForums[tforum].forumId}"></td>
	<td class="{cycle advance=false}">{$toForums[tforum].name}</td>
	<td class="{cycle advance=true}">{$toForums[tforum].comments}</td>
	</tr>
	{/section}
	</table>
	<br />
	<div align="center">
		{if $noforumsF eq 'true'}
			<b><i>There are no forums to migrate!</b></i>
		{elseif $noforumsT eq 'true'}
			<b><i>There are no forums to migrate into!  Create one first.</b></i>
		{else}
			<input type="submit" value="Import Forum">
			<p> </p>
			<b><i>Please note that by clicking on Import Forum, depending on the size of your SQL file, import may take several minutes.  Please be patient.</i></b>
		{/if}
	</div>
	<br />
</div>
</div>
</form>

{elseif $step eq 'import'}
	{if $failed eq 'true'}
		<form action="tiki-forum_import.php" method="post">
		<input type="hidden" name="step2" value="true">
		<input type="hidden" name="import" value="{$iMethod}">
		<input type="hidden" name="ftype" value="{$fi_type}">
		<input type="hidden" name="prefix" value="{$fi_prefix}">
		<input type="hidden" name="server" value="{$server}">

		You must select both a source forum and a destination forum!
		<div align="center">
			<input type="submit" value="Go Back">
		</div>
		<br />
		</form>
	{else}
		<b>{$tomove} <i>actual</i> posts moved from forum {$fF} to {$tF}.</b>
		<br />
		(over time, forum counters may have skewed, so the actual number of posts moved may not equal the number of posts shown in the previous screen.)
	{/if}

{/if}


