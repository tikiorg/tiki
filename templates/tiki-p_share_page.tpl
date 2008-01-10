{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-p_share_page.tpl,v 1.2.2.2 2008-01-10 20:44:40 pkdille Exp $ *}

<h1><a class="pagetitle" href="tiki-index.php?page={$objectId}">{$objectId}</a></h1>
<p>
<img src="pics/icons/key_active.png" /> {tr}Permission assigned to the group{/tr}
</p>
<p>
<img src="pics/icons/key.png" /> {tr}Permission assigned to the group through a category{/tr}
</p>
<form method="post" action="">
	<table class="normal">
		<caption>{tr}Current permissions{/tr}</caption>
		<col width="100"/>
		<col width="20"/>
		<col width="20"/>
		<col width="20"/>
		<thead>
			<tr>
				<th>{tr}Group name{/tr}</th>
				<th>{tr}Read{/tr}</th>
				<th>{tr}Read &amp; write{/tr}</th>
				<th>{tr}No specific permission{/tr}</th>
			</tr>
		</thead>
		<tfoot>
			<tr class="formcolor">
				<td colspan="4" class="center"><input type="submit" name="apply" value="{tr}Apply permissions{/tr}"/></td>
			</tr>
		</tfoot>
		<tbody>
			{section name=i loop=$groups}
			<tr>
				<td>{$groups[i]->name}</td>
				{section name=j loop=$columns}
				{if $groups[i]->getLevel( $columns[j] ) eq 'object'}
				<td><input type="radio" name="priv[{$groups[i]->name}]" value="{$columns[j]}"{if $groups[i]->isSelected( $columns[j] )} checked="checked"{/if} /></td>
				{/if}
				{if $groups[i]->getLevel( $columns[j] ) eq 'category'}
				<td><a href="" title="{$groups[i]->getSourceCategory($columns[j])}"><img src="pics/icons/key.png" border="0"/></a></td>
				
				{/if}
				{if $groups[i]->getLevel( $columns[j] ) eq 'group'}
				<td><img src="pics/icons/key_active.png" border="0"/></td>
				{/if}
				{/section}
				<td><input type="radio" name="priv[{$groups[i]->name|escape}]" value="none"{if ! $groups[i]->hasSelection()} checked="checked"{/if} /></td>
			</tr>
			{/section}
		</tbody>
	</table>
	<table>
		<caption>{tr}Additional permissions{/tr}</caption>
		<col width="200"/>
		<col width="200"/>
		<tr class="formcolor">
			<td class="heading">{tr}Add Groups{/tr}</td>
			<td class="heading">{tr}Add Users{/tr}</td>
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
			<td colspan="2" class="center"><input type="submit" name="add" value="{tr}Add to List{/tr}"/></td>
		</tr>
		</form>
	</table>
</form>
