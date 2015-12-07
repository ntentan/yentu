Introduction
============

Yentu is a database migration tool, built to aid developers keep track of
changes to their database schemas. The word Yentu is an Akan expression which means
let's migrate. Yentu is intended to help developers keep their database schema 
as a part of whichever source code management system they are using.
It also makes it easy for multiple developers to work on a database schema
without much integration difficulty. Built with PHP, Yentu is framework agnostic 
and can be used in just about any PHP project. Yentu provides a CLI interface 
which makes it easy to integrate with build tools and even use in projects in
other languages.

The syntax for Yentu migrations are designed to be as descriptive as possible, 
without the extra overhead of classes and other boilerplate code. Developers
would generally specify the operations that Yentu should perform when the 
migration is run and Yentu would automatically determine what to do when the 
migration is reversed. Yentu also provides an interface which allows developers
to perform custom reverse operations in cases where necessary.

Yentu currently supports MySQL, PostgreSQL and SQLite. With portablility in mind,
a yentu migration written on a MySQL server could be run on a PostgreSQL server
without any problems.