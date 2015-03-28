{title help="Perspectives"}{tr}Perspectives{/tr}{/title}
{tabset}

	{tab name="{tr}List{/tr}"}
		<h2>{tr}List{/tr}</h2>
		<a href="tiki-switch_perspective.php">{tr}Return to default perspective{/tr}</a>
		<div class="table-responsive">
			<table class="table normal table-striped table-hover">
				<tr>
					<th>{tr}Perspective{/tr}</th>
					<th></th>
				</tr>

				{foreach from=$perspectives item=persp}
					<tr>
						<td class="text">{$persp.name|escape}</td>
						<td class="action">
							{capture name=perspective_actions}
								{strip}
									<a href="tiki-switch_perspective.php?perspective={$persp.perspectiveId|escape:url}">
										{icon name='move' _menu_text='y' _menu_icon='y' alt="{tr}Switch to{/tr}"}
									</a>
									{if $persp.can_perms}
										{permission_link mode=text type="perspective" id=$persp.perspectiveId title=$persp.name}
									{/if}
									{if $persp.can_edit}
										{self_link _icon_name='edit' action=edit _ajax='y' _menu_text='y' _menu_icon='y' id=$persp.perspectiveId cookietab=3}
											{tr}Edit{/tr}
										{/self_link}
									{/if}
									{if $persp.can_remove}
										{self_link action=remove id=$persp.perspectiveId _menu_text='y' _menu_icon='y' _icon_name='remove'}
											{tr}Delete{/tr}
										{/self_link}
									{/if}
								{/strip}
							{/capture}
							<a class="tips"
							   title="{tr}Actions{/tr}"
							   href="#" {popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.perspective_actions|escape:"javascript"|escape:"html"}
							   style="padding:0; margin:0; border:0"
									>
								{icon name='wrench'}
							</a>
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
		{pagination_links offset=$offset step=$prefs.maxRecords cant=$count}{/pagination_links}
	{/tab}

	{if $tiki_p_perspective_create eq 'y'}
		{tab name="{tr}Create{/tr}"}
			<h2>{tr}Create{/tr}</h2>
			<form method="post" action="tiki-edit_perspective.php">
				<p>{tr}Name:{/tr} <input type="text" name="name"/> <input type="submit" class="btn btn-default btn-sm" name="create" value="{tr}Create{/tr}"></p>
			</form>
		{/tab}
	{/if}

	{if $perspective_info && $perspective_info.can_edit}
		{tab name="{tr}Edit{/tr}"}
			<h2>{tr}Edit{/tr}</h2>
			<form method="post" action="tiki-edit_perspective.php">
				<div class="form-group clearfix">
					<label for="name" class="col-sm-4 control-label">{tr}Name{/tr}</label>
					<div class="col-sm-8">
                        <input type="text" name="name" id="name" value="{$perspective_info.name|escape}" class="form-control">
                    </div>
    					<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}">
				</div>
				<fieldset id="preferences" class="panel panel-default dropzone" style="text-align: left;">
					<p class="panel-heading">{tr}Configurations:{/tr}</p>
					{foreach from=$perspective_info.preferences key=name item=val}
						{preference name=$name source=$perspective_info.preferences}
					{/foreach}
				</fieldset>
				<p>
					<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
				</p>
			</form>
			<form method="post" id="searchform" action="tiki-edit_perspective.php">
				{remarksbox type="info" title="{tr}Hint{/tr}"}{tr}Search for configurations below and drag them in to the configuration section above.{/tr}{/remarksbox}
				<p>
					<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}">
					<input id="criteria" type="text" name="criteria">
					<input type="submit" class="btn btn-default btn-sm" value="{tr}Search{/tr}">
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
						} );
						$(this).tiki_popover();
						if (typeof ajaxLoadingHide == 'function') { ajaxLoadingHide(); }
					} );
				} );
			{/jq}
		{/tab}
	{/if}
{/tabset}
