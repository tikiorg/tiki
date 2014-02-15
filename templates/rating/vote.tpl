{rating type=$type id=$id}
{if $tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y'}
	{rating_result type=$type id=$id}
{/if}
