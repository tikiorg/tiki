--Create a temporary table to join everything on from the options selected
DROP TABLE IF EXISTS temp_tracker_field_options;

CREATE TABLE temp_tracker_field_options (
	trackerIdThere INT,
	trackerIdHere INT,
	fieldIdThere INT,
	fieldIdHere INT,
	displayFieldIdThere INT,
	displayFieldIdHere INT,
	linkToItems INT,
	type VARCHAR(1)
);

INSERT INTO temp_tracker_field_options
SELECT
	REPLACE(SUBSTRING(
		SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 1),
		LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 1 -1)) + 1
		),
	',', ''),
	tiki_tracker_fields.trackerId,
	REPLACE(SUBSTRING(
		SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 2),
		LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 2 -1)) + 1
		),
	',', ''),
	REPLACE(SUBSTRING(
		SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 3),
		LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 3 -1)) + 1
		),
	',', ''),
	REPLACE(SUBSTRING(
		SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 4),
		LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 4 -1)) + 1
		),
	',', ''),
	tiki_tracker_fields.fieldId,
	REPLACE(SUBSTRING(
		SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 5),
		LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 5 -1)) + 1
		),
	',', ''),
	tiki_tracker_fields.type
FROM tiki_tracker_fields
WHERE tiki_tracker_fields.type = 'l';

--Now create the update/join script
UPDATE
	tiki_tracker_item_fields

LEFT JOIN tiki_tracker_fields ON 
	tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
LEFT JOIN tiki_trackers ON 
	tiki_trackers.trackerId = tiki_tracker_fields.trackerId
LEFT JOIN temp_tracker_field_options items_left_display ON
	items_left_display.displayFieldIdHere = tiki_tracker_item_fields.fieldId

LEFT JOIN tiki_tracker_item_fields items_left ON (
	items_left.fieldId = items_left_display.fieldIdHere AND
	items_left.itemId = tiki_tracker_item_fields.itemId
)

LEFT JOIN tiki_tracker_item_fields items_middle ON (
	items_middle.value = items_left.value AND
	items_left_display.fieldIdThere = items_middle.fieldId
)

LEFT JOIN tiki_tracker_item_fields items_right ON (
	items_right.itemId = items_middle.itemId AND
	items_right.fieldId = items_left_display.displayFieldIdThere
)

LEFT JOIN tiki_tracker_items ON (
	tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
)

SET
	tiki_tracker_item_fields.value = items_right.value
	
WHERE
	LENGTH(items_right.value) > 0 AND
	tiki_tracker_fields.type = 'l'



SELECT
	items_left.itemId,
	tiki_tracker_items.status,
	tiki_tracker_item_fields.itemId,
	tiki_tracker_fields.trackerId,
	tiki_tracker_item_fields.value
	
FROM tiki_tracker_item_fields 
LEFT JOIN tiki_tracker_fields ON 
	tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
LEFT JOIN tiki_trackers ON 
	tiki_trackers.trackerId = tiki_tracker_fields.trackerId
LEFT JOIN temp_tracker_field_options items_left_display ON
	items_left_display.displayFieldIdHere = tiki_tracker_item_fields.fieldId

LEFT JOIN tiki_tracker_item_fields items_left ON (
	items_left.fieldId = items_left_display.fieldIdHere AND
	items_left.itemId = tiki_tracker_item_fields.itemId
)

LEFT JOIN tiki_tracker_item_fields items_middle ON (
	items_middle.value = items_left.value AND
	items_left_display.fieldIdThere = items_middle.fieldId
)

LEFT JOIN tiki_tracker_item_fields items_right ON (
	items_right.itemId = items_middle.itemId AND
	items_right.fieldId = items_left_display.displayFieldIdThere
)

LEFT JOIN tiki_tracker_items ON (
	tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
)
	
GROUP BY 
	tiki_tracker_item_fields.itemId
ORDER BY 
	tiki_tracker_items.lastModif
