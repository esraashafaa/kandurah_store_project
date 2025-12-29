<?php
echo "PHP Version: " . phpversion() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "Readonly Support: " . (version_compare(phpversion(), '8.2.0', '>=') ? 'Yes' : 'No') . "\n";
phpinfo();

