<?php

require __DIR__ . '/../vendor/autoload.php';
require "InstantMass.php";

$instantMass = new InstantMass();
$instantMass->constructGraph();
$maxFlowGraph = $instantMass->executeEdmondsKarp();
$roleAssignment = $instantMass->getRoleAssignment($maxFlowGraph);
$instantMass->outputVisualGraph($maxFlowGraph);
print_r($roleAssignment);