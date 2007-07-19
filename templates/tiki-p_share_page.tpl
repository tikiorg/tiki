{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-p_share_page.tpl,v 1.1 2007-07-19 20:55:18 lphuberdeau Exp $ *}

<h1><a class="pagetitle" href="tiki-index.php?page={$objectId}">{$objectId}</a></h1>
<p>
<img src="pics/icons/key_active.png" /> {tr}permission assigned to the group{/tr}
</p>
<p>
<img src="pics/icons/key.png" /> {tr}permission assigned to the group through a category{/tr}
</p>
<form method="post" action="">
	<table class="normal">
		<caption>{tr}current permissions{/tr}</caption>
		<col width="100"/>
		<col width="20"/>
		<col width="20"/>
		<col width="20"/>
		<thead>
			<tr>
				<th>{tr}group name{/tr}</th>
				<th>{tr}read{/tr}</th>
				<th>{tr}read &amp; write{/tr}</th>
				<th>{tr}no specific permission{/tr}</th>
			</tr>
		</thead>
		<tfoot>
			<tr class="formcolor">
				<td colspan="4" class="center"><input type="submit" name="apply" value="{tr}apply permissions{/tr}"/></td>
			</tr>
		</tfoot>
		<tbody>
			{section name=i loop=$groups}
			<tr>
				<td>{$groups[i]->name}</td>
				{section name=j loop=$columns}
				{if $groups[i]->getLevel( $columns[j] ) eq 'object'}
				<td><input type="radio" name="priv[{$groups[i]->name}]" value="{$columns[j]}"{if $groups[i]->isSelected( $columns[j] )}  checked="checked"{/if} /></td>
				{/if}
				{if $groups[i]->getLevel( $columns[j] ) eq 'category'}
				<td><a href="" title="{$groups[i]->getSourceCategory($columns[j])}"><img src="pics/icons/key.png" border="0"/></a></td>
				
				{/if}
				{if $groups[i]->getLevel( $columns[j] ) eq 'group'}
				<td><img src="pics/icons/key_active.png" border="0"/></td>
				{/if}
				{/section}
				<td><input type="radio" name="priv[{$groups[i]->name|escape}]" value="none"{if ! $groups[i]->hasSelection()}  checked="checked"{/if} /></td>
			</tr>
			{/section}
		</tbody>
	</table>
	<table>
		<caption>{tr}additional permissions{/tr}</caption>
		<col width="200"/>
		<col width="200"/>
		<tr class="formcolor">
			<td class="heading">{tr}add groups{/tr}</td>
			<td class="heading">{tr}add users{/tr}</td>
		</tr>
		<tr class="formcolor">
			<td>
				<select name="groups[]" multiple="multiple" size="10">
				{section name=i loop=$otherGroups}
					<option{if $sharedObject->isValid( $otherGroups[i] )} selected="selected"{/if}>{$otherGroups[i]}</option>
				{/section}
				</select>
			</td>
			<td>
				<select name="users[]" multiple="multiple" size="10">
				{section name=i loop=$otherUsers}
					<option{if $sharedObject->isValid( '*' . $otherUsers[i] )} selected="selected"{/if}>{$otherUsers[i]}</option>
				{/section}
				</select>
			</td>
		</tr>
		<tr class="formcolor">
			<td colspan="2" class="center"><input type="submit" name="add" value="{tr}add to list{/tr}"/></td>
		</form>
	</table>
</form>
