{* $Id$ *}
<form method="post" action="tiki-page-contribution.php">
	<input type="hidden" name="page" id="page" value="{$page}" />
	<table>
		<tr>
		 <td>{tr}Process{/tr}</td>
		 <td>
		  <input type="radio" name="process" id="process0" value="0"{if $process==0} checked="checked"{/if} />{tr}Original wiki text{/tr}<br />
		  <input type="radio" name="process" id="process1" value="1"{if $process!=0 and $process!=2} checked="checked"{/if} />{tr}Parsed Text (HTML){/tr}<br />
		  <input type="radio" name="process" id="process2" value="2"{if $process==2} checked="checked"{/if} />{tr}Output text only (No HTML tags){/tr}<br />
		 </td>
		 <td>{tr}Options{/tr}</td>
		 <td>
		  <input type="checkbox" name="showstatistics" value="1"{if $showstatistics==1} checked="checked"{/if} /><label>{tr}Show statistics{/tr}</label><br />
		  <input type="checkbox" name="showpage" value="1"{if $showpage==1} checked="checked"{/if} /><label>{tr}Visualize page changes{/tr}</label><br />		  
		  <input type="checkbox" name="showpopups" value="1" {if $showpopups==1} checked="checked"{/if} /><label>{tr}Show popups{/tr}</label><br />		  
		  <input type="checkbox" name="escape" value="1" {if $escape==1} checked="checked"{/if} /><label>{tr}Escape HTML / Wiki syntax in page changes{/tr}</label><br />		  
		 </td>
		</tr>
		<tr>
		 <td>{tr}Version{/tr}</td>
		 <td colspan="3">
		  <table class="normal">
			<tr>
			  <th>{tr}Version{/tr}</th>
			  <th>{tr}Date{/tr}</th>
			  <th>{tr}User{/tr}</th>
			</tr>
				<tr>
					<td class="odd"><strong><input type="radio" name="lastversion" value="{$info.version}"{if $lastversion==$info.version or $lastversion==0} checked="checked"{/if} />{$info.version}</strong></td>
					<td class="odd"><strong>{$info.lastModif|tiki_short_datetime}</strong></td>
					<td class="odd"><strong>{$info.user|userlink}</strong></td>
				</tr>
{cycle values="odd,even" print=false}{foreach name=hist item=element from=$history}
				<tr>
					<td class="odd"><input type="radio" name="lastversion" value="{$element.version}"{if $lastversion==$element.version} checked="checked"{/if} />{$element.version}</td>
					<td class="odd">{$element.lastModif|tiki_short_datetime}</td>
					<td class="odd">{$element.user|userlink}</td>
				</tr>{/foreach}
			</table>
		 </td>
		</tr>
		<tr>
		 <td>&nbsp;</td>
		 <td><input type="submit" name="show" value="{tr}Show contributions{/tr}" /></td>
		</tr>
	</table>
</form>