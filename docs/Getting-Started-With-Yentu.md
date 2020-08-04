Getting Started with Yentu
==========================
Before you start working with yentu, you have to initialize the `yentu` directory and the migration history table for your project. The `yentu` directory stores all configurations and migrations, while the history table, which resides in your database, tracks all migrations that have already been run on your database.

Assuming yentu was installed with composer, then running the following command creates the `yentu` directory, as well as the history table:

    $ php vendor/bin/yentu init -i

[[note]]
If you installed yentu as a PHAR you will have to replace `vendor/bin/yentu` with the path to the location of your PHAR archive.
[[/note]]
    
While this command is executing, you will be expected to provide the details of your database connection. Prompts will be provided for the following:

- The type of database server, with postgresql, mysql and sqlite as options.
- The hostname of the database.
- The port on which the database server is running. This optional prompt can be skipped with an empty value, so the default port of your particular server can be used.
- The name of the database on the server.
- The username and password for connecting to the database server.

More on setting up
------------------
In cases where an interactive command line interface is not required (such as with automated build scripts), you can directly pass the required parameters to the init command and ignore the interactive (`-i`) switch. See [[Command-Reference]] for more details.

Importing a Schema
------------------
If your project already has a database, but you do not have a way of managing migrations, you can integrate yentu by importing your already existing schema as an initial migration. 

To import the migration, execute the following command in the root  directory of your project:

    $ php vendor/bin/yentu import -i

After the import, the `yentu/migrations` directory will contain the initial migration file, `XXXXXXXXXXXX_import.php`, for your database. If all goes well, this migration should represent the current state of your database. You can now make subsequent changes by writing migrations just as you would with yentu.

### Verifying your import
It's always a good idea to test the migration before proceeding. In some cases, database features that are not supported by yentu may not be included in your imported migration.

To test this migration, you can point the database configuration to a new database connection and execute:

    $ php vendor/bin/yentu migrate

This will reproduce your current database schema on the new connection. At this point, the comparison between the two databases can be performed manually.
