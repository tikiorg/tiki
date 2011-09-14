DELETE FROM tiki_preferences WHERE name='versionOfPreferencesCache'; # In case Tiki was used after the code upgrade before running this patch
UPDATE tiki_preferences SET name = 'versionOfPreferencesCache' WHERE name='lastUpdatePrefs';
