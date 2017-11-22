<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

class H5P_EditorTikiAjax implements H5PEditorAjaxInterface
{

	/**
	 * Gets latest library versions that exists locally
	 *
	 * @return array Latest version of all local libraries
	 */
	public function getLatestLibraryVersions()
	{
		// Get latest version of local libraries
		$major_versions_sql =
			"SELECT hl.name,
                MAX(hl.major_version) AS major_version
           FROM tiki_h5p_libraries hl
          WHERE hl.runnable = 1
       GROUP BY hl.name";

		$minor_versions_sql =
			"SELECT hl2.name,
                 hl2.major_version,
                 MAX(hl2.minor_version) AS minor_version
            FROM ({$major_versions_sql}) hl1
            JOIN tiki_h5p_libraries hl2
              ON hl1.name = hl2.name
             AND hl1.major_version = hl2.major_version
        GROUP BY hl2.name, hl2.major_version";

		$results = TikiDb::get()->query(
			"SELECT hl4.id,
                hl4.name AS machine_name,
                hl4.title,
                hl4.major_version,
                hl4.minor_version,
                hl4.patch_version,
                hl4.restricted,
                hl4.has_icon
           FROM ({$minor_versions_sql}) hl3
           JOIN tiki_h5p_libraries hl4
             ON hl3.name = hl4.name
            AND hl3.major_version = hl4.major_version
            AND hl3.minor_version = hl4.minor_version");

		$out = [];

		foreach ($results as $row) {
			$out[] = $row;
		}

		return $out;
	}

	/**
	 * Get locally stored Content Type Cache. If machine name is provided
	 * it will only get the given content type from the cache
	 *
	 * @param $machineName
	 *
	 * @return array|object|null Returns results from querying the database
	 */
	public function getContentTypeCache($machineName = NULL)
	{
		$tiki_h5p_libraries_hub_cache = TikiDb::get()->table('tiki_h5p_libraries_hub_cache');

		// Return info of only the content type with the given machine name
		if ($machineName) {
			return $tiki_h5p_libraries_hub_cache->fetchAll(
				['id', 'is_recommended'],
				['$tiki_h5p_libraries_hub_cache' => $machineName]
			);
		}

		return $tiki_h5p_libraries_hub_cache->fetchAll(
			$tiki_h5p_libraries_hub_cache->all()
		);

	}

	/**
	 * Gets recently used libraries for the current author
	 *
	 * @return array machine names. The first element in the array is the
	 * most recently used.
	 */
	public function getAuthorsRecentlyUsedLibraries()
	{
		// TODO (adapt for tiki action log

		$recently_used =[];

/*		$result = TikiDb::get()->query(
			"SELECT library_name, max(created_at) AS max_created_at
         FROM tiki_h5p_events
        WHERE type='content' AND sub_type = 'create' AND user_id = ?
     GROUP BY library_name
     ORDER BY max_created_at DESC",
			get_current_user_id()
		);

		foreach ($result as $row) {
			$recently_used[] = $row->library_name;
		}
*/

		return $recently_used;
	}

	/**
	 * Checks if the provided token is valid for this endpoint
	 *
	 * @param string $token
	 *
	 * @return bool True if successful validation
	 */
	public function validateEditorToken($token)
	{
		// TODO (with accesslib?)

		return true;
	}
}
