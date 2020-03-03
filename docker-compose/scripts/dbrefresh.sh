#!/bin/bash
echo "Refreshing your local dev database from the staging db"

dbfilename='bebraven_staging_db_latest.sql.gz'
dbMockIvfilename='bebraven_staging_mock_iv_db_latest.sql.gz'

aws s3 cp "s3://bebraven-staging-db-dumps/$dbfilename" - | gunzip | sed -e "
  s/https:\/\/staging.bebraven.org/http:\/\/bravenweb:3007/g;
  s/stagingplatform.bebraven.org/platformweb:3020/g;
  s/stagingjoin.bebraven.org/joinweb:3001/g;
" | docker-compose exec -T bravendb mysql -h bravendb -u wordpress "-pwordpress" wordpress
if [ $? -ne 0 ]
then
 echo "Failed restoring from: s3://bebraven-staging-db-dumps/$dbfilename"
 echo "Make sure that awscli is installed: pip3 install awscli"
 echo "Also, make sure and run 'aws configure' and put in your Access Key and Secret."
 echo "Lastly, make sure your IAM account is in the Developers group. That's where the policy to access this bucket is defined."
 exit 1;
fi

# Note: this container has some issues with creating the braven_interview_matcher database.
# Go see the TODO in docker-compose/scripts/docker_compose_run.sh for the commands to run
# before this will work.
aws s3 cp "s3://bebraven-staging-db-dumps/$dbMockIvfilename" - | gunzip \
  | docker-compose exec -T bravendb mysql -h bravendb -u wordpress "-pwordpress" braven_interview_matcher
if [ $? -ne 0 ]
then
 echo "Failed restoring from s3://bebraven-staging-db-dumps/$braven_interview_matcher"
 exit 1;
fi

# Now get the up to date plugins and uploads content.
./docker-compose/scripts/contentrefresh.sh
