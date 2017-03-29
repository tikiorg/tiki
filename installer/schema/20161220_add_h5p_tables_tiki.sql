/*
 * Initial setup of H5P tables
 */

# Keep track of h5p content entities > Pending in Tiki: Add FileId
CREATE TABLE tiki_h5p_contents (
	id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
	file_id			 INT UNSIGNED NOT NULL,	# reference to the file gallery object in tiki_files table
	created_at   TIMESTAMP    NULL,
	updated_at   TIMESTAMP    NULL,
	user_id      INT UNSIGNED NOT NULL,
	title        VARCHAR(255) NOT NULL,
	library_id   INT UNSIGNED NOT NULL,
	parameters   LONGTEXT     NOT NULL,
	filtered     LONGTEXT     NOT NULL,
	slug         VARCHAR(127) NOT NULL,
	embed_type   VARCHAR(127) NOT NULL,
	disable      INT UNSIGNED NOT NULL DEFAULT 0,
	content_type VARCHAR(127) NULL,
	author       VARCHAR(127) NULL,
	license      VARCHAR(7)   NULL,
	keywords     TEXT         NULL,
	description  TEXT         NULL,
	PRIMARY KEY (id),
	UNIQUE KEY `fileId` (`file_id`)

)	ENGINE = MyISAM;

# Keep track of content dependencies
CREATE TABLE tiki_h5p_contents_libraries (
	content_id      INT UNSIGNED      NOT NULL,
	library_id      INT UNSIGNED      NOT NULL,
	dependency_type VARCHAR(31)       NOT NULL,
	weight          SMALLINT UNSIGNED NOT NULL DEFAULT 0,
	drop_css        TINYINT UNSIGNED  NOT NULL,
	PRIMARY KEY (content_id, library_id, dependency_type)
)	ENGINE = MyISAM;



# Keep track of h5p libraries
CREATE TABLE tiki_h5p_libraries (
	id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
	created_at       TIMESTAMP     NULL,
	updated_at       TIMESTAMP     NULL,
	name             VARCHAR(127)  NOT NULL,
	title            VARCHAR(255)  NOT NULL,
	major_version    INT UNSIGNED  NOT NULL,
	minor_version    INT UNSIGNED  NOT NULL,
	patch_version    INT UNSIGNED  NOT NULL,
	runnable         INT UNSIGNED  NOT NULL,
	restricted       INT UNSIGNED  NOT NULL DEFAULT 0,
	fullscreen       INT UNSIGNED  NOT NULL,
	embed_types      VARCHAR(255)  NOT NULL,
	preloaded_js     TEXT          NULL,
	preloaded_css    TEXT          NULL,
	drop_library_css TEXT          NULL,
	semantics        TEXT          NOT NULL,
	tutorial_url     VARCHAR(1023) NOT NULL,
	PRIMARY KEY (id),
	KEY name_version (name, major_version, minor_version, patch_version),
	KEY runnable (runnable)
)	ENGINE = MyISAM;

# Keep track of h5p library dependencies
CREATE TABLE tiki_h5p_libraries_libraries (
	library_id          INT UNSIGNED NOT NULL,
	required_library_id INT UNSIGNED NOT NULL,
	dependency_type     VARCHAR(31)  NOT NULL,
	PRIMARY KEY (library_id, required_library_id)
)	ENGINE = MyISAM;

# Keep track of h5p library translations
CREATE TABLE tiki_h5p_libraries_languages (
	library_id    INT UNSIGNED NOT NULL,
	language_code VARCHAR(31)  NOT NULL,
	translation   TEXT         NOT NULL,
	PRIMARY KEY (library_id, language_code)
)	ENGINE = MyISAM;

# Keep track of temporary files uploaded in editor before saving content
CREATE TABLE tiki_h5p_tmpfiles (
	id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
	path       VARCHAR(255) NOT NULL,
	created_at INT UNSIGNED NOT NULL,
	PRIMARY KEY (id),
	KEY created_at (created_at),
	KEY path (path)
) ENGINE = MyISAM;

# Keep track of results (contents >-< users)  -> Reusing Action log in Tiki?
CREATE TABLE tiki_h5p_results (
	id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
	content_id INT UNSIGNED NOT NULL,
	user_id    INT UNSIGNED NOT NULL,
	score      INT UNSIGNED NOT NULL,
	max_score  INT UNSIGNED NOT NULL,
	opened     INT UNSIGNED NOT NULL,
	finished   INT UNSIGNED NOT NULL,
	time       INT UNSIGNED NOT NULL,
	PRIMARY KEY (id),
	KEY content_user (content_id, user_id)
)	ENGINE = MyISAM;

# Cache table for h5p libraries so we can reuse the existing h5p code for caching
CREATE TABLE tiki_h5p_libraries_cachedassets (
	library_id INT UNSIGNED NOT NULL,
	hash       VARCHAR(64)  NOT NULL,
	PRIMARY KEY (library_id, hash)
) ENGINE = MyISAM;
