-- In case Tiki was used after the code upgrade before running this patch
DELETE FROM tiki_preferences WHERE name='versionOfPreferencesCache';

UPDATE tiki_preferences SET name = 'versionOfPreferencesCache' WHERE name='lastUpdatePrefs';
