1. Start docker-compose

    ```
    $ docker-compose up
    ```

2. Install composer dependencies:

```
$ composer install
```

3. Initialize and fill clickhouse database:

```
$ php src/init.php && php src/fill.php
```

4. Go to Grafana http://127.0.0.1:3000

5. Create new dashboard

6. Add a graph with name "GEO"

7. Create 3 metrics:

```
SELECT
    $timeSeries as t,
    count(geo) as RU
FROM $table
WHERE
    $timeFilter
    AND geo == 'RU'
GROUP BY t
ORDER BY t


SELECT
    $timeSeries as t,
    count(geo) as UA
FROM $table
WHERE
    $timeFilter
    AND geo == 'UA'
GROUP BY t


SELECT
    $timeSeries as t,
    count(geo) as Other
FROM $table
WHERE
    $timeFilter
    AND geo != 'UA'
    AND geo != 'RU'
GROUP BY t
ORDER BY t
```
