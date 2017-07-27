ALTER TABLE tiki_tabular_formats ADD config TEXT DEFAULT NULL;
UPDATE tiki_tabular_formats SET config = '{"simple_headers": 0, "import_update": 1, "import_transaction": 0}';
