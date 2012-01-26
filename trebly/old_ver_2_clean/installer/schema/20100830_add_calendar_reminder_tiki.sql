CREATE TABLE IF NOT EXISTS custom_calendar_reminder
(
    reminder_id INT NOT NULL AUTO_INCREMENT,
    calendar_item_id INT NOT NULL,
    reminder_type TINYINT NOT NULL DEFAULT 0,
    fixed_date INT NOT NULL DEFAULT 0,
    time_offset INT NOT NULL DEFAULT 0,
    related_to CHAR(1) NOT NULL DEFAULT 'S',
    when_run CHAR(1) NOT NULL DEFAULT 'B',
    last_sent INT NOT NULL DEFAULT 0,
    times_sent INT NOT NULL DEFAULT 0,
    PRIMARY KEY (reminder_id),
    INDEX (calendar_item_id)
);