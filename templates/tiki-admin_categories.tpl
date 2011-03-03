{title help="Categories" admpage="category"}{tr}Admin Categories{/tr}{/title}

<div class="navbar">
	{button href="tiki-browse_categories.php?parentId=$parentId" _text="{tr}Browse Category{/tr}" _title="{tr}Browse the category system{/tr}"}
	{button href="tiki-edit_categories.php" _text="{tr}Organize Objects{/tr}" _title="{tr}Organize Objects{/tr}"}
</div>

{if !empty($errors)}
	<div class="simplebox highlight">{section name=ix loop=$errors}{$errors[ix]}{/section}</div>
{/if}

<div class="tree" id="top">
	<div class="treetitle">{tr}Current category:{/tr} 
		<a href="tiki-admin_categories.php?parentId=0" class="categpath">{tr}Top{/tr}</a>
		{section name=x loop=$path}
			&nbsp;::&nbsp;
			<a class="categpath" href="tiki-admin_categories.php?parentId={$path[x].categId}">{$path[x].name|escape}</a>
		{/section}
		<br />
		{tr}Current Category ID:{/tr} {$parentId}
	</div>
</div>

{section name=dx loop=$catree}
	{assign var=after value=$smarty.section.dx.index_next}
	{assign var=before value=$smarty.section.dx.index_prev}
	{if $smarty.section.dx.index > 0 and $catree[dx].deep > $catree[$before].deep}
		<div id="id{$catree[$before].categId}" style="display:{if $catree[$before].incat eq 'y'}inline{else}none{/if};">
	{/if}
	<div class="treenode{if $catree[dx].categId eq $smarty.request.parentId}select{/if}">
	<!-- {$catree[dx].parentId} :: {$catree[dx].categId} :: -->
	{if $catree[dx].children > 0}
		<i class="mini">{$catree[dx].children} {tr}Child categories{/tr}</i>
	{/if}
		
	{if $catree[dx].objects > 0}
		<i class="mini">{$catree[dx].objects} {tr}Child categories{/tr}</i>
	{/if}
		
	<a href="tiki-admin_categories.php?parentId={$catree[dx].parentId}&amp;categId={$catree[dx].categId}" title="{tr}Edit{/tr}">{icon _id='page_edit' hspace="5" vspace="1"}</a>
	<a href="tiki-admin_categories.php?parentId={$catree[dx].categId}" title="{tr}View{/tr}">{icon _id='magnifier' hspace="5" vspace="1"}</a>
	<a href="tiki-admin_categories.php?parentId={$catree[dx].parentId}&amp;removeCat={$catree[dx].categId}" title="{tr}Delete{/tr}">{icon _id='cross' hspace="5" vspace="1"}</a>
		
	{if $catree[dx].has_perm eq 'y'}
		<a title="{tr}Edit permissions for this category{/tr}" href="tiki-objectpermissions.php?objectType=category&amp;objectId={$catree[dx].categId}&amp;objectName={$catree[dx].name|escape:'urlencode'}&amp;permType=all">{icon hspace="5" vspace="1" _id='key_active' alt="{tr}Edit permissions for this category{/tr}"}</a>
	{else}
		<a title="{tr}Assign Permissions{/tr}" href="tiki-objectpermissions.php?objectType=category&amp;objectId={$catree[dx].categId}&amp;objectName={$catree[dx].name|escape:'url'}&amp;permType=all">{icon hspace="5" vspace="1" _id='key' alt="{tr}Assign Permissions{/tr}"}</a>
	{/if}
		
	<div style="display: inline; padding-left:{$catree[dx].deep*30+5}px;">
		<a class="catname" href="tiki-admin_categories.php?parentId={$catree[dx].categId}">{$catree[dx].name|escape}</a>
		{if $smarty.section.dx.last}
			{repeat count=$catree[dx].deep}</div>{/repeat}
		{elseif $catree[dx].deep < $catree[$after].deep}
			<a href="javascript:toggle('id{$catree[dx].categId}');" class="linkmenu">&gt;&gt;&gt;</a>
			</div>
		{elseif $catree[dx].deep eq $catree[$after].deep}
			</div>
		{else}
			</div>
			{repeat count=$catree[dx].deep-$catree[$after].deep}</div>{/repeat}
		{/if}
	</div>
{/section}

