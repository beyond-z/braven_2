-- represents the spreadsheets
CREATE TABLE pm_nag_group (
	id INTEGER AUTO_INCREMENT,

	name VARCHAR(80) NOT NULL,
	created_by VARCHAR(80) NOT NULL, -- an email address of the logged in user, we can use this to suggest it to them again

	default_message TEXT NOT NULL,

	PRIMARY KEY (id)
);

CREATE TABLE pm_nag_group_member (
	id INTEGER AUTO_INCREMENT,

	pm_nag_group_id INTEGER NOT NULL,

	fellow_name VARCHAR(255) NOT NULL,
	pm_name VARCHAR(255) NOT NULL,
	fellow_number VARCHAR(255) NOT NULL,
	pm_number VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
);

-- represents the result
CREATE TABLE pm_nag_group_member_nag_batch (
	id INTEGER AUTO_INCREMENT,

	created_by VARCHAR(80) NOT NULL,
	created_at TIMESTAMP NOT NULL,

	PRIMARY KEY (id)
);

CREATE TABLE pm_nag_group_member_nag (
	id INTEGER AUTO_INCREMENT,

	pm_nag_group_member_nag_batch_id INTEGER NOT NULL,
	pm_nag_group_member_id INTEGER NOT NULL,

	reply TEXT NOT NULL,

	FOREIGN KEY (pm_nag_group_member_nag_batch_id) REFERENCES pm_nag_group_member_nag_batch(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (pm_nag_group_member_id) REFERENCES pm_nag_group_member(id) ON UPDATE CASCADE ON DELETE CASCADE,

	PRIMARY KEY (id)
);
