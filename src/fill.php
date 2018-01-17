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

$advertisers = ['Advert A', 'Advert B', 'Advert C', 'Advert D'];
$partners = ['Partner A', 'Partner B', 'Partner C'];
$domains = ['Domain A', 'Domain B', 'Domain C'];
$movies = ['Movie A', 'Movie B', 'Movie C', 'Movie D', 'Movie E'];

for ($i = 0; $i < $recordsCount; $i++) {
    $rndNum = $faker->numberBetween(0, 2);

    array_push($videoValues, [
        /* event_date = */  ceil(time() / $day2sec), // количество дней с начала Unix эпохи
        /* event_time = */  $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        /* ipv4 = */        $faker->ipv4,
        /* partner = */     $partners[$rndNum],
        /* domain = */      $domains[$rndNum],
        /* movie = */       $faker->randomElement($movies),
        /* browser_id = */  $faker->numberBetween(1, $maxBrowserId),
        /* os_id = */       $faker->numberBetween(1, $maxOsId),
        /* geo = */         $faker->countryCode,
    ]);

    array_push($advValues, [
        /* event_date = */  ceil(time() / $day2sec), // количество дней с начала Unix эпохи
        /* event_time = */  $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        /* ipv4 = */        $faker->ipv4,
        /* domain = */      $domains[$rndNum],
        /* advert = */      $faker->randomElement($advertisers),
        /* browser_id = */  $faker->numberBetween(1, $maxBrowserId),
        /* os_id = */       $faker->numberBetween(1, $maxOsId),
        /* geo = */         $faker->countryCode,
    ]);

    if ($i > 0 && $i % 1000 == 0) {
        $db->insert($tblPlayVideo,
            $videoValues,
            ['event_date', 'event_time', 'ipv4', 'partner', 'domain', 'movie', 'browser_id', 'os_id', 'geo']
        );

        $db->insert($tblPlayAdv,
            $advValues,
            ['event_date', 'event_time', 'ipv4', 'domain', 'advert', 'browser_id', 'os_id', 'geo']
        );

        $videoValues = [];
        $advValues = [];
    }
}
