<h1><a class="pagetitle" href="tiki-edit_css.php">{tr}Edit Style Sheet{/tr}</a>

      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Edit+CSS" target="tikihelp" class="tikihelp" title="{tr}Edit CSS{/tr}">
{icon _id='help'}</a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-edit_css.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Edit CSS{/tr}">
{icon _id='shape_square_edit'}</a>{/if}</h1>

<div>
<form method="post" action="tiki-edit_css.php">
{tr}Style Sheet{/tr} : 
{if $action eq "edit"}
<input type="text" name="editstyle" value="{$editstyle}" />
<input type="submit" name="save" value="{tr}Save{/tr}" /> 
<a href="tiki-edit_css.php?editstyle={$editstyle}" class="link">{tr}Cancel{/tr}</a>
<div style="padding:4px;border-bottom:1px solid #c3b3a3;">
<textarea name="data" rows="42" cols="80" wrap="virtual" style="padding:7px;padding-right:0;">{$data|escape}</textarea>
</div>
{else}
<select name="editstyle">
<option value="" style="background-color:#efdece;color:#766656;border-bottom:1px dashed #787878;">{tr}choose a stylesheet{/tr}</option>
{section name=t loop=$list}
<option value="{$list[t]|escape}"{if $list[t] eq $editstyle} selected="selected"{/if}>{$list[t]}</option>
{/section}
</select>
<input type="submit" name="try" value="{tr}Try{/tr}" />
<input type="submit" name="display" value="{tr}Display{/tr}" />
<input type="submit" name="edit" value="{tr}Edit{/tr}" /><br />
<div class="">{tr}File with names appended by -{$user} are modifiable, others are only duplicable and be used as model.{/tr}</div>

{section name=l loop=$css}
<div style="padding:4px;border-bottom:1px solid #c3b3a3;">
<div style="float:right;">{$css[l].id}</div>
<div style="font-size:80%;background-color: #eeeece;">{$css[l].comment}</div>
{section name=i loop=$css[l].items}
<div style="font-weight: bold;background-color:white;">{$css[l].items[i]}</div>
{/section}
{foreach item=v key=a from=$css[l].attributes}
<div style="margin-left:10px;font:80% monospace;">{$a|string_format:"%'.-22s"} : 
{$v|string_format:"%-56.s"}
{if $v[0] eq "#"}
<span style="height:8px;width:30px;background-color:{$v};">&nbsp;&nbsp;&nbsp;&nbsp;</span>
{elseif $a|truncate:6:"" eq "border"}
<span style="height:8px;width:30px;{$a}:{$v};">&nbsp;&nbsp;&nbsp;&nbsp;</span>
{/if}
</div>
{/foreach}
</div>
{/section}
{/if}

</form>
</div>