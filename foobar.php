<?php
$result = '';

for($i = 1; $i < 101; $i++) {
    switch($i) {
        case $i % 3 == 0 && $i % 5 == 0:
            $result .= 'foobar' . ', ';
            break;
        case $i % 3 == 0 && $i % 5 != 0:
            $result .= 'foo' . ', ';
            break;
        case $i % 3 != 0 && $i % 5 == 0:
            $result .= 'bar' . ', ';
            break;
        default:
            $result .= $i . ', ';
            break;
    }
}

echo substr($result, 0, -2);

?>
