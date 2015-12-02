 {* $Id$ *}
 

<p>The current values for the {$xmlcount} items from the XML management file are shown in the input fields below.</p>

<p>Change any individual values and click the 'Update' button to submit the revised values and to resave them in the File Gallery XML file.</p>

<form action="{$smarty.server.PHP_SELF}?{query}" method="post" class="form-inline">
<hr />
<table>
	<tr>
    	{if $xmlnamelist eq 'yes'}<td style="text-align: right;"><b>xml node name</b></td>{/if}
        {if $attused eq 'yes'}<td style="text-align: center;"><b>xml node description</b></td>{/if}
        <td style="text-align: center;"><b>xml node current/new value</b></td>       
    </tr> 
	{for $item=0 to $xmlcount-1}
	<tr>
    	{if $xmlnamelist eq 'yes'}<td class="xml_name" style="text-align: right;"><b>{$xmlnames[$item]}</b></td>{/if}
        {if $attused eq 'yes'}<td class="xml_description">{$xmldesc[$item]}</td>{/if}
        <td class="xml_value">{if isset($xmlvalues[$item])}<input size="40" type="text" name="{$xmlnames[$item]}" value="{$xmlvalues[$item]|escape}">{else}This item not found in the XML file{/if}</td>       
    </tr>  
    {/for}
</table>
<hr />
    <input type="submit" class="btn btn-primary btn-sm" name="update" value="{tr}Update{/tr}">
</form>
