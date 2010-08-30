{* $Id$ *}
<form method="post" action="tiki-page-contribution.php">
	<input type="hidden" name="page" id="page" value="{$page}" />
	<fieldset>
		<legend>{tr}Process{/tr}</legend>
		  <label><input type="radio" name="process" id="process0" value="0"{if $process==0} checked="checked"{/if} title="{tr}Original wiki text{/tr}" />{tr}Original wiki text{/tr}</label><br />
		  <label><input type="radio" name="process" id="process1" value="1"{if $process!=0 and $process!=2} checked="checked"{/if} title="{tr}Parsed Text (HTML){/tr}" />{tr}Parsed Text (HTML){/tr}</label><br />
		  <label><input type="radio" name="process" id="process2" value="2"{if $process==2} checked="checked"{/if} title="{tr}Output text only (No HTML tags){/tr}" />{tr}Output text only (No HTML tags){/tr}</label><br />
	</fieldset>
	<fieldset>
		<legend>{tr}Options{/tr}</legend>
		  <label><input type="checkbox" name="showstatistics" value="1"{if $showstatistics==1} checked="checked"{/if} title="{tr}Show statistics{/tr}" /> {tr}Show statistics{/tr}</label><br />
		  <label><input type="checkbox" name="showpage" value="1"{if $showpage==1} checked="checked"{/if} title="{tr}Visualize page changes{/tr}" /> {tr}Visualize page changes{/tr}</label><br />		  
		  <label><input type="checkbox" name="showpopups" value="1" {if $showpopups==1} checked="checked"{/if} title="{tr}Visualize page changes{/tr}" /> {tr}Show popups{/tr}</label><br />		  
		  <label><input type="checkbox" name="escape" value="1" {if $escape==1} checked="checked"{/if} title="{tr}Escape HTML / Wiki syntax in page changes{/tr}" /> {tr}Escape HTML / Wiki syntax in page changes{/tr}</label><br />	
	</fieldset>
	<fieldset>
		<legend>{tr}Version{/tr}</legend>
		  <table class="normal">
			<tr>
			  <th>{tr}Version{/tr}</th>
			  <th>{tr}Date{/tr}</th>
			  <th>{tr}User{/tr}</th>
			</tr>
				<tr>
					<td class="odd"><label><input type="radio" name="lastversion" value="{$info.version}"{if $lastversion==$info.version or $lastversion==0} checked="checked"{/if} title="{tr}Version{/tr} {$info.version}" /> <strong>{$info.version}</strong></label></td>
					<td class="odd"><strong>{$info.lastModif|tiki_short_datetime}</strong></td>
					<td class="odd"><strong>{$info.user|userlink}</strong></td>
				</tr>
{cycle values="odd,even" print=false}{foreach name=hist item=element from=$history}
				<tr>
					<td class="{cycle values="odd,even" advance=false}"><label><input type="radio" name="lastversion" value="{$element.version}"{if $lastversion==$element.version} checked="checked"{/if}  title="{tr}Version{/tr} {$info.version}" /> {$element.version}</label></td>
					<td class="{cycle values="odd,even" advance=false}">{$element.lastModif|tiki_short_datetime}</td>
					<td class="{cycle values="odd,even"}">{$element.user|userlink}</td>
				</tr>{/foreach}
			</table>
	</fieldset>

	<div><input type="submit" name="show" value="{tr}Show contributions{/tr}" /></div>
	
</form>