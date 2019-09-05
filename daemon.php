<?php
require_once './worker.php';

$inputstream='php://input';
$json = file_get_contents( $inputstream );
$worker = new Worker();
echo $worker->getResult($json);
