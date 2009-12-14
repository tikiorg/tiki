{title}{tr}Perspectives{/tr}{/title}
{tabset}
	{tab name="{tr}List{/tr}"}
		<table class="data">
			<tr>
				<th>{tr}Perspective{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>
			{foreach from=$perspectives item=persp}
				<tr>
					<td>{$persp.name|escape}</td>
					<td>
						{if $persp.can_edit}
							{self_link action=edit perspectiveId=$persp.perspectiveId}{icon _id=page_edit}{/self_link}
						{/if}
						{if $persp.can_remove}
							{self_link action=remove perspectiveId=$persp.perspectiveId}{icon _id=cross}{/self_link}
						{/if}
						{if $persp.can_perms}
							<a href="tiki-objectpermissions.php?objectName={$persp.name|escape:"url"}&objectType=perspective&permType=perspective&objectId={$persp.perspectiveId|escape:"url"}">{icon _id=key}</a>
						{/if}
					</td>
				</tr>
			{/foreach}
		</table>
	{/tab}
	{if $tiki_p_perspective_create eq 'y'}
		{tab name="{tr}Create{/tr}"}
			<form method="post" action="tiki-edit_perspective.php">
				<p>{tr}Name{/tr}: <input type="text" name="name"/> <input type="submit" name="create" value="{tr}Create{/tr}"/></p>
			</form>
		{/tab}
	{/if}
	{if $perspective_info && $perspective_info.can_edit}
		{tab name="{tr}Edit{/tr}"}
			<form method="post" action="tiki-edit_perspective.php">
				<p>
					{tr}Name{/tr}:
					<input type="text" name="name" value="{$perspective_info.name|escape}"/>
					<input type="hidden" name="perspectiveId" value="{$perspective_info.perspectiveId|escape}"/>
				</p>
				<div>
					{preference name=category_jail source=$perspective_info.preferences}
				</div>
				<p>
					<input type="submit" name="edit" value="{tr}Edit{/tr}"/>
				</p>
			</form>
		{/tab}
	{/if}
{/tabset}
