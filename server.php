<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once 'helpers/Graph.php';

require_once 'helpers/Hash.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\EventLoop\Loop;
use React\Http\Server as HttpServer;
use React\Socket\SocketServer;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

/* Wildcard (*) CORS origin for practicality */
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/', function (Request $request, Response $response) {
    $html = file_get_contents('public/index.html');
    $response->getBody()->write($html);
    return $response;
});

/* Return Airports list */
$app->get(
    '/airports',
    function (Request $request, Response $response) {
        try {
            $airportsJson = @file_get_contents("data/airports.json");

            if ($airportsJson === false) {
                throw new Exception("File not found!");
            }
        } catch(Exception $e) {
            $response->getBody()->write(json_encode([]));
            return $response;
        }

        $airports = json_decode($airportsJson);

        $response->getBody()->write(json_encode($airports));
        return $response;
    }
);

$app->get(
    '/flights/{from}/{to}',
    function (Request $request, Response $response, $args) {
        try {
            $airportsJson = @file_get_contents("data/airports.json");
            $flightsJson = @file_get_contents("data/flights.json");

            if ($airportsJson === false || $flightsJson === false) {
                throw new Exception("File not found!");
            }
        } catch(Exception $e) {
            $response->getBody()->write(json_encode([]));
            return $response;
        }

        $airports = json_decode($airportsJson);
        $flights = json_decode($flightsJson);
        
        
        $airportsHashMap = Hash::getHashMap($airports, "code");

        $g = new Graph(sizeof($airports));

        // Use Airport's ID as the nodes' name
        foreach($flights as $flight) {
            $g->addEdge($airportsHashMap[$flight->code_departure]->id, $airportsHashMap[$flight->code_arrival]->id);
        }
        
        // Departure id
        $s = (int)$args["from"];

        // Arrival id
        $d = (int)$args["to"];

        $airportsIDHashMap = Hash::getHashMap($airports, "id");
        
        $flightsJointHashMap = Hash::getJointHashMap($flights, "code_departure", "code_arrival");
        $routes = array();
        
        $solutions = $g->getAllPaths($s, $d);

        if ($solutions) {
            // Fill up the IDs of the solution nodes with actual airport data for client consumption
            foreach ($solutions as $solutionNodes) {
                if (sizeof($solutionNodes) < 5) {
                    $tempRoutes = array("total" =>  0, "routes" => array());
                    
                    // Traverse the current solution by subsets of 2 nodes to connect airports and get the matching price [(1, 0), 4] -> [1, (0, 4)]
                    for ($i = 0; $i < sizeof($solutionNodes)-1; $i++) {
                        $tempRoute = array();
                        $tempRoute["from"] = $airportsIDHashMap[$solutionNodes[$i]];
                        $tempRoute["to"] = $airportsIDHashMap[$solutionNodes[$i+1]];
                        $tempRoute["price"] = $flightsJointHashMap[$tempRoute["from"]->code . $tempRoute["to"]->code]->price;
    
                        array_push($tempRoutes["routes"], $tempRoute);
                        $tempRoutes["total"] += $tempRoute["price"];
                    }
    
                    array_push($routes, $tempRoutes);
                }
            }
            // Sort the routes by total cost
            usort($routes, fn($a, $b) => $a["total"] <=> $b["total"]);
        }

        $response->getBody()->write(json_encode($routes));
        return $response;
    }
);

$loop = Loop::get();

$server = new HttpServer(
    function (Request $request) use ($app) {
        return $app->handle($request);
    }
);

$socket = new SocketServer('127.0.0.1:8080');

$server->listen($socket);

$loop->run();