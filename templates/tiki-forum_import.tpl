{* $Id$ *}

{title help="Forums" admpage="forums"}{tr}Forum Importer{/tr}{/title}

<div class="navbar">
	{button href="tiki-admin_forums.php" _text="{tr}Admin forums{/tr}"}
	{button href="tiki-forums.php" _text="{tr}List forums{/tr}"}
</div>
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
	<input type="hidden" name="step1" value="true" />
	<input type="hidden" name="import" value="same" />
	<table class="formcolor">
		<tr><td>{tr}Forum Type{/tr}:</td><td>
		<select name="forum">
		
		{cycle values="odd,even" print=false}
		{section name=ftype loop=$fi_types}
		<option value="{$fi_types[ftype]}">{$fi_types[ftype]}</option>
		{/section}

		</select>
		</td></tr>
		<tr><td>{tr}DB Prefix{/tr}:</td><td>
		<input type="text" name="prefix" value="{$fi_prefixes[0]}" />
		</td></tr>
	</table>
	<br />
	<div align="center">
		<input type="submit" value="{tr}Get Forum List{/tr}" />
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
	<input type="hidden" name="step1" value="true" />
	<input type="hidden" name="import" value="other" />
	<table class="formcolor">
		<tr><td>{tr}Server{/tr}:</td><td>
		<input type="text" name="server" />
		</td><td>{tr}DB Name{/tr}:</td><td>
		<input type="text" name="dbname" />
                </td></tr>
		<tr><td>{tr}Forum Type{/tr}:</td><td>
		<select name="forum">

		</select>
		</td><td>{tr}DB Prefix{/tr}:</td><td>
		<input type="text" name="prefix" />
		</td></tr>
		<tr><td>{tr}Username{/tr}:</td><td>
		<input type="text" name="username" />
		</td><td>{tr}Password{/tr}:</td><td>
		<input type="text" name="password" />
                </td></tr>
	</table>
	<br />
	<div align="center">
		<input type="submit" value="Test Connection" />
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
	<input type="hidden" name="step1" value="true" />
	<input type="hidden" name="import" value="sql" />
	<table class="formcolor">
		<tr><td>{tr}Forum Type{/tr}:</td><td>
		<select name="ftype">
		
		{* List all forums that are supported. *}
		{cycle values="odd,even" print=false}
		{section name=ftype loop=$fi_types}
		<option value="{$fi_types[ftype]}">{$fi_types[ftype]}</option>
		{/section}

		</select>
		</td></tr>
		<tr><td>{tr}DB Prefix{/tr}:</td><td>
		<input type="text" name="prefix" value="{$fi_prefixes[0]}" />
		</td></tr>
		<tr><td>{tr}Local SQL Filename on Server (path will be stripped){/tr}:<br />
		<i>{tr}Must be in tikiroot/{$tmpdir} or tikiroot/img/wiki_up{/tr}</i></td><td>
		<input type="text" name="server" />
	</td></tr>
	</table>
	<div align="center">
		<input type="submit" value="{tr}Get Forum List{/tr}" />
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
	  <input type="hidden" name="step2" value="true" />
	{else}
	  <input type="hidden" name="step0" value="true" />
	{/if}
	<input type="hidden" name="import" value="{$iMethod}" />
	<input type="hidden" name="ftype" value="{$fi_type}" />
	<input type="hidden" name="prefix" value="{$fi_prefix}" />
	<input type="hidden" name="server" value="{$server}" />

	<b>{tr}File must be an SQL file and exist in either <i>$tikiroot/temp</i> or <i>$tikiroot/img/wiki_up</i>.{/tr}</b>
	<p> </p>
	{if $passed eq 'true'}
	    File found: {$server}.
	{else}
	  {tr}File was not specified, or could not be found in either location.  Please put the file in either directory and Go Back.  If the file exists in both locations, the $tikiroot/{$filecheck} location will be preferred.{/tr}
	{/if}

	<p> </p>
	<div align="center">
		{if $passed eq 'true'}
		  <input type="submit" value="{tr}Proceed{/tr}" />
		{else}
		  <input type="submit" value="{tr}Go Back{/tr}" />
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
	<input type="hidden" name="step3" value="true" />
	<input type="hidden" name="import" value="{$iMethod}" />
	<input type="hidden" name="ftype" value="{$fi_type}" />
	<input type="hidden" name="prefix" value="{$fi_prefix}" />
	<input type="hidden" name="server" value="{$server}" />

	<table class="normal">
	<tr>
	<th>Select</th>
	<th>Forum Name</th>
	<th>Posts</th>
	</tr>

	{cycle values="odd,even" print=false}
	{section name=fforum loop=$fromForums}
	<tr class="{cycle}">
	<td>
          <input type="radio" name="fForumid" value="{$fromForums[fforum].id}" />
        </td>
	<td>{$fromForums[fforum].name}</td>
	<td>{$fromForums[fforum].comments}</td>
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
	<tr class="{cycle}">
	<td>
          <input type="radio" name="tForumid" value="{$toForums[tforum].forumId}" />
        </td>
	<td>{$toForums[tforum].name}</td>
	<td>{$toForums[tforum].comments}</td>
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
		<input type="hidden" name="step2" value="true" />
		<input type="hidden" name="import" value="{$iMethod}" />
		<input type="hidden" name="ftype" value="{$fi_type}" />
		<input type="hidden" name="prefix" value="{$fi_prefix}" />
		<input type="hidden" name="server" value="{$server}" />

		You must select both a source forum and a destination forum!
		<div align="center">
			<input type="submit" value="{tr}Go Back{/tr}" />
		</div>
		<br />
		</form>
	{else}
		<b>{$tomove} <i>actual</i> posts moved from forum {$fF} to {$tF}.</b>
		<br />
		(over time, forum counters may have skewed, so the actual number of posts moved may not equal the number of posts shown in the previous screen.)
	{/if}

{/if}


