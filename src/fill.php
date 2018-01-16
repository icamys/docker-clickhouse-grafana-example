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
$db->database($dbName);

$day2sec = 86400;
$maxPartnerId = 1000;
$domainsPerPartner = 5;
$maxDomainId = $maxPartnerId * $domainsPerPartner;
$maxMovieId = 100000;
$maxAdvertId = 100000;
$maxBrowserId = 10;
$maxOsId = 10;
//$recordsCount = 400 * 1000 * 1000;
$recordsCount = 2 * 1000 * 1000;

$faker = Faker\Factory::create();

$videoValues = [];
$advValues = [];
for ($i = 0; $i < $recordsCount; $i++) {
    array_push($videoValues, [
        /* event_date = */  ceil(time() / $day2sec), // количество дней с начала Unix эпохи
        /* event_time = */  $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        /* ipv4 = */        $faker->ipv4,
        /* partner_id = */  $faker->numberBetween(1, $maxPartnerId),
        /* domain_id = */   $faker->numberBetween(1, $maxDomainId),
        /* movie_id = */    $faker->numberBetween(1, $maxMovieId),
        /* browser_id = */  $faker->numberBetween(1, $maxBrowserId),
        /* os_id = */       $faker->numberBetween(1, $maxOsId),
        /* geo = */         $faker->countryCode,
    ]);

    array_push($advValues, [
        /* event_date = */  ceil(time() / $day2sec), // количество дней с начала Unix эпохи
        /* event_time = */  $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        /* ipv4 = */        $faker->ipv4,
        /* domain_id = */   $faker->numberBetween(1, $maxDomainId),
        /* advert_id = */    $faker->numberBetween(1, $maxAdvertId),
        /* browser_id = */  $faker->numberBetween(1, $maxBrowserId),
        /* os_id = */       $faker->numberBetween(1, $maxOsId),
        /* geo = */         $faker->countryCode,
    ]);

    if ($i > 0 && $i % 1000 == 0) {
        $db->insert($tblPlayVideo,
            $videoValues,
            ['event_date', 'event_time', 'ipv4', 'partner_id', 'domain_id', 'movie_id', 'browser_id', 'os_id', 'geo']
        );

        $db->insert($tblPlayAdv,
            $videoValues,
            ['event_date', 'event_time', 'ipv4', 'domain_id', 'advert_id', 'browser_id', 'os_id', 'geo']
        );

        $videoValues = [];
        $advValues = [];
    }
}
