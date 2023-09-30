<?php
$content = $_GET[1];
if(isset($content)){
    file_put_contents('tmp/flag.txt',$content);
}else{
    echo 'no date input';
}