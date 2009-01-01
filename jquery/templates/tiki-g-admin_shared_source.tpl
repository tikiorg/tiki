{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-admin_shared_source.php?pid={$pid}">{tr}Admin process sources{/tr}</a></h1>
{include file=tiki-g-proc_bar.tpl}
{if count($errors) > 0}
<div class="wikitext">
Errors:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div>
{/if}

<form id='editsource' action="tiki-g-admin_shared_source.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="source_name" value="{$source_name|escape}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}select source{/tr}</td>
  <td class="formcolor">
		<select name="activityId" onchange="document.getElementById('editsource').submit();">
		<option value="" {if $activityId eq 0}selected="selected"{/if}>{tr}Shared code{/tr}</option>
		{section loop=$items name=ix}
		<option value="{$items[ix].activityId|escape}" {if $activityId eq $items[ix].activityId}selected="selected"{/if}>{$items[ix].name}</option>
		{/section}
		</select>
  </td>

  <td class="formcolor">
    {if $activityId > 0 and $act_info.isInteractive eq 'y' and $template eq 'n'}
    <input type="submit" name='template' value="{tr}template{/tr}" />
    {/if}
    {if $activityId > 0 and $act_info.isInteractive eq 'y' and $template eq 'y'}
	<input type="submit" name='save' value="{tr}Code{/tr}" />
    {/if}
  </td>


  <td class='formcolor'>
  	<input type="submit" name='save' value="{tr}Save{/tr}" />
  	<input type="submit" name='cancel' value="{tr}Cancel{/tr}" />
  </td>
</tr>
<tr>
  <td class="formcolor" colspan="4">
    <table>
    <tr>
    <td>
  	<textarea id='src' name="source" rows="20" cols="80">{$data|escape}</textarea>
  	</td>
  	<td>
  	{if $template eq 'y'}
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}if{rdelim}\n{ldelim}elseif{rdelim}\n{ldelim}else{rdelim}\n{ldelim}/if{rdelim}\n');">{ldelim}if{rdelim}{ldelim}/if{rdelim}</a><hr/>
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}section name= loop={rdelim}\n{ldelim}sectionelse{rdelim}\n{ldelim}/section{rdelim}\n');">{ldelim}section{rdelim}{ldelim}/section{rdelim}</a><hr/>
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}foreach from= item={rdelim}\n{ldelim}foreachelse{rdelim}\n{ldelim}/foreach{rdelim}\n');">{ldelim}foreach{rdelim}{ldelim}/foreach{rdelim}</a><hr/>
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}tr{rdelim}{ldelim}/tr{rdelim}\n');">{ldelim}tr{rdelim}{ldelim}/tr{rdelim}</a><hr/>
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}literal{rdelim}\n{ldelim}/literal{rdelim}\n');">{ldelim}literal{rdelim}{ldelim}/literal{rdelim}</a><hr/>
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}* *{rdelim}\n');">{ldelim}* *{rdelim}</a><hr/>
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}{literal}strip{/literal}{rdelim}{ldelim}{literal}/strip{/literal}{rdelim}\n');">{ldelim}{literal}strip{/literal}{rdelim}{ldelim}{literal}/strip{/literal}{rdelim}</a><hr/>
      <a class="link" href="javascript:setSomeElement('src','\n{ldelim}include file={rdelim}\n');">{ldelim}include{rdelim}</a><hr/>
  	{else}
  		{literal}
  		<a class="link" href="javascript:setSomeElement('src','$instance->setNextUser(\'\');');">{tr}Set next user{/tr}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','$instance->get(\'\');');">{tr}Get property{/tr}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','$instance->set(\'\',\'\');');">{tr}Set property{/tr}</a><hr />
		{/literal}
  		{if $act_info.isInteractive eq 'y'}
			{literal}
  			<a class="link" href="javascript:setSomeElement('src','$instance->complete();');">{tr}Complete{/tr}</a><hr/>
  			<a class="link" href="javascript:setSomeElement('src','if(isset($_REQUEST[\'save\'])){\n  $instance->complete();\n}');">{tr}Process form{/tr}</a><hr/>
			{/literal}
  		{/if}
  		{if $act_info.type eq 'switch'}
  			{literal}
			<a class="link" href="javascript:setSomeElement('src','$instance->setNextActivity(\'\');');">{tr}Set Next act{/tr}</a><hr />  		    
			<a class="link" href="javascript:setSomeElement('src','if() {\n  $instance->setNextActivity(\'\');\n}');">{tr}If:SetNextact{/tr}</a><hr />  		    
			<a class="link" href="javascript:setSomeElement('src','switch($instance->get(\'\')){\n  case:\'\':\n  $instance->setNextActivity(\'\');\n  break;\n}');">{tr}Switch construct{/tr}</a><hr />
			{/literal}
  		{/if}
  	{/if}
  	
    
  	</td>
  	</tr>
  	</table>
  </td>
</tr>
</table>  
</form>
