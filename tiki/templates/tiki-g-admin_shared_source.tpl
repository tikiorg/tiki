{*Smarty template*}
<a class="pagetitle" href="tiki-g-admin_shared_source.php?pid={$pid}">{tr}Admin process sources{/tr}</a><br/><br/>
{include file=tiki-g-proc_bar.tpl}
{if count($errors) > 0}
<div class="wikitext">
Errors:<br/>
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br/>
{/section}
</div>
{/if}

<form id='editsource' action="tiki-g-admin_shared_source.php" method="post">
<input type="hidden" name="pid" value="{$pid}" />
<input type="hidden" name="source_name" value="{$source_name}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}select source{/tr}</td>
  <td class="formcolor">
		<select name="activityId" onChange="document.getElementById('editsource').submit();">
		<option value="" {if $activityId eq 0}selected="selected"{/if}>{tr}Shared code{/tr}</option>
		{section loop=$items name=ix}
		<option value="{$items[ix].activityId}" {if $activityId eq $items[ix].activityId}selected="selected"{/if}>{$items[ix].name}</option>
		{/section}
		</select>
  </td>

  <td class="formcolor">
    {if $activityId > 0 and $act_info.isInteractive eq 'y' and $template eq 'n'}
    <input type="submit" name='template' value="{tr}template{/tr}" />
    {/if}
    {if $activityId > 0 and $act_info.isInteractive eq 'y' and $template eq 'y'}
	<input type="submit" name='save' value="{tr}code{/tr}" />
    {/if}
  </td>


  <td class='formcolor'>
  	<input type="submit" name='save' value="{tr}save{/tr}" />
  	<input type="submit" name='cancel' value="{tr}cancel{/tr}" />
  </td>
</tr>
<tr>
  <td class="formcolor" colspan="4">
  	<textarea name="source" rows="20" cols="80">{$data}</textarea>
  </td>
</tr>
</table>  
</form>
