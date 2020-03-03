# https://bebraven.org
The Wordpress code running our public facing website.

## Development environment setup

1. Follow the instructions at: https://github.com/beyond-z/development
2. There is a bug I haven't fixed with database setup, so go run the commands marked with TODO in `docker-compose/scripts/docker_compose_run.sh`
3. Then, from your application root just run: `./docker-compose/scripts/dbrefresh.sh`
4. One last restart for good measure: `./docker-compose/scripts/restart.sh`

When complete, the app will be available at: `http://bravenweb:3007`

Note: Currently the admin login credentials are the same as production. See [this commit](https://github.com/beyond-z/devops/commit/e241732df70839b57f4ddb4b4c42b8f198ffcf9f) for how the database is created if you ever want to fix the dbrefresh script to create local, easy to remember, dev passwords.

Some things to keep in mind with Docker:
* If there are build errors, run `docker-compose logs` to see what they
  are.
* The `wp-config.php` is created on the fly using `wp-config-sample.php` and injecting environment
  variables from docker-compose.yml. See the `docker_compose_run.sh` script.
* There are more scripts in `./docker-compose/scripts` to help you work
  with the container(s).

## Development Process

Have a look at [this section](https://github.com/beyond-z/development/blob/master/README.md#development) of the overall development setup for our entire enviornment (all apps) for an overview.
