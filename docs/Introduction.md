Introduction
============

Yentu is a database migration tool, built to aid developers keep track of
changes to their database schemas. By using Yentu, developers can keep their
database schema as part of whichever source code management tool they are using.
This also makes it easy for multiple developers to work on a database schema
without much difficulty. Built in PHP, Yentu is framework agnostic and provides 
a CLI interface (which allows integration with build tools and use in other 
languages). 

The syntax for Yentu migrations are designed to be as descriptive as possible, 
without the extra overhead of classes and other boilerplate code. Developers
would generally specify the operations that Yentu should perform when the 
migration is run and Yentu would automatically determine what to do when the 
migration is reversed. Yentu also provides an interface which allows developers
to perform custom reverse operations in cases where necessary.

Yentu currently supports MySQL, PostgreSQL and SQLite. With portablility in mind,
a yentu migration written on a MySQL server could be run on a PostgreSQL server
without any problems.