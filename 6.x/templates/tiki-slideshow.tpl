<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="styles/slideshows/{$prefs.slide_style}" />
		<title>{$page_info.pageName}</title>
		<script type="text/javascript" src="lib/tiki-js.js"></script>
	</head>
	<body>
		<div align="center">
			<div><a href="tiki-index.php?page={$page|escape:url}" class="buttons">{$page}</a></div>
			<table class="maintable" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td colspan="3" class="title"><h1>{$slide_title}</h1></td>
				</tr>
				{capture name=navbar}
				{if $structure eq 'y'}
					<tr class="footer">
						<td width="30%">
							<a class="buttons" accesskey="p" title="previous [alt-p]" href="tiki-slideshow2.php?page_ref_id={$prev_info.page_ref_id}">{$prev_info.pageName}</a>
						</td>
						<td align="center">
							<a class="buttons" href="tiki-slideshow2.php?page_ref_id={$home_info.page_ref_id}">{$home_info.pageName}</a>
						</td>
						<td align="right" width="30%">
							<a class="buttons" title="next [alt-space]" accesskey=" " href="tiki-slideshow2.php?page_ref_id={$next_info.page_ref_id}">{$next_info.pageName}</a>
						</td>
					</tr>
				{else}
					<tr class="footer">
						<td width="30%">
							{if $prev_slide > -1}
								<a class="buttons" accesskey="s" title="back to first [alt-s]" href="tiki-slideshow.php?page={$page|escape:url}&amp;slide={$prev_slide}">{$slide_prev_title|default:"previous"}</a>
							{else}
								<i>{tr}First{/tr}</i>
							{/if}
						</td>
						<td align="center">
							<a accesskey="p" title="previous [alt-p]" href="tiki-slideshow.php?page={$page|escape:url}" class="buttons">{$current_slide} of {$total_slides}</a>
						</td>
						<td align="right" width="30%">
							{if $next_slide < $total_slides}
								<a class="buttons" title="next [alt-space]" accesskey=" " href="tiki-slideshow.php?page={$page|escape:url}&amp;slide={$next_slide}">{$slide_next_title|default:"next"}</a>
							{else}
								<i>{tr}Last{/tr}</i>
							{/if}
						</td>
					</tr>
				{/if}
				{/capture}

				{if $smarty.capture.navbar ne ''}{$smarty.capture.navbar}{/if}

				<tr>
					<td colspan="3" class="Main">{$slide_data}</td>
				</tr>

				{if $smarty.capture.navbar ne ''}{$smarty.capture.navbar}{/if}
			</table>
			{if $tiki_p_edit eq 'y'}
				<a href="tiki-editpage.php?page={$page_info.pageName|escape:url}">o</a>
			{/if}
		</div>
	</body>
</html>

