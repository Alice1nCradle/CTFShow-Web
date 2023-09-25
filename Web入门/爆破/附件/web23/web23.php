
<?php
    for ($i = 1; $i <= 5000; $i++){
        $token = md5($i);
        if (substr($token, 1, 1) === substr($token, 14, 1) && substr($token, 14, 1) === substr($token, 17, 1)) {
            if((intval(substr($token, 1,1)) + intval(substr($token, 14,1)) + substr($token, 17,1)) / substr($token, 1,1) === intval(substr($token, 31,1))){
                echo $i;
            }
            }
        }
?>