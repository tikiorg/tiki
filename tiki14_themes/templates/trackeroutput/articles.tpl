<ul>
	{foreach $field.articleIds as $id}
		<li>{object_link type=article id=$id}</li>
	{foreachelse}
		<li>{tr}No articles{/tr}</li>
	{/foreach}
</ul>
