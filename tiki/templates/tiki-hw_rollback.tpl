{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_rollback.tpl,v 1.1 2004-02-07 17:08:10 ggeller Exp $ *}
{* Copyright (c) 2004 George G. Geller *}
<h2>{tr}Rollback page{/tr}: {$page} {tr}by{/tr} {$studentName} {tr}to_version{/tr}: {$version}</h2>
<div class="wikitext">{$preview.data}</div>
<div align="center">
  <form action="tiki-hw_rollback.php" method="post">
    <input type="hidden" name="student" value="{$studentName|escape}" /> 
    <input type="hidden" name="id" value="{$pageId|escape}" />
    <input type="hidden" name="assignmentId" value="{$assignmentId|escape}" />
    <input type="hidden" name="version" value="{$version|escape}" />
    <input type="submit" name="rollback" value="{tr}rollback{/tr}" />
  </form>
</div>
