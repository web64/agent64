<?php

$track_servers = ['161sys4']; // https://www.soyoustart.com/ie/offers/161sys4.xml
//$track_servers = ['160fs1']; // test

$data = json_decode(
    file_get_contents("https://ws.ovh.com/dedicated/r2/ws.dispatcher/getAvailability2"),
   1
);



//print_r($data);

if ( !empty($data['answer']) && !empty($data['answer']['availability']) )
{
    foreach($data['answer']['availability'] as $item)
    {
        if ( array_search($item['reference'] , $track_servers) !== false )
        {
            foreach( $item['metaZones'] as $zone )
            {
                //echo "<h1>MetaZone</h1>";
                //echo "<pre>". print_r($zone, true) . "</pre>";
                
                if ( $zone['zone'] == 'centralEurope' || $zone['zone'] == 'fr' )
                {
                    if ( $zone['availability'] != 'unknown' && $zone['availability'] != 'unavailable' )
                    {
                        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                        echo "<h3>GREAT! zone: {$zone['zone']} - availability: {$zone['availability']}</h3>";
                        echo "Server Found!! [{$item['reference']}]";
                        exit;
                    }
                }
            }
        }
    }
}



echo "OK! - No new servers available!\n";