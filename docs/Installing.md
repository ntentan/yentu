Installation
============
Yentu can either be installed as a phar archive or through composer. The
preference for either format is dependent on the developer. Developers who use
composer for managing project dependencies are however better off installing 
through composer.

To install yentu with composer you can require using:

    php composer.phar require ekowabaka/yentu

After running composer install, you can access the yentu binary with 
`vendor/bin/yentu`.

For the phar version you can download the most recent version through:

    curl -LSs https://yentu.github.io/installer.php | php

This would check for Yentu's requirements and install the most recent phar 
version if all requirements are met.
