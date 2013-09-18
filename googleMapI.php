set_time_limit(0);
$db=new cls_mysql(dbHost,dbUser,dbPassword,dbName,'UTF8');
function xml_to_array( $xml )
{
    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches))
    {
        $count = count($matches[0]);
        $arr = array();
        for($i = 0; $i < $count; $i++)
        {
            $key = $matches[1][$i];
            $val = xml_to_array( $matches[2][$i] );  // 递归
            if(array_key_exists($key, $arr))
            {
                if(is_array($arr[$key]))
                {
                    if(!array_key_exists(0,$arr[$key]))
                    {
                        $arr[$key] = array($arr[$key]);
                    }
                }else{
                    $arr[$key] = array($arr[$key]);
                }
                $arr[$key][] = $val;
            }else{
                $arr[$key] = $val;
            }
        }
        return $arr;
    }else{
        return $xml;
    }
}
function getJwInfo($address){
$url="http://maps.google.com/maps/api/geocode/xml?address=".urlencode($address)."&sensor=false";
$content=file_get_contents($url);
$array= xml_to_array($content);
return $array['GeocodeResponse']['result']['geometry']['location'];
}
//$array=getJwInfo("復興鄉");
//print_r($array);
$array1=$db->getAll("select * from ecs_city order by id asc");
foreach($array1 as $row1)
{
$array=$db->getAll("select * from ecs_city_district where city_id='$row1[id]' order by id asc ");
foreach($array as $row)
{
	
	//sql 
	$city=$row1[title].$row['title'];
	$array=getJwInfo($city);
	$db->query("update ecs_city_district set lat='$array[lat]',lng='$array[lng]' where id='$row[id]'");
	echo"update ecs_city_district set lat='$array[lat]',lng='$array[lng]' where id='$row[id]'"."<br/>";
}
}