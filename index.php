<?php
require './vendor/autoload.php';
$app = new \Slim\App;
$container = $app->getContainer();
$app->get('/', function ($req, $res, $args=[]) {
    echo "This is theonepiece.net api server";
    echo "please visit this repository, <a href='https://github.com/theonepiecenet/optcapi'>theonepiecenet/optcapi</a>";
});
$app->get('/{lang}/tags', function($request, $response) {
    $lang = $request->getAttribute('lang');
    $tag = json_decode(getFile("res/en/tags.json"), true);
    $json = [];
    for($i=0; $i<count($tag);$i++)
    {
        $json[$i] = array("tag" => $tag[$i]['tag'], "match" => $tag[$i]['match'], "tag_local" => $tag[$i]['tag']);
    }
    if($lang != "en")
    {
        $local_json = json_decode(getFile("res/".$lang."/tags.json"), true);
        for($i=0;$i<count($local_json);$i++)
        {
            for($j=0; $j<count($tag);$j++)
            {
              if($json[$j]['tag_local'] == $local_json[$i]['tag'] && $local_json[$i]['tag_local'])
                  $json[$j]['tag_local'] = $local_json[$i]['tag_local'];
            }
        }
    }
    $json = json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $response->getBody()->write($json);
    return $response;
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
        $tag = json_decode(getFile("res/en/tags.json"), true);
        $tag_local = json_decode(getFile("res/".$lang."/tags.json"), true);
        $j = 0;
        for($i=0; $i<count($tag);$i++)
        {
            if(array_search($id, $tag[$i]['target']) > -1)
            {
                for($k=0; $k<count($tag_local);$k++)
                {
                    if($tag[$i]['tag'] == $tag_local[$k]['tag'])
                    {
                        $original_arr['tags'][$j]['tag'] = $tag_local[$k]['tag'];
                        $original_arr['tags'][$j]['match'] = $tag_local[$k]['match'];
                        $j++;
                    }
                }
            }
        }
        $json = json_encode($original_arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $response->getBody()->write($json);
    }
    
    return $response;
});
$app->post('/{lang}/search', function($request, $response) {
    log_file(json_encode($_POST, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    $result_arr = [];
    $result_name = [];
    $result_key = [];
    $lang = $request->getAttribute('lang');
    $allPostPutVars = $request->getParsedBody();
    foreach($allPostPutVars as $key => $param){
        if($key == "name")
        {
            $value = $param;
            $name = json_decode(getFile("res/".$lang."/name.json"), true);
            for($i=0; $i<count($name);$i++)
            {
                for($j=0; $j<count($name[$i]['aliases']);$j++)
                {
                    if(stripos($name[$i]['aliases'][$j], $value) > -1)
                    {
                        array_push($result_name, $name[$i]['id']);
                        break;
                    }
                }
            }
        }
        elseif($key == "type" || $key == "class")
        {
            $value = json_decode($param, true);
            $name = json_decode(getFile("res/en/name.json"), true);
            $result_tmp = [];
            for($i=0; $i<count($value);$i++)
            {
                for($j=0; $j<count($name);$j++)
                {
                    if(array_search($value[$i], $name[$j][$key]) > -1)
                    {
                        array_push($result_key,$name[$j]['id']);
                    }
                }
            }
            if(count($result_tmp) > 0)
                $result_key = array_values(array_intersect($result_tmp, $result_key));
        }
        elseif($key == "captain" || $key == "sailor" || $key == "special" || $key == "limit")
        {
            $value = json_decode($param);
            $tag = json_decode(getFile("res/en/tags.json"), true);
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
    if(count($result_key) > 0 && count($result_arr) > 0){
        $result_arr = array_values(array_intersect($result_arr, $result_key));
    }
    elseif(count($result_key) > 0 && count($result_arr) == 0) {
        $result_arr = $result_key;
    }
    if(count($result_name) > 0 && count($result_arr) > 0) {
        $result_arr = array_values(array_intersect($result_arr, $result_name));
    }
    elseif(count($result_name) > 0 && count($result_arr) == 0) {
        $result_arr = $result_name;
    }
    sort($result_arr);
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
?>
