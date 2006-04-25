{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tikimodule title="{tr}Assignments{/tr}" name="aulawiki_assignments" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
{foreach from=$assignments item=assignment}
{cycle values="odd,even" assign="parImpar"}
<div class="{$parImpar}">
<img src="./images/aulawiki/edu_assignments.gif" align="middle"> <a class="categtree" href="aulawiki-view_module.php?module=aulawiki_view_assignment&activeAssignment={$assignment.assignmentId}">({$assignment.startDate|tiki_short_datetime}-{$assignment.endDate|tiki_short_datetime})<br/>{$assignment.name}</a>
</div>
<br/>
{/foreach}
{/tikimodule}
