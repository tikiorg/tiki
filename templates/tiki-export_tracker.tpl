{* $Id: tiki-export_tracker.tpl 15727 2008-11-21 22:05:51Z sylvieg $ *}

<h2>{tr}Export Tracker Items{/tr}</h2>

<form action="tiki-view_tracker.php?trackerId={$trackerId}&cookietab=3" method="post">
<table class="normal">
<tr class="formcolor">
	<td><label for="tracker">{tr}Tracker{/tr}</label></td>
	<td>
	<select name="trackerId" onchange="this.form.submit();" id="tracker">
      {foreach from=$trackers item=tracker}
       <option value="{$tracker.trackerId}" title="{$tracker.description|escape}"{if $tracker.trackerId eq $trackerId} selected="selected"{/if}>
           {$tracker.name|escape}
       </option>
      {/foreach}
    </select>
	</td>
</tr>
</table>
</form>
<form action="tiki-export_tracker.php" method="post">
<table class="normal">
<tr class="formcolor">
	<td>{tr}File{/tr}</td>
	<td>{tr}Tracker{/tr}_{$trackerId}.csv</td>
</tr>
<tr class="formcolor">
	<td><label for="encoding">{tr}Charset encoding{/tr}</label></td>
	<td><select name="encoding" id="emcoding"><option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option><option value="ISO-8859-1">{tr}ISO-8859-1{/tr}</option></select></td>
</tr>
<tr class="formcolor">
	<td><label for="separator">{tr}Separator{/tr}</label></td>
	<td><input type="text" name="separator" id="separator" value="," /></td>
</tr>
<tr class="formcolor">
	<td><label for="delimitorL">{tr}Delimitors{/tr}</label></td>
	<td><input type="text" name="delimitorL" id="delimitorL" value='"' /><input type="text" name="delimitorR" value='"' /></td>
</tr>
<tr class="formcolor">
	<td><label for="CR">{tr}Carriage Return inside Field Value{/tr}</label></td>
	<td><input type="text" name="CR" id="CR" value='%%%' /></td>
</tr>
<tr class="formcolor">
	<td><label for="parse">{tr}Parse as Wiki Text{/tr}</label></td>
	<td><input type="checkbox" name="parse" id="parse" /></td>
</tr>
<tr class="formcolor">
	<td>{tr}Info{/tr}</td>
	<td>
		<input name="showItemId" id="showItemId" type="checkbox" checked="checked" /><label for="showItemId">{tr}itemId{/tr}</label>
		<br /><input type="checkbox" name="showStatus" id="showStatus"{if $info.showStatus eq 'y'} checked="checked"{/if} /><label for="showStatus">{tr}status{/tr}</label>
		<br /><input type="checkbox" name="showCreated" id="showCreated"{if $info.showCreated eq 'y'} checked="checked"{/if} /><label for="showCreated">{tr}created{/tr}</label>
		<br /><input type="checkbox" name="showLastModif" id="showLastModif"{if $info.showLastModif eq 'y'} checked="checked"{/if} /><label for="lastModif">{tr}lastModif{/tr}</label>
	</td>
</tr>
<tr class="formcolor">
	<td>{tr}Fields{/tr}</td>
	<td>
		<input type="radio" name="which" id="list" value="list"/> <label for="list">{tr}Fields visible in items list{/tr}</label>
		<br /><input type="radio" name="which" id="ls" value="ls"/> <label for="ls">{tr}Fields searchable or visible in items list{/tr}</label>
		<br /><input type="radio" name="which" id="item" value="item"/> <label for="item">{tr}Fields visible in an item view{/tr}</label>
		<br /><input type="radio" name="which" id="all" value="all"{if empty($displayedFields)} checked="checked"{/if} /> <label for="all">{tr}All fields{/tr}</label>
		<br /><input type="radio" name="which" id="these" value="these"{if !empty($displayedFields)} checked="checked"{/if}> <label for="these">{tr}These fields{/tr}</label>
		<select multiple="multiple" name="listfields[]" id="listfields">
			{foreach from=$fields item=ix}
				{if ($ix.isHidden eq 'n' or $ix.isHidden eq 'c' or $ix.isHidden eq 'p' or $tiki_p_admin_trackers eq 'y') and $ix.type ne 'x' and $ix.type ne 'h' and ($ix.type ne 'p' or $ix.options_array[0] ne 'password') and (empty($ix.visibleBy) or in_array($default_group, $ix.visibleBy) or $tiki_p_admin_trackers eq 'y')}
					<option value="{$ix.fieldId}"{if !empty($displayedFields) and in_array($ix.fieldId, $displayedFields)} selected="selected"{/if}>{$ix.name|escape}</option>
				{/if}
			{/foreach}
		</select>
		{remarksbox type="tip" title="Tip"}{tr}Use Ctrl+Click to select multiple fields.{/tr}{/remarksbox}
	</td>
</tr>
<tr class="formcolor">
	<td>{tr}Filter{/tr}</td>
	<td>{include file="wiki-plugins/wikiplugin_trackerfilter.tpl" showFieldId="y" inForm="y"}</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="export" value="{tr}Export{/tr}" /></td>
</tr>
</table>
</form>
{if $tiki_p_tracker_dump eq "y" or $tiki_p_admin eq "y"}
	<h2>{tr}Dump All Tracker Items{/tr}</h2>
	<div>
		<form action="{$smarty.server.PHP_SELF}" method="post">
			<table class="normal">
				<tr class="formcolor">
					<td width="20%"><label for="tracker">{tr}Tracker{/tr}</label></td>
					<td>
					<select name="trackerId" onchange="this.form.submit();" id="dumpTrackerId">
				      {foreach from=$trackers item=tracker}
				       <option value="{$tracker.trackerId}" title="{$tracker.description|escape}"{if $tracker.trackerId eq $trackerId} selected="selected"{/if}>
				           {$tracker.name|escape}
				       </option>
				      {/foreach}
				    </select>
				    {$recordsMax} {tr}Items{/tr}
					</td>
				</tr>
			</table>
		</form>
		<form action="tiki-export_tracker.php?trackerId={$trackerId}" method="post" id="dump_form">
			<table>
				<tr class="formcolor">
					<td width="20%">&nbsp;</td>
					<td>
						<input type="submit" name="dump_tracker" id="dump_tracker" value="{tr}Dump{/tr}" />
					</td>
				</tr>
			</table>
		</form>
	</div>
{/if}


