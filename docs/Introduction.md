Introduction
============

Like any other database migration tool, Yentu allows developers to track changes they make to their database schemas. By writing scripts that define the incremental changes that are made to the database, the actual evolution of the database is captured along with the rest of the codebase &mdash; and this is done through whatever source control management system that may be in use. The word "Yentu" is an Akan word which means "let's migrate". 

Yentu is framework agnostic, and although it's built for use with the Ntentan framework, it can be used in just about any PHP project. Apart from the migration scripts that you write, Yentu has very little interference with your codebase. Most of the interactions you'll have with yentu will occur on the command line; something your build systems will love too.

Unlike most other migration systems, migrations for yentu are designed to be as descriptive as possible. This means you don't have the extra overhead of classes and other boilerplate code. Migrations you write will roughly specify the structure of the modification to be made to the schema, and yentu will figure out what to execute when migrations are brought up or down. This approach, of course, removes a lot of flexibility in favour of simplicity. You still have the option &mdash; albeit a little limited &mdash; to run some simple operations when certain migration events are taking place.

Yentu currently supports MySQL, PostgreSQL and SQLite. If you keep portablility in mind while writing your migrations, a database specified in yentu can easily be run on any of the supported platforms.