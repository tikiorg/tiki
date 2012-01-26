{title help="Perspectives"}{tr}Perspectives{/tr}{/title}
{tabset}
	{tab name="{tr}List{/tr}"}
		<a href="tiki-switch_perspective.php">{tr}Return to default perspective{/tr}</a>
		<table class="normal">
			<tr>
				<th>{tr}Perspective{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>
			{cycle values="odd,even" print=false}
			{foreach from=$perspectives item=persp}
				<tr class="{cycle}">
					<td class="text">{$persp.name|escape}</td>
					<td class="action">
						<a href="tiki-switch_perspective.php?perspective={$persp.perspectiveId|escape:url}">{icon _id=arrow_right alt="{tr}Switch to{/tr}"}</a>
						{if $persp.can_edit}
							{self_link _icon=page_edit action=edit _ajax='y' id=$persp.perspectiveId cookietab=3}{tr}Edit{/tr}{/self_link}
						{/if}
						{if $persp.can_remove}
							{self_link action=remove id=$persp.perspectiveId}{icon _id=cross alt="{tr}Delete{/tr}"}{/self_link}
						{/if}
						{if $persp.can_perms}
							<a href="tiki-objectpermissions.php?objectName={$persp.name|escape:"url"}&objectType=perspective&permType=perspective&objectId={$persp.perspectiveId|escape:"url"}">{icon _id=key alt="{tr}Permissions{/tr}"}</a>
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
				<p>{tr}Name:{/tr} <input type="text" name="name"/> <input type="submit" name="create" value="{tr}Create{/tr}"/></p>
			</form>
		{/tab}
	{/if}
	{if $perspective_info && $perspective_info.can_edit}
		{tab name="{tr}Edit{/tr}"}
			<form method="post" action="tiki-edit_perspective.php">
				<p>
					{tr}Name:{/tr}
					<input type="text" name="name" value="{$perspective_info.name|escape}"/>
					<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}"/>
				</p>
				<fieldset id="preferences" class="dropzone" style="text-align: left;">
					<p>{tr}Configurations:{/tr}</p>
					{foreach from=$perspective_info.preferences key=name item=val}
						{preference name=$name source=$perspective_info.preferences}
					{/foreach}
				</fieldset>
				<p>
					<input type="submit" name="save" value="{tr}Save{/tr}"/>
				</p>
			</form>
			<form method="post" id="searchform" action="tiki-edit_perspective.php">
				{remarksbox type="info" title="{tr}Hint{/tr}"}{tr}Search for configurations below and drag them in to the configuration section above.{/tr}{/remarksbox}
				<p>
					<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}"/>
					<input id="criteria" type="text" name="criteria"/>
					<input type="submit" value="{tr}Search{/tr}"/>
				</p>
				<fieldset id="resultzone" class="dropzone" style="text-align: left;"></fieldset>
			</form>
			{jq}
				$('#preferences')
					.droppable( {
						activeClass: 'ui-state-highlight',
						drop: function( e, ui ) {
							$('#preferences').append( ui.draggable );
							$(ui.draggable)
								.draggable('destroy')
								.draggable( {
									distance: 50,
									handle: 'label',
									axis: 'x',
									stop: function( e, ui ) {
										$(this).remove();
									}
								} );
						}
					} )
					.find('div.adminoptionbox').draggable( {
						distance: 50,
						handle: 'label',
						axis: 'x',
						stop: function( e, ui ) {
							$(this).remove();
						}
					} );
				$('#searchform').submit( function(e) {
					e.preventDefault();
					if (typeof ajaxLoadingShow == 'function') { ajaxLoadingShow('resultzone'); }
					$('#resultzone').load( this.action, $(this).serialize(), function() {
						$('#resultzone div.adminoptionbox').draggable( {
							handle: 'label',
							axis: 'y',
							helper: 'clone'
						} ).find('.tikihelp').cluetip({splitTitle: ':', width: '150px', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}, clickThrough: true});
						if (typeof ajaxLoadingHide == 'function') { ajaxLoadingHide(); }
					} );
				} );
			{/jq}
		{/tab}
	{/if}
{/tabset}
