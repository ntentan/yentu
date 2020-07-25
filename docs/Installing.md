Installation
============
Yentu can either be installed as a phar archive executable, or through composer. The preference for either format dependends on how your environments is setup. If you use composer for managing your project dependencies,you are better off installing through composer. On the other hand, if you have a couple of separate projects that require yentu, you can install the phar archive executable.

To install yentu with composer you can execute the following:

    php composer.phar require --dev ekowabaka/yentu

After running composer install, you can access the yentu binary with 
`vendor/bin/yentu` from your projects base directory.

For the phar version you can always download the most recent version through:

    curl -LSs https://yentu.github.io/installer.php | php

This will check for Yentu's requirements and download the most recent phar 
version, provided your system's environment meets all requirements.
