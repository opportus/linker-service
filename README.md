# Linker Service

[![License](https://poser.pugx.org/opportus/linker-service/license)](https://packagist.org/packages/opportus/linker-service)
[![Latest Stable Version](https://poser.pugx.org/opportus/linker-service/v/stable)](https://packagist.org/packages/opportus/linker-service)
[![Latest Unstable Version](https://poser.pugx.org/opportus/linker-service/v/unstable)](https://packagist.org/packages/opportus/linker-service)
[![Build](https://github.com/opportus/linker-service/workflows/Build/badge.svg)](https://github.com/opportus/linker-service/actions?query=workflow%3ABuild)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/d3f5178323844f59a6ef5647cb11d9d7)](https://www.codacy.com/manual/opportus/linker-service/dashboard?utm_source=github.com&utm_medium=referral&utm_content=opportus/linker-service&utm_campaign=Badge_Coverage)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d3f5178323844f59a6ef5647cb11d9d7)](https://www.codacy.com/manual/opportus/linker-service?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=opportus/linker-service&amp;utm_campaign=Badge_Grade)

Public web service linking nodes from input collection and parameters.

## Todo for v1.0 release

- [ ] Implement TLS support
- [ ] Implement non existing resource request handling
- [ ] Implement not supported HTTP method handling
- [ ] Implement logging
- [ ] Implement illogical list handling
- [ ] Implement 100% test coverage
- [ ] Optimize link autodiscovery algorithm
- [ ] Optimize linking algorithm

## Setup for dev environment

Requires:

- GNU/Linux OS
- Git
- Composer
- Docker
- Free 8080 ports on dev environment

```shell
git clone git@github.com:opportus/linker-service.git
cd linker-service
composer install --ignore-platform-reqs

sudo docker-compose --file docker/dev/docker-compose.yaml up
```

## Overview

The single operation endpoint accepts 2 query parameters as input, both optional:

-  `link` with pattern `/^([A-Za-z0-9\_\-]*):*([A-Za-z0-9\_\-]*)$/` (`previous_node_attribute:next_node_attribute`).
   If no link is provided, the service tries to discover a link among the nodes in the list.
-  `list` containing nodes to link. If no list is provided, the service returns an empty
   list.

If the `previous_node_attribute` value matches the `next_node_attribute` value, then the next node is
appended to the linked list right after the previous node...

Input list:

```json
[
  {
    "departure": "cityB",
    "arrival": "cityC"
  },
  {
    "departure": "cityA",
    "arrival": "cityB"
  },
  {
    "departure": "cityD",
    "arrival": "cityE"
  },
  {
    "departure": "cityE",
    "arrival": "cityF"
  },
  {
    "departure": "cityC",
    "arrival": "cityD"
  }
]
```

Web service API call:

```shell
curl -X GET -G \
'http://localhost:8080' \
-d "link=arrival:departure" \
-data-urlencode "list=$(cat list.json)"
```

Beautified linked list output:

```json
[
  {
    "departure": "cityA",
    "arrival": "cityB"
  },
  {
    "departure": "cityB",
    "arrival": "cityC"
  },
  {
    "departure": "cityC",
    "arrival": "cityD"
  },
  {
    "departure": "cityD",
    "arrival": "cityE"
  },
  {
    "departure": "cityE",
    "arrival": "cityF"
  }
]
```
