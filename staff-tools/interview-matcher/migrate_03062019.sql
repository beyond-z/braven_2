START TRANSACTION;

-- these are nullable because they may not be set during auto-saves
ALTER TABLE feedback_for_fellow ADD when_started TIMESTAMP NULL;
ALTER TABLE feedback_for_fellow ADD when_submitted TIMESTAMP NULL;
ALTER TABLE feedback_for_fellow ADD when_last_changed TIMESTAMP NULL;

CREATE UNIQUE INDEX msmid_for_feedback ON feedback_for_fellow (msm_id);

-- this one is null just because it is not strictly necessary; it is for convenience of matching grades
ALTER TABLE fellows ADD email_address VARCHAR(80) NULL;

COMMIT;
