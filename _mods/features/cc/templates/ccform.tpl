{if $user and $tiki_p_cc_create eq 'y'}
{if $info and ($tiki_p_cc_admin eq 'y' or $info.owner eq $user)}
<a href="cc.php?page=admincc" class="pagetitle">Modify a Currency</a>
<br /><br />

<FORM action="cc.php?page=admincc" method='post'>
{else}
<a href="cc.php?page=newcc" class="pagetitle">Create a new Currency</a>
<br /><br />

<FORM action="cc.php?page=newcc" method='post'>
{/if}
{if $tiki_p_cc_admin eq 'y'}
<span class="button2"><a href="cc.php?page=admincc" class="linkbut">Admin Currencies</a></span>
<br /><br />
{/if}

<table class="formcolor">

<tr class="formrow">
<td>Currency id</td>
<td><input type='text' name='id' value="{$info.id}" /></td>
</tr>

<tr class="formrow">
<td>Name</td>
<td><input type='text' name='cc_name' value="{$info.cc_name}" /></td>
</tr>

<tr class="formrow">
<td>Description</td>
<td><textarea cols='40' rows='10' name='cc_description'>{$info.cc_description}</textarea></td>
</tr>

<tr class="formrow">
<td>Requires approval</td>
<td><select name='requires_approval'>
<option value='y'{if $info.requires_approval eq 'y'} selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n'{if $info.requires_approval eq 'n'} selected="selected"{/if}>{tr}No{/tr}</option>
</select>
</td>
</tr>

<tr class="formrow">
<td>Listed publicaly</td>
<td><select name='listed'>
<option value='y'{if $info.listed eq 'y'} selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n'{if $info.listed eq 'n'} selected="selected"{/if}>{tr}No{/tr}</option>
</select>
</td>
</tr>

{if $tiki_p_cc_admin eq 'y'}
<tr class="formrow">
<td>Owner</td>
<td><input type="text" name="owner" value="{$info.owner_id|default:$user}"/>
</tr>
{/if}

<tr>
<td></td><td><input type='submit' value='{if $info and ($tiki_p_cc_admin eq 'y' or $info.owner eq $user)}Save{else}Create{/if}'></td>
</tr>
</table>
</form>
{/if}
