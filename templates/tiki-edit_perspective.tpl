{title help="Perspectives"}{tr}Perspectives{/tr}{/title}
{tabset}
	{tab name="{tr}List{/tr}"}
		<a href="tiki-switch_perspective.php">{tr}Return to default perspective{/tr}</a>
		<table class="data">
			<tr>
				<th>{tr}Perspective{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>
			{foreach from=$perspectives item=persp}
				<tr>
					<td>{$persp.name|escape}</td>
					<td>
						<a href="tiki-switch_perspective.php?perspective={$persp.perspectiveId|escape:url}">{icon _id=arrow_right}</a>
						{if $persp.can_edit}
							{self_link action=edit id=$persp.perspectiveId cookietab=3}{icon _id=page_edit}{/self_link}
						{/if}
						{if $persp.can_remove}
							{self_link action=remove id=$persp.perspectiveId}{icon _id=cross}{/self_link}
						{/if}
						{if $persp.can_perms}
							<a href="tiki-objectpermissions.php?objectName={$persp.name|escape:"url"}&objectType=perspective&permType=perspective&objectId={$persp.perspectiveId|escape:"url"}">{icon _id=key}</a>
						{/if}
					</td>
				</tr>
			{/foreach}
		</table>
		{pagination_links offset=$offset step=$prefs.maxRecords cant=$count}{/pagination_links}
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
					<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}"/>
				</p>
				<fieldset id="preferences" class="tabcontent" style="text-align: left;">
					<p>{tr}Configurations{/tr}:</p>
					{foreach from=$perspective_info.preferences key=name item=val}
						{preference name=$name source=$perspective_info.preferences}
					{/foreach}
				</fieldset>
				<p>
					<input type="submit" name="edit" value="{tr}Edit{/tr}"/>
				</p>
			</form>
			<form method="post" id="searchform" action="tiki-edit_perspective.php">
				<p>{tr}Search for configurations and drag them in the configuration section above.{/tr}</p>
				<p>
					<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}"/>
					<input id="criteria" type="text" name="criteria"/>
					<input type="submit" value="{tr}Search{/tr}"/>
				</p>
				<fieldset id="resultzone" class="tabcontent" style="text-align: left;"></fieldset>
			</form>
			{jq}
				$jq('#preferences').droppable( {
					activeClass: 'ui-state-highlight',
					drop: function( e, ui ) {
						$jq('#preferences').append( ui.draggable );
					}
				} );
				$jq('#searchform').submit( function(e) {
					e.preventDefault();
					$jq('#resultzone').load( this.action, $jq(this).serialize(), function() {
						$jq('#resultzone div.adminoptionbox').draggable( {
							handle: 'label',
							axis: 'y',
							helper: 'clone'
						} );
					} );
				} );
			{/jq}
		{/tab}
	{/if}
{/tabset}
