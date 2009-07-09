
{if empty($user)}
<tr{if !empty($tr_style)} class="{$tr_style}"{/if}
>
	<td{if !empty($td_style)} class="{$td_style}"{/if}>{tr}Anti-Bot verification code{/tr}:</td>
	<td{if !empty($td_style)} class="{$td_style}"{/if}><img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}' /></td>
</tr>
<tr{if !empty($tr_style)} class="{$tr_style}"{/if}
>
	<td{if !empty($td_style)} class="{$td_style}"{/if}><label for="antibotcode">{tr}Enter the code you see above{/tr}{if $showmandatory eq 'y'}*{/if}:</label></td>
	<td{if !empty($td_style)} class="{$td_style}"{/if}><input type="text" maxlength="8" size="8" name="antibotcode" id="antibotcode" /></td>
</tr>
{/if}
