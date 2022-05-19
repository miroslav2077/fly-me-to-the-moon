<?php

class Graph
{
    private $verticesNumber;

    // Adjacency list
    private $adjList;

    private $solutions;

    function __construct($verticesNumber)
    {

        // Initialise vertex count
        $this->verticesNumber = $verticesNumber;

        // Initialise adjacency list
        $this->adjList = array($this->verticesNumber);

        for ($i = 0; $i < $this->verticesNumber; $i++) {
            $this->adjList[$i] = array();
        }
    }

    // Add edge 'from' -> 'to'
    public function addEdge($from, $to)
    {
        array_push($this->adjList[$from], $to);
    }

    /* Get all paths that goes 'from' -> 'to' */
    public function getAllPaths($from, $to)
    {
        $isVisited = array_fill(0, $this->verticesNumber, false);
        $pathList = array();

        // add source to path[]
        array_push($pathList, $from);

        // Call recursive utility
        return $this->getAllPathsUtil($from, $to, $isVisited, $pathList);
    }

    /* Recursive function for getting all paths 'from' -> 'to'. isVisited keeps track of already visited vertices in current path to avoid looping
       currentIterationPathList stores current vertices in the path */
    private function getAllPathsUtil($from, $to, $isVisited, $currentIterationPathList)
    {

        if ($from == $to) {
            if ($this->solutions == null) {
                $this->solutions = array();
            }
            array_push($this->solutions, $currentIterationPathList);
            // if match is found, add it to the solutions and stop recurring
            return;
        }

        // Mark the current node
        $isVisited[$from] = true;

        // Recur for all the vertices adjacent to current vertex
        for ($i = 0; $i < sizeof($this->adjList[$from]); $i++) {
            if (!$isVisited[$this->adjList[$from][$i]]) {
                // store current node
                // in path[]
                array_push($currentIterationPathList, $this->adjList[$from][$i]);
                $this->getAllPathsUtil($this->adjList[$from][$i], $to, $isVisited, $currentIterationPathList);

                // remove current node
                // in path[]
                array_splice($currentIterationPathList, array_search($this->adjList[$from][$i], $currentIterationPathList), 1);
            }
        }

        // Mark the current node
        $isVisited[$from] = false;

        return $this->solutions;
    }
}
