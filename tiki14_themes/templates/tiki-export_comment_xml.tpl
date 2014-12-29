<comment>
	<title><![CDATA[{$comment.title}]]></title>
	<user><![CDATA[{$comment.userName}]]></user>
	<date>{$comment.commentDate}</date>
	<data><![CDATA[{$comment.data}]]></data>
	{*FIXME*}
	{foreach from=$comment.replies_info.replies item=com}
		{include file='tiki-export_comment_xml.tpl' comment=com}
	{/foreach}
</comment>
