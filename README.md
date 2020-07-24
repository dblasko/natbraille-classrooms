# natbraille-classrooms

## Requirements
* PHP : `>= 7.2`
* Composer

When first cloning the project to your machine, please run `composer update` in the app folder since `vendor` is gitignored. This will ensure that you locally have all the dependencies needed and that they are up to date.

## Running the app
### In a development environment
* Make sure to match all the requirements listed above.
* Copy `app/env` to `app/.env`. **🚨 The .env file shouldn't be versioned.**
* Fill ine that file : 
    * `CI_ENVIRONMENT` should be set to `development`
    * Make sure to fill in the **Database** section. An example development setup would look like :
        ```.dotenv
        database.default.hostname = 'localhost:3308'
        database.default.database = 'natbraille-classrooms'
        database.default.username = 'root'
        database.default.password = ''
        database.default.DBDriver = 'MySQLi'
        ```
* Once the environment is configured and your development database is up and running (*the base relational database can be set up with the `db/db_script.sql` script*), you can run the site with the built-in development server.
    * First, get into the `app` folder in your command line (for example `cd app`).
    * You can then run the server with `php spark serve` in your command line. The site is available at `localhost:8080`.
### In a production environment
**This part isn't fully available yet. A docker image should be available for this purpose in the future.**
