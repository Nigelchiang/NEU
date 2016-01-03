<?php

include ('Valite.php');

$valite = new Valite();
$valite->setImage('4.jpeg');
$valite->getHec();
$ert = $valite->run();
//$ert = "1234";
print_r($ert);
echo '<br><img src="../phpcrash/4.jpeg"><br>';
