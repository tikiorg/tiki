{if $tiki_p_edit eq 'y'}
<div class="box">
<div class="box-title">
{tr}Quick edit a Wiki page{/tr}
</div>
<div class="box-data">
<form method="get" action="tiki-editpage.php">
<input type="text" size="15" name="page" />
<input type="submit" name="quickedit" value="{tr}edit{/tr}" />
</form>
</div>
</div>
{/if}
