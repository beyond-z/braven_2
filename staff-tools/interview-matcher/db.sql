-- the database layout as sql script, you can just `mysql db < thisfile.sql` to create.

START TRANSACTION;

SET NAMES utf8mb4;

-- ******************
-- The list of potential interests. All things should reference back
-- to this somehow.
CREATE TABLE interests (
	id INTEGER NOT NULL AUTO_INCREMENT,
	interest VARCHAR(128) NOT NULL UNIQUE,
	CHECK (interest = LOWER(interest)), -- i want all lowercase interests to simplify matching
	INDEX (interest),
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET=utf8mb4;

-- ******************
-- The matching is done per-event, so we create a new event and tie all
-- the spreadsheet data and match data to it.
CREATE TABLE events (
	id INTEGER NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	when_created TIMESTAMP NOT NULL,
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET=utf8mb4;

-- ******************
CREATE TABLE volunteers (
	id INTEGER NOT NULL AUTO_INCREMENT,
	event_id INTEGER NOT NULL,
	name VARCHAR(255) NOT NULL,
	vip BOOLEAN NOT NULL,
	available BOOLEAN NOT NULL,
	is_virtual BOOLEAN NOT NULL, -- doesn't seem to be used....
	contact_number VARCHAR(255) NOT NULL,
	-- interests are done in the following table
	FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE volunteer_interests (
	volunteer_id INTEGER NOT NULL,
	interest_id INTEGER NOT NULL,
	FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE,
	FOREIGN KEY (interest_id) REFERENCES interests(id) ON DELETE CASCADE,
	PRIMARY KEY (volunteer_id, interest_id)
) DEFAULT CHARACTER SET=utf8mb4;

-- ******************
CREATE TABLE fellows (
	id INTEGER NOT NULL AUTO_INCREMENT,
	event_id INTEGER NOT NULL,
	name VARCHAR(255) NOT NULL,
	score INTEGER NOT NULL,
	available BOOLEAN NOT NULL,
	-- interests are done in the following table
	FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE fellow_interests (
	fellow_id INTEGER NOT NULL,
	interest_id INTEGER NOT NULL,
	FOREIGN KEY (fellow_id) REFERENCES fellows(id) ON DELETE CASCADE,
	FOREIGN KEY (interest_id) REFERENCES interests(id) ON DELETE CASCADE,
	PRIMARY KEY (fellow_id, interest_id)
) DEFAULT CHARACTER SET=utf8mb4;


-- ******************
-- when you run a match, it creates one of these...
CREATE TABLE match_sets (
	id INTEGER NOT NULL AUTO_INCREMENT,
	event_id INTEGER NOT NULL,
	when_created TIMESTAMP NOT NULL,

	FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET=utf8mb4;

-- ******************
-- and the match has a one-to-many relationship to these
CREATE TABLE match_sets_members (
	match_member_id INTEGER NOT NULL AUTO_INCREMENT,
	match_set_id INTEGER NOT NULL,
	volunteer_id INTEGER NOT NULL,
	fellow_id INTEGER NOT NULL,

	FOREIGN KEY (match_set_id) REFERENCES match_sets(id) ON DELETE CASCADE,
	FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE,
	FOREIGN KEY (fellow_id) REFERENCES fellows(id) ON DELETE CASCADE,
	PRIMARY KEY (match_member_id)
) DEFAULT CHARACTER SET=utf8mb4;

COMMIT;
