{tikimodule error=$module_params.error title=$tpl_module_title name="months_links" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{assign var=i value=0 }
{if $feature eq 'cms'}
	{assign var=itemlink value='tiki-read_article.php?articleId='}
{else}
	{assign var=itemlink value='tiki-view_blog_post.php?postId='}
{/if}
<script type="text/javascript" >
<!--
	function mlchange(id) {
		var e = document.getElementById('ml-sub-' + id);
		var zip = document.getElementById('ml-icon-' + id);
		if(zip.innerHTML == '►' ) {
			e.style.display = 'block';
			zip.innerHTML = '▼'
		} else {
			e.style.display = 'none';
			zip.innerHTML = '►'
		}
}
//-->
</script>
{modules_list list=$archives nonums='y'}
	{foreach from=$archives key=year_number item=year_data}
		{if $year_expanded == 0 }
			{assign var=year_expanded value=$year_number }
		{/if}
		{if $year_number == $year_expanded }
			{assign var=i value=$i+1}
			<li class='archivedate expanded' id='ml-li-{$module_id}-{$i}' >
				<a class="toggle" href="javascript:void();" >
					<span class="zippy " id='ml-icon-{$module_id}-{$i}' >○</span>
				</a>
				<a class="linkmodule" href="javascript:void();">{$year_number}</a>
				<span class="post-count badge" dir="ltr">{$year_data.cant}</span>
				<ul id='ml-sub-{$module_id}-{$i}' >
					{foreach from=$year_data.monthlist key=month_name item=month_data}
						{if $month_name == $month_expanded }
							{assign var=i value=$i+1}
							<li class='archivedate expanded' id='ml-{$module_id}-{$i}' >
								<a class="toggle" href="javascript:mlchange('{$module_id}-{$i}')" >
									<span class="zippy " id='ml-icon-{$module_id}-{$i}' >▼</span>
								</a>
								<a class="linkmodule" href="{$month_data.link}">{$month_name}</a>
								<span class="post-count badge" dir="ltr">{$month_data.cant}</span>
								<ul id='ml-sub-{$module_id}-{$i}' >
									{foreach from=$month_data.postlist key=articleId item=title}
										<li class='archivedate collapsed' >
											<a class="linkmodule" href="{$itemlink}{$articleId}">{$title}</a>
										</li>
									{/foreach}
								</ul>
							</li>
						{else}
							{assign var=i value=$i+1}
							<li class='archivedate collapsed' id='ml-{$module_id}-{$i}' >
								<a class="toggle" href="javascript:mlchange('{$module_id}-{$i}')" >
									<span class="zippy " id='ml-icon-{$module_id}-{$i}'>►</span>
								</a>
								<a class="linkmodule" href="{$month_data.link}">{$month_name}</a>
								<span class="post-count badge" dir="ltr">{$month_data.cant}</span>
								<ul id='ml-sub-{$module_id}-{$i}' >
									{foreach from=$month_data.postlist key=articleId item=title}
										<li class='archivedate collapsed' >
											<a class="linkmodule" href="{$itemlink}{$articleId}">{$title}</a>
										</li>
									{/foreach}
								</ul>
							</li>
						{/if}
					{/foreach}
				</ul>
			</li>
		{else}
			{assign var=i value=$i+1}
			<li class='archivedate collapsed' id='ml-li-{$module_id}-{$i}' >
				<a class="toggle" href="{$year_data.link}" >
					<span class="zippy " id='ml-icon-{$module_id}-{$i}' >●</span>
				</a>
				<a class="linkmodule" href="{$year_data.link}">{$year_number}</a>
				<span class="post-count badge" dir="ltr">{$year_data.cant}</span>
			</li>
		{/if}
	{foreachelse}
		{if $feature eq 'cms'}
			{tr}No article found{/tr}
		{else}
			{tr}No blog post found{/tr}
		{/if}
	{/foreach}
{/modules_list}

{/tikimodule}
