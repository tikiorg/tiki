{if $info and ($tiki_p_cc_admin eq 'y' or $info.owner_id eq $user)}
<a href="cc.php?page=currencies&amp;cc_id={$info.id}" class="pagetitle">{tr}Modify a Currency{/tr}</a>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />
<br /><br />
{else}
<a href="cc.php?page=currencies&amp;new=1" class="pagetitle">{tr}Create a new Currency{/tr}</a>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />
<br /><br />
{/if}

<FORM action="cc.php?page={$page}" method='post'>
<span class="button2"><a href="cc.php?page=currencies" class="linkbut">{tr}List Currencies{/tr}</a></span>
<span class="button2"><a href="cc.php?page=currencies&amp;my=1" class="linkbut">{tr}My Currencies{/tr}</a></span>
{if $info.id}
<span class="button2"><a href="cc.php?page=currencies&amp;cc_id={$info.id}&amp;view=1" class="linkbut">{tr}Examine{/tr} {$info.id}</a></span>
{/if}
<br /><br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<table class="formcolor">

<tr class="formrow">
<td>Id</td>
<td>
{if $info.id}
{$info.id}
<input type='hidden' name='cc_id' value="{$info.id}" />
{else}
<input type='text' name='cc_id' value="" />
{/if}
</td>
</tr>

<tr class="formrow">
<td>{tr}Name{/tr}</td>
<td><input type='text' name='cc_name' value="{$info.cc_name}" /></td>
</tr>

<tr class="formrow">
<td>{tr}Description{/tr}</td>
<td><textarea cols='40' rows='10' name='cc_description'>{$info.cc_description}</textarea></td>
</tr>

<tr class="formrow">
<td>{tr}Requires approval{/tr}</td>
<td><select name='requires_approval'>
<option value='n'{if $info.requires_approval eq 'n'} selected="selected"{/if}>{tr}No{/tr}</option>
<option value='y'{if $info.requires_approval eq 'y'} selected="selected"{/if}>{tr}Yes{/tr}</option>
</select>
</td>
</tr>

<tr class="formrow">
<td>{tr}Listed publicly{/tr}</td>
<td><select name='listed'>
<option value='y'{if $info.listed eq 'y'} selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n'{if $info.listed eq 'n'} selected="selected"{/if}>{tr}No{/tr}</option>
</select>
</td>
</tr>


{if !$info}
<tr class="formrow">
<td>{tr}Register Owner{/tr}</td>
<td><select name='register_owner'>
<option value='y'>{tr}Yes{/tr}</option>
<option value='n'>{tr}No{/tr}</option>
</select>
</td>
</tr>
{/if}

{if $tiki_p_cc_admin eq 'y'}
<tr class="formrow">
<td>{tr}Owner{/tr}</td>
<td><input type="text" name="owner" value="{$info.owner_id|default:$user}"/>
</tr>
{/if}

<tr>
<td></td><td><input type='submit' value='{if $info and ($tiki_p_cc_admin eq 'y' or $info.owner_id eq $user)}{tr}Save{/tr}{else}{tr}Create{/tr}{/if}'></td>
</tr>
</table>
</form>