{tabset}
	{tab name="{tr}Create/Edit category{/tr}"}
		{if $categId > 0}
			<h2>{tr}Edit this category:{/tr} <b>{$name|escape}</b> </h2>
			{button href="tiki-admin_categories.php?parentId=$parentId#editcreate" _text="{tr}Create New{/tr}" _title="{tr}Create New{/tr}"}
		{else}
			<h2>{tr}Add new category{/tr}</h2>
		{/if}
		<form action="tiki-admin_categories.php" method="post">
			<input type="hidden" name="categId" value="{$categId|escape}" />
			<table class="formcolor">
				<tr>
					<td>{tr}Parent:{/tr}</td>
					<td>
						<select name="parentId">
							<option value="0">{tr}Top{/tr}</option>
							{section name=ix loop=$catree}
								<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $parentId}selected="selected"{/if}>{$catree[ix].categpath|escape}</option>
							{/section}
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Name:{/tr}</td>
					<td><input type="text" size="40" name="name" value="{$name|escape}" /></td>
				</tr>
				<tr>
					<td>{tr}Description:{/tr}</td>
					<td><textarea rows="2" cols="40" name="description">{$description|escape}</textarea></td>
				</tr>
				<tr>
					<td align="center" colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
				</tr>
			</table>
		</form>
	{/tab}

	{if $categId <= 0}
		{tab name="{tr}Batch upload{/tr}"}
			<h2>{tr}Batch upload (CSV file){/tr}<a {popup text='category,description,parent&lt;br /&gt;vegetable,vegetable&lt;br /&gt;potato,,vegetable'}>{icon _id='help'}</a></h2>
			<form action="tiki-admin_categories.php" method="post" enctype="multipart/form-data">
				<input type="file" name="csvlist" /><br />
				<input type="submit" name="import" value="{tr}Add{/tr}" />
			</form>
		{/tab}
	{/if}
	{tab name="{tr}Objects in category{/tr}"}
		<h2>{tr}Objects in category:{/tr} {$categ_name|escape}</b></h2>
		{if $objects}
			<table class="findtable">
				<tr>
					<td class="findtable">{tr}Find{/tr}</td>
					<td class="findtable">
						<form method="get" action="tiki-admin_categories.php">
							<input type="text" name="find" />
							<input type="hidden" name="parentId" value="{$parentId|escape}" />
							<input type="submit" value="{tr}Find{/tr}" name="search" />
							<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
							<input type="hidden" name="find_objects" value="{$find_objects|escape}" />
						</form>
					</td>
				</tr>
			</table>
		{/if}
		<table class="normal">
			<tr>
				<th>&nbsp;</th>
				<th>
					<a href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}#objects">{tr}Name{/tr}</a>
				</th>
				<th>
					<a href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}#objects">{tr}Type{/tr}</a>
				</th>
			</tr>
			{cycle values="even,odd" print=false}
			{section name=ix loop=$objects}
				<tr class="{cycle}">
					<td class="icon">
						<a href="tiki-admin_categories.php?parentId={$parentId}&amp;removeObject={$objects[ix].catObjectId}&amp;fromCateg={$parentId}" title="{tr}Remove from this Category{/tr}">{icon _id='link_delete' alt="{tr}Remove from this Category{/tr}"}</a>
					</td>
					<td class="text">
						<a href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:80:"(...)":true|escape}</a>
					</td>
					<td class="text">{tr}{$objects[ix].type}{/tr}</td>
				</tr>
			{sectionelse}
				{norecords _colspan=3}
			{/section}
		</table>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}
	
	{if $parentId !=0}
		{tab name="{tr}Moving objects between categories{/tr}"}
			<h2>{tr}Moving objects between categories{/tr}</h2>
			<form method="get" action="tiki-admin_categories.php" name="move">
				<input type="hidden" name="parentId" value="{$parentId|escape}" />
				<input type="submit" name="unassign" value="{tr}Unassign all objects from this category{/tr}" />
				<br />
				<select name="toId">
				{section name=ix loop=$catree}
					<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $parentId}selected="selected"{/if}>{$catree[ix].categpath|escape}</option>
				{/section}
				</select>
				<input type="submit" name="move_to" value="{tr}Move all the objects from this category to this one{/tr}" />
				<br />
				<select name="to">
				{section name=ix loop=$catree}
					<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $parentId}selected="selected"{/if}>{$catree[ix].categpath|escape}</option>
				{/section}
				</select>
				<input type="submit" name="copy_from" value="{tr}Assign all objects of this category to this one{/tr}" />
			</form>
		{/tab}
				
		{tab name="{tr}Add objects to category{/tr}"}
			<h2>{tr}Add objects to category:{/tr} <b>{$categ_name|escape}</b></h2>
			<table class="findtable">
				<tr>
					<td class="findtable">{tr}Find{/tr}</td>
					<td>
						<form method="get" action="tiki-admin_categories.php">
							<input type="text" name="find_objects" />
							<input type="hidden" name="parentId" value="{$parentId|escape}" />
							<input type="submit" value="{tr}Filter{/tr}" name="search_objects" />
							<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
							<input type="hidden" name="offset" value="{$offset|escape}" />
							<input type="hidden" name="find" value="{$find|escape}" />
						</form>
					</td>
				</tr>
			</table>
			{pagination_links cant=$maximum step=$maxRecords offset=$offset}{/pagination_links}
			<form action="tiki-admin_categories.php" method="post">
				<input type="hidden" name="parentId" value="{$parentId|escape}" />
				<table class="formcolor">
					{if $prefs.feature_wiki eq 'y' and $pages}
						<tr>
							<td>{tr}Page:{/tr}</td>
							<td>
								<select name="pageName[]" multiple="multiple" size="5">
									{section name=ix loop=$pages}
										<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td>
								<input type="submit" name="addpage" value="{tr}Add{/tr}" />
							</td>
						</tr>
					{/if}
					
					{if $prefs.feature_articles eq 'y' and $articles}
						<tr>
							<td>{tr}Article:{/tr}</td>
							<td>
								<select name="articleId">
									{section name=ix loop=$articles}
										<option value="{$articles[ix].articleId|escape}">{$articles[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addarticle" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
						
					{if $prefs.feature_blogs eq 'y' and $blogs}
						<tr>
							<td>{tr}Blog:{/tr}</td>
							<td>
								<select name="blogId">
									{section name=ix loop=$blogs}
										<option value="{$blogs[ix].blogId|escape}">{$blogs[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addblog" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
					
					{if $prefs.feature_directory eq 'y'and $directories}
						<tr>
							<td>{tr}Directory:{/tr}</td>
							<td>
								<select name="directoryId">
									{section name=ix loop=$directories}
										<option value="{$directories[ix].categId|escape}">{$directories[ix].name|truncate:40:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="adddirectory" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}

					{if $prefs.feature_galleries eq 'y' and $galleries}
						<tr>
							<td>{tr}image gal:{/tr}</td>
							<td>
								<select name="galleryId">
									{section name=ix loop=$galleries}
										<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addgallery" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
			
					{if $prefs.feature_file_galleries eq 'y' and $file_galleries}
						<tr>
							<td>{tr}File gal:{/tr}</td>
							<td>
								<select name="file_galleryId">
									{section name=ix loop=$file_galleries}
										<option value="{$file_galleries[ix].id|escape}">{$file_galleries[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addfilegallery" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
			
					{if $prefs.feature_forums eq 'y' and $forums}
						<tr>
							<td>{tr}Forum:{/tr}</td>
							<td>
								<select name="forumId">
									{section name=ix loop=$forums}
										<option value="{$forums[ix].forumId|escape}">{$forums[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addforum" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
				
					{if $prefs.feature_polls eq 'y' and $polls}
						<tr>
							<td>{tr}Poll:{/tr}</td>
							<td>
								<select name="pollId">
									{section name=ix loop=$polls}
										<option value="{$polls[ix].pollId|escape}">{$polls[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addpoll" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
					
					{if $prefs.feature_faqs eq 'y and $faqs'}
						<tr>
							<td>{tr}FAQ:{/tr}</td>
							<td>
								<select name="faqId">
									{section name=ix loop=$faqs}
										<option value="{$faqs[ix].faqId|escape}">{$faqs[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addfaq" value="{tr}Add{/tr}" /></td>
						</tr> 
					{/if}
				
					{if $prefs.feature_trackers eq 'y' and $trackers}
						<tr>
							<td>{tr}Tracker:{/tr}</td>
							<td>
								<select name="trackerId">
									{section name=ix loop=$trackers}
										<option value="{$trackers[ix].trackerId|escape}">{$trackers[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addtracker" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
						
					{if $prefs.feature_quizzes eq 'y' and $quizzes}
						<tr>
							<td>{tr}quiz:{/tr}</td>
							<td>
								<select name="quizId">
									{section name=ix loop=$quizzes}
										<option value="{$quizzes[ix].quizId|escape}">{$quizzes[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addquiz" value="{tr}Add{/tr}" /></td>
						</tr>
					{/if}
				</table>
			</form>
			{pagination_links cant=$maximum step=$maxRecords offset=$offset}{/pagination_links}
		{/tab}
	{/if}
{/tabset}
