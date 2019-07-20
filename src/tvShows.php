<?php
use PSR\Http\Message\ServerRequestInterface as Request;
use PSR\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/welcome/{user}', function(Request $request, Response $response)
{
    $user = $request->getAttribute('user');
    $response->getBody()->write("Welcome $user");
});

//CREATE route
$app->post('/loadTvShowsDB', function(Request $request, Response $response)
{
    try{
        $collection = (new MongoDB\Client)->test->tvShows;
        $collection->drop();
        $insertOneResult = $collection->insertMany([
                [
                "title"=> "A Game Of Thrones",
                "released"=> "2011",
                "cast"=> ["emilia clarke", "kit harrington", "peter dinklage"] 
                ],
            
                [
                "title"=> "Dexter",
                "released"=> "2006",
                "cast"=> ["michael c hall", "jennifer carpenter", "david zayas"]
                ],
            
                [
                "title"=> "Breaking Bad",
                "released"=> 2008,
                "cast" =>  ["bryan cranston", "aaron paul"]		
                ]   
            ]);
        printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
    }
    catch(MongoDB\Exception $e){
        printf(json_encode($e->getMessage()));
    }
});

//READ route
$app->get('/getTvShows',function(Request $request, Response $response){
    try{
        $collection = (new MongoDB\Client)->test->tvShows;
        $findAllResult = $collection->find();
        foreach ($findAllResult as $result)
        {   
            printf('Title : ' . $result->{'title'} . '<br>' . 'Release : ' . $result->{'released'} . '<br>' . 'Cast : ');
            foreach($result->{'cast'} as $cast)
            {
                printf($cast . ', ');
            }
            printf('<br>');
        }
    }
    catch(MongoDB\Exception $e){
        printf(json_encode($e->getMessage()));
    }   
});

//UPDATE route
$app->put('/updateOneTitle/{title}', function(Request $request, Response $response)
{
    try{     
    $currentTitle = $request->getAttribute('title');
    $newTitle = $request->getParam('title'); 
    $collection = (new MongoDB\Client)->test->tvShows;
    $updateResult = $collection->updateOne(
        ['title' => $currentTitle], 
        ['$set' => ['title' => $newTitle]]
    );
    printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
    printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
}
catch(MongoDB\Exception $e){
    printf(json_encode($e->getMessage()));
}  
});

//DELETE Route
$app->delete('/deleteDemo', function(Request $request, Response $response)
{
    try {
        $collection = (new MongoDB\Client)->test->tvShows;
        $insertDummy = $collection->insertOne(['_id'=> 1 ], ['title'=> 'dummy']);
        $deleteOneResult = $collection->deleteOne(['_id' => 1]);
        printf("Deleted %d document(s)\n",$deleteOneResult->getDeletedCount());
    } catch (MongoDB\Exception $e) {
        printf(json_encode($e->getMessage()));
    }
});

//INSERT json Route
$app->post('/insertOneJson', function(Request $request, Response $response)
{
    $title = $request->getParam('title');
    $released = $request->getParam('released');
    $cast = $request->getParam('cast');
    try {
        $collection = (new MongoDB\Client)->test->tvShows;
        $insertOneResult = $collection->insertOne(
            [
                "title" => $title,
                "released" => $released,
                "cast" => $cast
            ]);
            printf("Inserted %d document(s)\n",$insertOneResult->getInsertedCount());
    }
    catch (MongoDB\Exception $e) {
        printf(json_encode($e->getMessage()));
    }
});

//READ json Route
$app->get('/getTvShowsJson', function(Request $request, Response $response){
    try{
        $collection = (new MongoDB\Client)->test->tvShows;
        $findAllResult = $collection->find();
        foreach($findAllResult as $result)
        {
            printf(json_encode($result) . '<br>');
        }
    }
    catch(MongoDB\Exception $e){
        printf(json_encode($e->getMessage()));
    }
});