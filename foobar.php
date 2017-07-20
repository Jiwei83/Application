<?php
    $result = '';
    for($i = 1; $i <= 100; $i++) {
        if (($i % 15) == 0) {
            $result .= "foobar ";
        }
        else if (($i % 5) == 0) {
            $result .= "bar ";
        }
        else if (($i % 3) == 0) {
            $result .= "foo ";
        }
        else {
            $result .= $i." ";
        }  
    }
    echo $result."\r\n";
?>
