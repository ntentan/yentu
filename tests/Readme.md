# Running `yentu` Tests

Tests for yentu are written in a somewhat unconventional form. Most of them were written before I had a proper understanding of how tests are written, and although I really do not want to go into what that should actually be, I still wonder: do I really know how tests should be written. Of course, that's beside the point of this writeup. Anyway, as it turns out, I just migrated machines, and I can't seem to get the original tests to run. So in that regard, I am writing this brief documentation to help myself, and any other crazy people that might consider taking up this endeavour someday.

## Getting started
Quintessential, isn't it? First you need to decide which databases you intend to run your tests on. Inside the `tests/config` directory, there are sample configuration files for all the supported database platforms. These are specific PHPUnit configuration files with which you'll invoke the tests. You need to decide the platform on which you want to tests, and rename the configuration for that file from `.xml.sample` to `.xml`. Once that is done, you need to ensure that the databases you have in the said configuration files are active.

 
