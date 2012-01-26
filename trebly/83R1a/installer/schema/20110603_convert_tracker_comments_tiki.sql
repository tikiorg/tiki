INSERT INTO tiki_comments (object, objectType, parentId, userName, commentDate, title, data, hits, type, points, votes, average)
	SELECT
		itemId,
		'trackeritem',
		0,
		user,
		posted,
		title,
		data,
		0,
		'n',
		0,
		0,
		0
	FROM tiki_tracker_item_comments;
DROP TABLE tiki_tracker_item_comments;
