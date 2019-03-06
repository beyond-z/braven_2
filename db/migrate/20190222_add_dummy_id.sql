USE braven_interview_matcher;
ALTER TABLE fellow_interests DROP PRIMARY KEY, ADD dummy_id INT PRIMARY KEY AUTO_INCREMENT, ADD UNIQUE INDEX unique_index (`fellow_id`, `interest_id`);

USE braven_interview_matcher;
ALTER TABLE volunteer_interests DROP PRIMARY KEY, ADD dummy_id INT PRIMARY KEY AUTO_INCREMENT, ADD UNIQUE INDEX unique_index (`volunteer_id`, `interest_id`);

USE wordpress;
ALTER TABLE bz_term_relationships DROP PRIMARY KEY, ADD dummy_id INT PRIMARY KEY AUTO_INCREMENT, ADD UNIQUE INDEX unique_index (`object_id`, `term_taxonomy_id`);
