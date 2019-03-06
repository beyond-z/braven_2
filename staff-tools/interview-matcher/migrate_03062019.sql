START TRANSACTION;

ALTER TABLE feedback_for_fellow ADD when_started TIMESTAMP NULL;
ALTER TABLE feedback_for_fellow ADD when_submitted TIMESTAMP NULL;
ALTER TABLE feedback_for_fellow ADD when_last_changed TIMESTAMP NULL;

CREATE UNIQUE INDEX msmid_for_feedback ON feedback_for_fellow (msm_id);

COMMIT;
