<?php

use Fhaculty\Graph\Graph;
use Graphp\Algorithms\MaxFlow\EdmondsKarp;

require __DIR__ . "/../vendor/autoload.php";

class InstantMass{

    private $comp;
    private $players;
    private $vertexes = array();
    public $graph;

    public function __construct(string $players = null, string $comp = null){
        if($players == null){
            $this->players = json_decode(file_get_contents(__DIR__ . "/../Data/playersv2.json"));
        } else {
            $this->players = json_decode($players);
        }
        if($comp == null){
            $this->comp = json_decode(file_get_contents(__DIR__ . "/../Data/comp.json"));
        } else {
            $this->comp = json_decode($comp);
        }
    }

    public function constructGraph(){
        $graph = new Fhaculty\Graph\Graph();

        $this->vertexes["source"] = $graph->createVertex('source');
        $this->vertexes["source"]->setAttribute('type', 'source');
        $this->vertexes["sink"] = $graph->createVertex('sink');
        $this->vertexes["sink"]->setAttribute('type', 'sink');

        foreach($this->comp as $weapon => $amount){
            $this->vertexes["weapons"][$weapon] = $graph->createVertex($weapon);
            $this->vertexes["weapons"][$weapon]->setAttribute('type', 'weapon');
            $edge = new Fhaculty\Graph\Edge\Directed($this->vertexes["weapons"][$weapon], $this->vertexes["sink"]);
            $edge->setCapacity(intval($amount));
        }

        foreach($this->players as $player => $weapons){
            $this->vertexes["players"][$player] = $graph->createVertex($player);
            $this->vertexes["players"][$player]->setAttribute('type', 'player');
            $edge = new Fhaculty\Graph\Edge\Directed($this->vertexes["source"], $this->vertexes["players"][$player]);
            $edge->setCapacity(1);

            foreach($weapons as $weapon => $priority){
                if(key_exists($weapon, $this->vertexes["weapons"])){
                    $edge = new Fhaculty\Graph\Edge\Directed($this->vertexes["players"][$player], $this->vertexes["weapons"][$weapon]);
                    $edge->setCapacity(1);
                    $edge->setWeight($priority);
                }
            }
        }
        $this->graph = $graph;
    }

    public function executeEdmondsKarp(){
        $edmondsKarp = new EdmondsKarp($this->vertexes["source"], $this->vertexes["sink"]);
        return $edmondsKarp->createGraph();
    }

    public function getRoleAssignment(Graph $maxFlowGraph){
        $result = array();

        $edges = $maxFlowGraph->getEdges();
        foreach($edges as $edge) {
            $edgeStart = $edge->getVertexStart();

            if ($edgeStart->getAttribute('type') == 'player') {
                if ($edge->getFlow() == 1) {
                    $result['roles'][$edge->getVertexEnd()->getId()][] = $edgeStart->getId();
                }
            } elseif($edgeStart->getAttribute('type') == 'weapon') {
                if ($edge->getCapacityRemaining() != 0){
                    $result['missing'][$edgeStart->getId()] = $edge->getCapacityRemaining();
                }
            }
        }

        return $result;
    }

    public function outputVisualGraph($graph){
        $graphviz = new \Graphp\GraphViz\GraphViz();
        $graphviz->display($graph);
    }


    public function setComp($comp){
        $this->comp = $comp;
    }

    public function setPlayers($players){
        $this->players = $players;
    }

}

