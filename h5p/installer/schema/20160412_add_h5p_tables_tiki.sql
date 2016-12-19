/*
 * Initial setup of H5P tables
 */

# Keep track of h5p content entities
CREATE TABLE tiki_h5p_contents (
	id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
	created_at   TIMESTAMP    NOT NULL DEFAULT 0,
	updated_at   TIMESTAMP    NOT NULL DEFAULT 0,
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
	PRIMARY KEY (id)
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

# Keep track of data/state when users use content (contents >-< users)
CREATE TABLE tiki_h5p_contents_user_data (
	content_id     INT UNSIGNED     NOT NULL,
	user_id        INT UNSIGNED     NOT NULL,
	sub_content_id INT UNSIGNED     NOT NULL,
	data_id        VARCHAR(127)     NOT NULL,
	data           LONGTEXT         NOT NULL,
	preload        TINYINT UNSIGNED NOT NULL DEFAULT 0,
	invalidate     TINYINT UNSIGNED NOT NULL DEFAULT 0,
	updated_at     TIMESTAMP        NOT NULL DEFAULT 0,
	PRIMARY KEY (content_id, user_id, sub_content_id, data_id)
)	ENGINE = MyISAM;

# Create a relation between tags and content
CREATE TABLE tiki_h5p_contents_tags (
	content_id INT UNSIGNED NOT NULL,
	tag_id     INT UNSIGNED NOT NULL,
	PRIMARY KEY (content_id, tag_id)
)	ENGINE = MyISAM;

# Keep track of tags
CREATE TABLE tiki_h5p_tags (
	id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(31)  NOT NULL,
	PRIMARY KEY (id)
)	ENGINE = MyISAM;

# Keep track of results (contents >-< users)
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

# Keep track of h5p libraries
CREATE TABLE tiki_h5p_libraries (
	id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
	created_at       TIMESTAMP     NOT NULL,
	updated_at       TIMESTAMP     NOT NULL,
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

# Keep track of logged h5p EVENTS
CREATE TABLE tiki_h5p_events (
	id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id         INT UNSIGNED NOT NULL,
	created_at      INT UNSIGNED NOT NULL,
	type            VARCHAR(63)  NOT NULL,
	sub_type        VARCHAR(63)  NOT NULL,
	content_id      INT UNSIGNED NOT NULL,
	content_title   VARCHAR(255) NOT NULL,
	library_name    VARCHAR(127) NOT NULL,
	library_version VARCHAR(31)  NOT NULL,
	PRIMARY KEY (id)
)	ENGINE = MyISAM;

# A SET of GLOBAL counters TO keep track of H5P USAGE
CREATE TABLE tiki_h5p_counters (
	type            VARCHAR(63)  NOT NULL,
	library_name    VARCHAR(127) NOT NULL,
	library_version VARCHAR(31)  NOT NULL,
	num             INT UNSIGNED NOT NULL,
	PRIMARY KEY (type, library_name, library_version)
)	ENGINE = MyISAM;

CREATE TABLE tiki_h5p_libraries_cachedassets (
	library_id INT UNSIGNED NOT NULL,
	hash       VARCHAR(64)  NOT NULL,
	PRIMARY KEY (library_id, hash)
) ENGINE = MyISAM;

