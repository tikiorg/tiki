{if $prefs.feature_categories eq 'y' and $tiki_p_modify_object_categories eq 'y' and (count($categories) gt 0 or $tiki_p_admin_categories eq 'y')}
	{if !isset($labelcol)}
		{$labelcol = '3'}
	{/if}
	{if !isset($inputcol)}
		{$inputcol = '9'}
	{/if}
	{if !isset($notable) || $notable neq 'y'}
		<!-- div class="form-horizontal" -->
			<div class="form-group">
				<label class="col-sm-{$labelcol} control-label">
					{tr}Categorize{/tr}
				</label>
				<div class="col-sm-{$inputcol}">
					{if isset($colsCategorize)} colspan="{$colsCategorize}"{/if}
				</div>

		<!--/div-->
	{/if}
	{if $mandatory_category >= 0 or $prefs.javascript_enabled neq 'y' or (isset($auto) and $auto eq 'y')}
		<div id="categorizator"><div>
	{else}
		{if !isset($notable) || $notable neq 'y'} <div class="col-sm-{$inputcol}">{button href="#" _flip_id='categorizator' _class='btn btn-default btn-sm tips' _text="{tr}Select Categories{/tr}" _flip_default_open='n'}</div>{/if}
		<div class="clearfix"><div id="categorizator" class="col-sm-{$inputcol} col-sm-offset-{$labelcol}" style="display:{if isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y' or (isset($notable) && $notable eq 'y')}block{else}none{/if};">
	{/if}
	<div class="multiselect">
		{if count($categories) gt 0}
			{$cat_tree}
			<input type="hidden" name="cat_categorize" value="on">
			<div class="clearfix">
				{if $tiki_p_admin_categories eq 'y'}
					<div class="pull-right">
						<a class="btn btn-default btn-sm tips" href="tiki-admin_categories.php" title=":{tr}Admin Categories{/tr}">
							{icon name="cog"} {tr}Categories{/tr}
						</a>
					</div>
				{/if}

				{select_all checkbox_names='cat_categories[]' label="{tr}Select/deselect all categories{/tr}"}
			</div> {* end .clear *}

		{else}
			<div class="clearfix">
				{if $tiki_p_admin_categories eq 'y'}
					<div class="pull-right">
						<a class="btn btn-link" href="tiki-admin_categories.php" title=":{tr}Admin Categories{/tr}">
							{icon name="cog"} {tr}Categories{/tr}
						</a>
					</div>
				{/if}
			</div> {* end .clear *}
			{tr}No categories defined{/tr}
		{/if}
	</div> {* end #multiselect *}
	    </div>
    </div>{* end #categorizator *}
	{if !isset($notable) || $notable neq 'y'}
		</div> {* end .form-group *}
	{/if}
{/if}

{if $prefs.feature_theme_control_autocategorize eq 'y'}
	{jq}
		if ('{{$smarty.session.tc_theme_cat}}') {
			if ($('.tree ul[data-id="{{$prefs.feature_theme_control_parentcategory}}"] input[type=checkbox]:checked').length == 0) {
				$('#categ-{{$smarty.session.tc_theme_cat}}').prop("checked", true);
			}
		}
	{/jq}
{/if}
