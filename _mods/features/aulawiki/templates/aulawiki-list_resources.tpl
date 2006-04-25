<table class="normal">
	<tr>
		<td class="heading">&nbsp;</td>
		<td class="heading">{tr}Name{/tr}</td>
		{if $viewDesc!="n"}
			<td class="heading">{tr}Description{/tr}</td>
		{/if}
		{if $viewType!="n"}
			<td class="heading">{tr}Type{/tr}</td>
		{/if}
		<td class="heading">{tr}Creation date{/tr}</td>
		<td class="heading">&nbsp;</td>
		<td class="heading">&nbsp;</td>
		<td class="heading">&nbsp;</td>
		<td class="heading">&nbsp;</td>
	</tr>
     {foreach key=key item=object from=$resources}
     	{cycle values="odd,even" assign="parImpar"}
     	<tr>
     		<td class="{$parImpar}"><img align="bottom" border=0 src="images/aulawiki/edu_{$object.type|replace:" ":""}.gif"/></td>
      		<td class="{$parImpar}"><a class="link" href="{$object.href}">{$object.name}</a></td>
      		{if $viewDesc!="n"}
	      		<td class="{$parImpar}">{$object.description}</td>
	      	{/if}
      		{if $viewType!="n"}
				<td class="{$parImpar}">{$object.type}</td>
			{/if}
      		<td class="{$parImpar}">{$object.created|date_format:"$short_date_format $short_time_format"}</td>
      		<td class="{$parImpar}">
      		{include file="aulawiki-copy_to_clipboard.tpl" copyIdObj=$object.objId copyType=$object.type copyName=$object.name copyDesc=$object.description copyHref=$object.href} 
      		</td>
      		<td class="{$parImpar}">
      			{if $object.type=="wiki page"}
	      			<a class="link" href="aulawiki-objectpermissions.php?objectName={$object.name|escape:"url"}&amp;objectType={$object.type|replace:" ":"+"}&amp;permType=wiki&amp;resourceIdName={$object.objId}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' height="16" width="17" border='0' /></a>
	      		{elseif $object.type=="structure"}
	      			<a class="link" href="aulawiki-objectpermissions.php?objectName={$object.name|escape:"url"}&amp;objectType=wiki+page&amp;permType=wiki&amp;resourceIdName={$object.name}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' height="16" width="17" border='0' /></a>
      			{elseif $object.type=="image gallery"}
      				<a class="link" href="aulawiki-objectpermissions.php?objectName={$object.name|escape:"url"}&amp;objectType={$object.type|replace:" ":"+"}&amp;permType=image+galleries&amp;resourceIdName={$object.objId}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' height="16" width="17" border='0' /></a>
      			{elseif $object.type=="quiz"}
      				<a class="link" href="aulawiki-objectpermissions.php?objectName={$object.name|escape:"url"}&amp;objectType={$object.type|replace:" ":"+"}&amp;permType=quizzes&amp;resourceIdName={$object.objId}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' height="16" width="17" border='0' /></a>
      			{elseif $object.type=="file gallery"}
      				<a class="link" href="aulawiki-objectpermissions.php?objectName={$object.name|escape:"url"}&amp;objectType={$object.type|replace:" ":"+"}&amp;permType=file+galleries&amp;resourceIdName={$object.objId}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' height="16" width="17" border='0' /></a>
      			{elseif $object.type=="workspace" || $object.type=="calendar" || $object.type=="assignments"}
      				<a class="link" href="aulawiki-objectpermissions.php?objectName={$object.name|escape:"url"}&amp;objectType={$object.type|replace:" ":"+"}&amp;permType={$object.type|replace:" ":"+"}&amp;resourceIdName={$object.objId}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' height="16" width="17" border='0' /></a>
      			{else}
      				<a class="link" href="aulawiki-objectpermissions.php?objectName={$object.name|escape:"url"}&amp;objectType={$object.type|replace:" ":"+"}&amp;permType={$object.type|replace:" ":"+"}s&amp;resourceIdName={$object.objId}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' height="16" width="17" border='0' /></a>
      			{/if}
      		</td>
      		<td class="{$parImpar}">
      		<a class="link" href="{$object.adminURL}"><img src='img/icons/config.gif' alt='{tr}Admin resource{/tr}' title='{tr}Admin resource{/tr}' border='0' /></a>
      		</td>
      		<td class="{$parImpar}"><a class="link" href="{$object.removeURL}">
      		   <img src='img/icons2/delete.gif' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
		    </td>
      	</tr>
     {/foreach}
     </table>
