START TRANSACTION;

ALTER TABLE events ADD university VARCHAR(255) NOT NULL;
UPDATE events SET university = '(no university set)'; -- just for old data
ALTER TABLE volunteers ADD feedback_nag_address VARCHAR(80) NOT NULL;
UPDATE volunteers SET feedback_nag_address = ''; -- so old data passes, but we won't be using it

ALTER TABLE match_sets_members ADD link_nonce INTEGER NOT NULL;
UPDATE match_sets_members SET link_nonce = FLOOR(RAND() * 2000000000);

CREATE TABLE feedback_for_fellow (
	id INTEGER NOT NULL AUTO_INCREMENT,

	msm_id INTEGER NOT NULL,

	fellow_name VARCHAR(80) NOT NULL,
	fellow_university VARCHAR(80) NOT NULL,
	interviewer_name VARCHAR(80) NOT NULL,

	q1 VARCHAR(20) NOT NULL,
	q2 VARCHAR(20) NOT NULL,
	q3 VARCHAR(20) NOT NULL,
	q4 VARCHAR(20) NOT NULL,
	q5 VARCHAR(20) NOT NULL,
	q6 VARCHAR(20) NOT NULL,
	q7 VARCHAR(20) NOT NULL,
	q8 VARCHAR(20) NOT NULL,
	q9 VARCHAR(20) NOT NULL,
	q10 VARCHAR(20) NOT NULL,

	comments TEXT NOT NULL,

	FOREIGN KEY (msm_id) REFERENCES match_sets_members(match_member_id) ON DELETE CASCADE,
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET=utf8mb4;

COMMIT;
