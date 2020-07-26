Getting Started with Yentu
==========================
Before working with yentu, you need to initialize your project directory. 

In both forms, of initialisation a directory named `yentu`, which contains all the configurations and migrations is created in the root of your project's directory. Also, an additional table that stores the migration history is added to the database. The following sections will walk you through the steps required for both strategies.

Starting from Scratch
---------------------
To start using yentu for your migrations you need to initialize the `yentu` directory, and the migration history table for your project. Assuming you installed yentu into your project with composer, when the following command is executed, it will initialize the yentu installation:

    $ php vendor/bin/yentu init -i

[[note]]
If you installed yentu as a PHAR you will have to replace `vendor/bin/yentu` with the path to the location of your PHAR archive.
[[/note]]
    
While this command is running, you will be expected to provide the details of your database connection. Prompts will be provided for the following:

- The type of database, with postgresql, mysql and sqlite as options.
- The hostname of the database
- The port on which the database server is running. This optional prompt can be skipped with an empty value, so the default port of your particular server can be used.
- The name of the database on the server.
- The username and password for connecting to the database server.

More on setting up
------------------
In cases where an interactive command line interface is not required (such as with automated build scripts), you can directly pass the required parameters to the init command and ignore the interactive (`-i`) switch. See [[Command-Reference]] for more details.



Importing a Schema
------------------
If your already has a database, but you do not have any way of managing migrations, you can integrate yentu by importing that schema as an initial migration. Execute the following command in the root 
directory of your project:

    $ php vendor/bin/yentu import -i

This will start an interractive command line session, and you will be required to provide some details about your target database. Depending on the database system you have, you will provide details about hosts, usernames, passwords or database files &mdash; whichever are necessary.

After the import, the `yentu` directory will be created in the root directory of your project. This directory will contain a `migrations` directory as well as a `config` directory. The `config` directory will contain the `default.conf.php` configuration file generated for your database connection. The `migrations` directory, hand will contain the initial migration file `XXXXXXXXXXXX_import.php`, where `XXXXXXXXXXXX` represents the timestamp of the migration.

To test this migration, you can point the database configuration to a new database connection and execute:

    $ php vendor/bin/yentu migrate

This will reproduce your current database schema on the new connection. 
