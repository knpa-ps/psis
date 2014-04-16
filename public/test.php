<?php 

$str = ":34:2:33:";
echo '<pre>';
print_r(explode(':', trim($str,':')));
echo '</pre>';
die();