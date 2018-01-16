<?php

include realpath(__DIR__.'/../vendor/autoload.php');

$config = [
    'host'     => '172.22.0.2', // clickhouse server ip
    'port'     => '8123',
    'username' => 'default',
    'password' => '',
];


$dbName = 'video_service';
$tblPlayVideo = 'play_video';
$tblPlayAdv = 'play_adv';

$db = new ClickHouseDB\Client($config);
$db->database('default');
$db->setTimeout(1.5);      // 1500 ms
$db->setConnectTimeOut(5); // 5 seconds

$db->write("DROP DATABASE IF EXISTS {$dbName}");

$db->write("CREATE DATABASE IF NOT EXISTS {$dbName}");
$db->database($dbName);

$db->write(
    "
    CREATE TABLE IF NOT EXISTS {$dbName}.{$tblPlayVideo} (
        event_date  Date DEFAULT toDate(event_time),
        event_time  DateTime,
        ipv4        FixedString(15),
        partner_id  UInt32,
        domain_id   UInt32,
        movie_id    UInt32,
        browser_id  UInt8,
        os_id       UInt8,
        geo         FixedString(2)
    )
    ENGINE = MergeTree(event_date, (ipv4, partner_id, domain_id, movie_id, browser_id, os_id, geo, event_time, event_date), 8192)
"
);

// todo: Uncomment on production
//$db->write(
//    "
//    CREATE TABLE {$dbName}.{$tblPlayVideo}_buffer AS {$dbName}.{$tblPlayVideo}
//    ENGINE = Buffer({$dbName}, {$tblPlayVideo}, 16, 10, 100, 10000, 1000000, 10000000, 100000000)
//"
//);

$db->write(
    "
    CREATE TABLE IF NOT EXISTS {$dbName}.{$tblPlayAdv} (
        event_date  Date DEFAULT toDate(event_time),
        event_time  DateTime,
        ipv4        FixedString(15),
        domain_id   UInt32,
        advert_id   UInt32,
        browser_id  UInt8,
        os_id       UInt8,
        geo         FixedString(2)
    )
    ENGINE = MergeTree(event_date, (ipv4, domain_id, advert_id, browser_id, os_id, geo, event_time, event_date), 8192)
"
);

// todo: Uncomment on production
//$db->write(
//    "
//    CREATE TABLE {$dbName}.{$tblPlayAdv}_buffer AS {$dbName}.{$tblPlayAdv}
//    ENGINE = Buffer({$dbName}, {$tblPlayAdv}, 16, 10, 100, 10000, 1000000, 10000000, 100000000)
//"
//);

print_r($db->showTables());
