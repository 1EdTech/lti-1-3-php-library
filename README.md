# LTI 1.3 Advantage Demo Tool
This tool is a demonstration of LTI 1.3 Core and the additional services that make up LTI advantage. The code here is designed to provide an example use case of how you can utilize the LTI specifications.

## Setup
The example code here is written in PHP, and it also contains a docker compose file for easy setup if you have docker installed.

To get up and running simply run:
```
docker-compose up --build
```

There are now two example tools you can launch into on the port 9001:
```
Simple Example:
OIDC Login URL http://localhost:9001/example/login.php
LTI Launch URL http://localhost:9001/example/launch.php

Game Example:
OIDC Login URL http://localhost:9001/game_example/login.php
LTI Launch URL http://localhost:9001/game_example/game.php
```

You're now free to launch in and use the tool. On your first launch in, there will be no credentials saved, so you will be prompted to fill out the registration and deployment forms. After that when you launch in, the credentials will be verified and loaded into the tool.

## Maintenance
This code is created and maintained by Turnitin.
