{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-num_submissions.tpl,v 1.10 2007-02-18 11:21:17 mose Exp $ *}

{if $feature_submissions eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Waiting Submissions{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="num_submissions" flip=$module_params.flip decorations=$module_params.decorations}
  {tr}We have{/tr} {$modNumSubmissions} <a class="linkmodule" href="tiki-list_submissions.php">{tr}submissions waiting to be examined{/tr}</a>.
{/tikimodule}
{/if}
