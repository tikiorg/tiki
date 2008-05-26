<h1 class='pagetitle'><a href='#' class='pagetitle'>TikiTests Replay Configuration</a></h1>
<br/>
<h2 class='pagetitle'>TikiTest:{$filename}</h2>
<br/>
{if $summary eq 'y' and is_array($result) and sizeof($result) gt 0}
	{if $test_success eq $test_count}
	<b><font color="green">{tr}Success{/tr}</font></b>
	{else}
	<b><font color="red">{tr}Failure{/tr}</font></b> {$test_success}/{$test_count}
	{/if}
{else}
{include file='tiki-tests_menubar.tpl'}
<fieldset>
<legend>{tr}Options{/tr}</legend>
<form action="tiki_tests/tiki-tests_replay.php" method="post">
<input type="checkbox" name="summary" value="y" {if $summary eq 'y'} checked="checked"{/if}/>{tr}Summary mode{/tr}<br/>
<input type="checkbox" name="show_page" value="y" {if $show_page eq 'y'} checked="checked"{/if}/>{tr}Show Page Differences{/tr}<br/>
<input type="checkbox" name="show_tidy" value="y" {if $show_tidy eq 'y'} checked="checked"{/if}/>{tr}Show Tidy Errors and Warnings{/tr}<br/>
<input type="checkbox" name="show_post" value="y" {if $show_post eq 'y'} checked="checked"{/if}/>{tr}Show POST Data{/tr}<br/>
<input type="checkbox" name="current_session" value="y" {if $current_session eq 'y'} checked="checked"{/if}/>{tr}Use Current Session/Logout{/tr}<br/>
<input type="hidden" name="filename" value="{$filename}" />
<center><input type="submit" name="action" value="{tr}Replay{/tr}" /></center>
</form>
</fieldset>
{if is_array($result) and sizeof($result) gt 0}
<fieldset>
<legend>{tr}Results{/tr}</legend>
<table class="normal" width="100%">
{foreach from=$result item=r}
	<tr>
		<td class="heading" width="10%">{tr}Request:{/tr}&nbsp;{$r.method}</td><td>{$r.url}</td>
	</tr>
	{if isset($r.post) and $show_post}
		<tr>
			<td class="heading" colspan="4">{tr}Post Variables{/tr}</td>
		</tr>
		{foreach from=$r.post item=p key=k}
			<tr>
				<td colspan="2">{$k}</td><td colspan="2">{$p}</td>
			</tr>
		{/foreach}
	{/if}
	<tr><td colspan="4">
	<table class="normal" width="100%">
	{if $show_tidy}
		<tr><td class="heading" colspan="2">{tr}Tidy Results{/tr}&nbsp;{tr}Reference{/tr}</td><td class="heading" colspan="2">{tr}Tidy Results{/tr}&nbsp;{tr}Replay{/tr}</td></tr>
 		<tr><td colspan="2" width="50%"><pre>{$r.ref_error_msg|escape:"html"}</pre></td>
		<td colspan="2" width="50%"><pre>{$r.replay_error_msg|escape:"html"}</pre></td>
		</tr>
		{/if}
	{if $r.html}
		{if $show_page}
	<tr><td class="heading" colspan="4" border="1">{tr}Results{/tr}</td></tr>
			{$r.html}
		{else}
	<tr>
		<td class="heading" colspan="1">{tr}Results{/tr}</td>
		<td colspan="3"><b><font color="red">{tr}The pages are different{/tr}</font></b></td>
	</tr>
		{/if}
	{else}
	<tr>
		<td class="heading" colspan="1">{tr}Results{/tr}</td>
		<td colspan="3"><b><font color="green">{tr}The pages are identical{/tr}</font></b></td>
	</tr>
{/if}
	</table>
	</td>
	</tr>
{/foreach}
</table>
</fieldset>
{/if}
{/if}
