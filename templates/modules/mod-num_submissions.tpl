{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-num_submissions.tpl,v 1.5 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_submissions eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Waiting Submissions{/tr}" module_name="num_submissions"}
</div>
<div class="box-data">
{tr}We have{/tr} {$modNumSubmissions} {tr}submissions waiting to be examined{/tr}.
</div>
</div>
{/if}