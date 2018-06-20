<?php
require './vendor/autoload.php';

$app = new \Slim\App;
$container = $app->getContainer();
$app->get('/', function ($req, $res, $args=[]) {
    echo "This is theonepiece.net api server";
    echo "please visit this repository, <a href='https://github.com/theonepiecenet/optcapi'>theonepiecenet/optcapi</a>";
});

$app->get('/{lang}/character/{id}', function($request, $response) {
    $id = $request->getAttribute('id');
    $lang = $request->getAttribute('lang');
    $original_file = "res/en/character/".$id;
    $local_file = "res/".$lang."/character/".$id;
    $original_json = getFile($original_file);
    if($original_json == 404)
    {
        header("HTTP/1.0 404 Not Found");
        exit();
    }
    else
    {
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
    }
    
    return $response;
});

$app->post('/{lang}/search', function($request, $response) {
    $result_arr = [];
    $result_name = [];
    $lang = $request->getAttribute('lang');
    $allPostPutVars = $request->getParsedBody();
    foreach($allPostPutVars as $key => $param){
        if($key == "name")
        {
            $value = $param;
            $fp = fopen("res/".$lang."/name.json", "r");
            $name = fread($fp, filesize("res/".$lang."/name.json"));
            $name = json_decode($name, true);
            fclose($fp);
            for($i=0; $i<count($name);$i++)
            {
                if(array_search(strtolower($value), array_map('strtolower', $name[$i]['aliases'])) > -1)
                {
                    array_push($result_name, $name[$i]['id']);
                }
            }
        }
        elseif($key == "captain" || $key == "sailor" || $key == "special" || $key == "limit")
        {
            $value = json_decode($param);
            $fp = fopen("res/".$lang."/tags.json", "r");
            $tag= fread($fp, filesize("res/".$lang."/tags.json"));
            $tag= json_decode($tag, true);
            fclose($fp);
	    for($i=0; $i<count($tag);$i++)
	    {
		if($tag[$i]['match'] == $key)
		{
                    for($j=0; $j<count($value);$j++)
                    {
                        if($tag[$i]['tag'] == $value[$j])
                        {
                            if(count($result_arr) > 0)
		                $result_arr = array_intersect($result_arr,$tag[$i]['target']);
                            else
                                $result_arr = $tag[$i]['target'];
                        }
                    }
		}
	    }
        }
    }
    if(count($result_arr) > 0)
        $result_arr = array_values($result_arr);

    if(count($result_name) > 0 && count($result_arr) > 0) {
        $result_arr = array_values(array_intersect($result_arr, $result_name));
    }
    elseif(count($result_name) > 0 && count($result_arr) == 0)
        $result_arr = $result_name;

    $response->getBody()->write(json_encode($result_arr));
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
    else
    {
        $json = 404;
    }
    return $json;
}
function objectToArray ($object) {
    if(!is_object($object) && !is_array($object))
        return $object;

    return array_map('objectToArray', (array) $object);
}
?>
