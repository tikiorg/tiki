{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-num_submissions.tpl,v 1.12 2007-10-14 17:51:01 mose Exp $ *}

{if $prefs.feature_submissions eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Waiting Submissions{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="num_submissions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {tr}We have{/tr} {$modNumSubmissions} <a class="linkmodule" href="tiki-list_submissions.php">{tr}submissions waiting to be examined{/tr}</a>.
{/tikimodule}
{/if}
