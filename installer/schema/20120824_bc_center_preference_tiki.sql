
-- Upgrade only: Set the center tag character to :: unless it was manually set to ::: (which is the new default)
INSERT IGNORE INTO tiki_preferences (name, value) VALUES ('feature_use_three_colon_centertag', 'n');
