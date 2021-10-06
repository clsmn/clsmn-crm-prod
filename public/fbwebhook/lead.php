<?php
include "config.php";
include "db.php";


$db = new db($HOST, $USER, $PASS, $DBNAME);
//$db->query('show tables');
//echo $db->numRows();
// $request = $_REQUEST;
// if ($request['hub_verify_token'] === 'clsmn123456') {
//     echo $request['hub_challenge'];
// }
$entityBody = file_get_contents('php://input');
file_put_contents('/var/www/html/crm/public/fbwebhook/leaddata/lead.json', $entityBody);
$jsondata = json_decode(trim($entityBody,'"'), TRUE);
if ($jsondata['object'] === "page" && count($jsondata['entry'])) {    
    foreach ($jsondata['entry'] as $entry) {
        if (count($entry['changes'])) {
            foreach ($entry['changes'] as $changes) {
                if ($changes['field'] === 'leadgen') {
                    $value = $changes['value'];
                    $sql = "INSERT INTO `leads`(`ad_id`, `form_id`, `leadgen_id`, `created_time`, `page_id`, `adgroup_id`) 
                    VALUES(".$value['ad_id'].", ".$value['form_id'].", ".$value['leadgen_id'].", '".date('Y-m-d H:i:s',$value['created_time'])."', ".$value['page_id'].", ".$value['adgroup_id'].")";
                    $db->query($sql);
                    getAdsetDetail($changes['value']['leadgen_id'], $FB_TOKEN);
                    getDataByLeadGenId($changes['value']['leadgen_id'], $FB_TOKEN);
                }
            }
        }
    }
}
function getAdsetDetail($adsetid, $token){
    $url = "https://graph.facebook.com/v10.0/$adsetid?access_token=$token";
    $data = getContent($url);
}
function getDataByLeadGenId($leadgenid, $token){
    $url = "https://graph.facebook.com/v10.0/$leadgenid?access_token=$token";
    $data = getContent($url);
    file_put_contents('/var/www/html/crm/public/fbwebhook/leaddata/error.json', $data); 
}

function getContent($url){
    $curlSession = curl_init();
    curl_setopt($curlSession, CURLOPT_URL, $url);
    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

    $jsonData = curl_exec($curlSession);
    curl_close($curlSession);
    return $jsonData;
}