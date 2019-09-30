ALTER TABLE feedback_for_fellow
        -- Rename old question columns.
	CHANGE `q1` `q_speaks_professionally` VARCHAR(20) NOT NULL,
	CHANGE `q2` `q_eye_contact` VARCHAR(20) NOT NULL,
	CHANGE `q3` `q_solid_handshake` VARCHAR(20) NOT NULL,
	CHANGE `q4` `q_specific_examples` VARCHAR(20) NOT NULL,
	CHANGE `q5` `q_transferable_skills` VARCHAR(20) NOT NULL,
	CHANGE `q6` `q_clear_concise` VARCHAR(20) NOT NULL,
	CHANGE `q7` `q_compelling_storytelling` VARCHAR(20) NOT NULL,
	CHANGE `q8` `q_confidently_persists` VARCHAR(20) NOT NULL,
	CHANGE `q9` `q_personal_connection` VARCHAR(20) NOT NULL,
	CHANGE `q10` `q_continue_connection` VARCHAR(20) NOT NULL,
	-- Add new questions in specific locations.
	ADD `q_body_language` VARCHAR(20) NOT NULL AFTER `q_solid_handshake`,
	ADD `q_prepared_questions` VARCHAR(20) NOT NULL AFTER `q_continue_connection`;
