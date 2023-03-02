# PropellerAds Cache Service

## Getting Started

1. Run `sudo git pull https://github.com/karamyan/propellerads_cache_service.git` to pull project.
2. Run `cd propellerads_cache_service`
3. Run `sudo docker-compose -f docker-compose.yml up -d` to start the Docker containers.
4. Run `sudo cp .env.example .env` copy .env.example to .env
5. Run `sudo docker exec -it propellerads_php composer install` to install packages.
6. Run `sudo docker exec -it propellerads_php composer dump-autoload`.
7. Run `sudo docker-compose -f docker-compose.yml down` to stop the Docker containers.


## Application Endpoints

1.  [GET] http://localhost:7777
           `/api/stats?datamarts[]=stats_dep_a&date_time_from=2023-02-01%2014:00&date_time_to=2023-03-07%2014:00`

2.  [POST] http://localhost:7777
           `/api/stats/calculate`
   
           `{
             "datamarts" : "stats_dep_c",
             "date_time": "2023-03-04 14:00"
             }
           `
