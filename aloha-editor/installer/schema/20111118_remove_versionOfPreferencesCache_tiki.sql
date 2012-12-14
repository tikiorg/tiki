# lastUpdatePrefs is the name of versionOfPreferencesCache before Tiki 8
DELETE FROM tiki_preferences WHERE name IN ('versionOfPreferencesCache', 'lastUpdatePrefs');