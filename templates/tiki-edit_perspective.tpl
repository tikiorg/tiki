{title help="Perspectives"}{tr}Perspectives{/tr}{/title}
{tabset}

	{tab name="{tr}List{/tr}"}
		<h2>{tr}List{/tr}</h2>
		<a href="tiki-switch_perspective.php">{tr}Return to default perspective{/tr}</a>
		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
			<table class="table table-striped table-hover">
				<tr>
					<th>{tr}Perspective{/tr}</th>
					<th></th>
				</tr>

				{foreach from=$perspectives item=persp}
					<tr>
						<td class="text">
							{if $persp.can_edit}
								{self_link _icon_name='edit' action=edit _ajax='y' _menu_text='y' _menu_icon='y' id=$persp.perspectiveId cookietab=3}
									{$persp.name|escape}
								{/self_link}
							{else}
								<a href="tiki-switch_perspective.php?perspective={$persp.perspectiveId|escape:url}">
									{icon name='move' _menu_icon='y' alt="{tr}Switch to{/tr}"} {$persp.name|escape}
								</a>
							{/if}
							</td>
						<td class="action">
							{capture name=perspective_actions}
								{strip}
									{$libeg}<a href="tiki-switch_perspective.php?perspective={$persp.perspectiveId|escape:url}">
										{icon name='move' _menu_text='y' _menu_icon='y' alt="{tr}Switch to{/tr}"}
									</a>{$liend}
									{if $persp.can_perms}
										{$libeg}{permission_link mode=text type="perspective" id=$persp.perspectiveId title=$persp.name}{$liend}
									{/if}
									{if $persp.can_edit}
										{$libeg}{self_link _icon_name='edit' action=edit _ajax='y' _menu_text='y' _menu_icon='y' id=$persp.perspectiveId cookietab=3}
											{tr}Edit{/tr}
										{/self_link}{$liend}
									{/if}
									{if $persp.can_remove}
										{$libeg}{self_link action=remove id=$persp.perspectiveId _menu_text='y' _menu_icon='y' _icon_name='remove'}
											{tr}Delete{/tr}
										{/self_link}{$liend}
									{/if}
								{/strip}	
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.perspective_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.perspective_actions}</ul></li></ul>
							{/if}
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
			<form method="post" action="tiki-edit_perspective.php" class="form-inline">
                <div class="form-group">
				    <label>{tr}Name:{/tr} </label>
                        <input type="text" name="name" class="form-control">
                </div>
                <input type="submit" class="btn btn-default" name="create" value="{tr}Create{/tr}">
			</form>
		{/tab}
	{/if}

	{if $perspective_info && $perspective_info.can_edit}
		{tab name="{tr}Edit{/tr}"}
			<h2>{tr}Edit{/tr}</h2>
			<form method="post" action="tiki-edit_perspective.php" class="form-horizontal">
				<div class="form-group clearfix">
					<label for="name" class="col-sm-2 control-label">{tr}Name{/tr}</label>
					<div class="col-sm-10">
                        <input type="text" name="name" id="name" value="{$perspective_info.name|escape}" class="form-control">
                    </div>
    				<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}">
				</div>
				<div class="col-sm-offset-2">
					<fieldset id="preferences" class="panel panel-default dropzone">
						<div class="panel-heading">{tr}Preference List{/tr}</div>
						<div class="panel-body">
							{foreach from=$perspective_info.preferences key=name item=val}
								{preference name=$name source=$perspective_info.preferences}
							{/foreach}
					</fieldset>
				</div>
				<div class="text-center">
					<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
				</div>
			</form>
			<form method="post" id="searchform" action="tiki-edit_perspective.php" class="form col-sm-offset-2 clearfix" role="form">
				{remarksbox type="info" title="{tr}Hint{/tr}"}
					{tr}Search preferences below and drag them into the preference list above.{/tr}
				{/remarksbox}
				<div class="panel panel-default">
					<input type="hidden" name="id" value="{$perspective_info.perspectiveId|escape}">
					<div class="panel-body clearfix">
						<div class="input-group">
							<span class="input-group-addon">
								{icon name="search"}
							</span>
							<input id="criteria" type="text" name="criteria" class="form-control" placeholder="{tr}Search preferences{/tr}...">
							<div class="input-group-btn">
								<input type="submit" class="btn btn-default" value="{tr}Search{/tr}">
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<fieldset id="resultzone" class="dropzone"></fieldset>
					</div>
				</div>
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
							scroll: true,
							cursor: 'move',
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
