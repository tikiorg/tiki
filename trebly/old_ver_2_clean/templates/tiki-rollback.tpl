{title}{tr}Rollback page{/tr} {$page|escape} {tr}to version{/tr} {$version}{/title}

<form action="tiki-rollback.php?page={$page|escape:url}&amp;version={$version|escape}&amp;rollback=y" method="post">
  <input type="submit" name="rollback" value="{tr}Rollback{/tr}" />
  <input type="hidden"  name="page" value="{$page|escape}" />
  <input type="hidden" name="version" value="{$version|escape}" />
</form>
<div class="wikitext">{$preview.data}</div>
<form action="tiki-rollback.php?page={$page|escape:url}&amp;version={$version|escape}&amp;rollback=y" method="post">
  <div align="center">
    <input type="hidden"  name="page" value="{$page|escape}" />
    <input type="hidden" name="version" value="{$version|escape}" />
    <input type="submit" name="rollback" value="{tr}Rollback{/tr}" />
  </div>
</form>

{include file='tiki-page_bar.tpl'}

