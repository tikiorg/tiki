<div class="box">
<div class="box-title">
{tr}Ephemerides{/tr}
</div>
<div class="box-data">
{if $modephdata}
<table>
{if $modephdata.filesize}
<tr>
<td text-align="center" class="module"><img alt="image" src="tiki-view_eph.php?ephId={$modephdata.ephId}" /></td>
</tr>
{/if}
<tr>
<td class="module">{$modephdata.textdata}</td>
</tr>
</table>
{/if}
</div>
</div>

