{if $tiki_p_edit eq 'y'}
<div class="box">
<div class="box-title">
{tr}Quick edit a Wiki page{/tr}
</div>
<div class="box-data">
<form method="get" action="tiki-editpage.php">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td class="module">
<input type="text" size="15" name="page" />
</td>
<td class="module">
<input type="submit" name="quickedit" value="{tr}edit{/tr}" />
</td>
</tr>
</table>
</form>
</div>
</div>
{/if}