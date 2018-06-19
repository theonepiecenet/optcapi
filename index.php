<?php
require './vendor/autoload.php';

$app = new \Slim\App;
$container = $app->getContainer();
$app->get('/', function ($req, $res, $args=[]) {
    echo "theonepiece.net api server";
});

$app->get('/{lang}/character/{id}', function($request, $response) {
    $id = $request->getAttribute('id');
    $lang = $request->getAttribute('lang');
    $original_file = "res/en/character/".$id;
    $local_file = "res/".$lang."/character/".$id;
    $original_json = getFile($original_file);
    $original_arr = json_decode($original_json, true);

    if($lang != "en")
    {
        $local_json = getFile($local_file);
        $local_arr  = json_decode($local_json, true);
        $local_keys = array_keys($local_arr);
    
        for($i=0;$i<count($local_arr);$i++)
	{
            $original_arr[$local_keys[$i]] = $local_arr[$local_keys[$i]];
	}
    }
    $json = json_encode($original_arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $response->getBody()->write($json);
    return $response;
});

$app->run();

function getFile($path)
{
    if(file_exists($path))
    {
        $fp = fopen($path, "r");
        $json = fread($fp, filesize($path));
        fclose($fp);
    }
    return $json;
}
?>
