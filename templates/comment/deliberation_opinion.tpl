{$headerlib->clear_js()}
<div>
	<div>{tr}Opinion{/tr}</div>
	{textarea name="forum_deliberation_description[]" class="forum_deliberation_description" _simple="y" codemirror="y" syntax="tiki" _toolbars=$prefs.feature_forum_parse}{/textarea}
	{tr}Opinion Options{/tr}
</div>
{rating_override_menu type="comment"}
{$headerlib->output_js_files()}
<script>{$headerlib->output_js()}</script>