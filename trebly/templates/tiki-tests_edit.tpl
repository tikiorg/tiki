{jq}
var html = new Array();
function test_xpath(index) {
	/*
	alert("Index:"+index);
  alert ('Xpath['+index+']='+document.getElementById('xpath_'+index).value);
	*/
	window.open('tiki_tests/tiki-tests_show_xpath.php?filename={{$filename}}&index='+index+'&xpath='+document.getElementById('xpath_'+index).value,'_blank')	;
}
{/jq}

{title help="tests"}{tr}TikiTests Edit{/tr}{/title}

<h2 class='pagetitle'>TikiTest:{$filename}</h2>
<br/>
{include file='tiki-tests_menubar.tpl'}
<fieldset>
<legend>{tr}Options{/tr}</legend>
<form name='tiki_tests' action="tiki_tests/tiki-tests_edit.php" method="post">
<input type="checkbox" name="summary" value="y" {if $summary eq 'y'} checked="checked"{/if}/>{tr}Summary mode{/tr}<br/>
<input type="checkbox" name="show_page" value="y" {if $show_page eq 'y'} checked="checked"{/if}/>{tr}Show Page Differences{/tr}<br/>
<input type="checkbox" name="show_tidy" value="y" {if $show_tidy eq 'y'} checked="checked"{/if}/>{tr}Show Tidy Errors and Warnings{/tr}<br/>
<input type="checkbox" name="show_post" value="y" {if $show_post eq 'y'} checked="checked"{/if}/>{tr}Show POST Data{/tr}<br/>
<input type="checkbox" name="current_session" value="y" {if $current_session eq 'y'} checked="checked"{/if}/>{tr}Use Current Session/Log out{/tr}<br/>
<input type="hidden" name="filename" value="{$filename}" />
<center><input type="submit" name="action" value="{tr}Refresh{/tr}" /></center>
{if $result}
</fieldset>
<fieldset>
<legend>{tr}Recorded Links{/tr}</legend>
<table class="normal" width="100%">
{foreach from=$result item=r name=url}
	<tr>
		<th style="width:10%">{tr}Request:{/tr}&nbsp;{$r.method}</td><td>{$r.url}</th>
	</tr>
	<tr><td colspan="2">
  <table style="width:100%" >
		<tr><th colspan="2">{tr}Element to compare (Xpath expression):{/tr}&nbsp;<input type="text" style="width:50%;" id="xpath_{$smarty.foreach.url.index}" name="xpath[{$smarty.foreach.url.index}]" value="{$r.xpath}" />&nbsp;<input type="button" value="{tr}Test Xpath Expression{/tr}" onclick="javascript:test_xpath({$smarty.foreach.url.index}); return false" /></th>
	</tr>
	{if isset($r.post) and $show_post and sizeof($r.post) gt 0 }
		<tr>
			<th colspan="2">{tr}Post Variables{/tr}</th>
		</tr>
		{foreach from=$r.post item=p key=k}
			<tr>
				<td>{$k}</td><td>{$p}</td>
			</tr>
		{/foreach}
	{/if}
	{if $show_tidy}
	<tr><th colspan="4">{tr}Tidy Results{/tr}</th></tr>
	<tr><td colspan="2">
	<table class="normal" width="100%">
 		<tr><td colspan="2" width="50%"><pre>{$r.ref_error_msg|escape:"html"}</pre></td>
		</tr>
	</table>
	</td>
	</tr>
	{/if}
	<tr><td colspan="2"><input type="checkbox" name="delete[{$smarty.foreach.url.index}]" value="delete">{tr}Delete this link{/tr}</td></tr>
	</table>
	</td>
	</tr>
	{/foreach}
</table>
</fieldset>
<center><input type="submit" name="action" value="{tr}Edit{/tr}" /></center>
{/if}
</form>
