<?php

require __DIR__ . '/../vendor/autoload.php';
require "InstantMass.php";

$comp = $_POST['comp'];
$players = $_POST['players'];

$instantMass = new InstantMass($players, $comp);
$instantMass->constructGraph();
$maxFlowGraph = $instantMass->executeEdmondsKarp();
$roleAssignment = $instantMass->getRoleAssignment($maxFlowGraph);

foreach( $roleAssignment['roles'] as $key => $role){
    foreach( $role as $player){
        echo "<p>" . $player . " = " . $key . "</p>";
    }
}