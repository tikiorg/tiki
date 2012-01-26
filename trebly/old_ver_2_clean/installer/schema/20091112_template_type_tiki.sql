-- 2009-11-12 lphuberdeau : Add a field to indicate the type of template

ALTER TABLE tiki_content_templates ADD COLUMN template_type VARCHAR( 20 ) NOT NULL DEFAULT 'static' AFTER templateId;

