# JSON import

The backend API can be used to update the project information per project. It is possible to obtain a clientID, client
secret, username and password to request an API token

```
POST https://api.eurekaclusters.eu/oauth
Accept: application/json
Content-Type: application/json

{
  "grant_type": "password",
  "redirect_uri": "https://itea4.org/oauth",
  "username": "johan.van.der.heide@itea4.org",
  "password": "redacted",
  "client_id": "redacted",
  "client_secret": "redacted"
}
```

The service will respond with an access token which can be used to send the project.json file (see full example below)

```
POST https://api.eurekaclusters.eu/api/update/project
Accept: application/json
Content-Type: application/json

{
    "internalIdentifier": "itea-120",
    "number": "12234",
    .
}
```

Below an example of a project can be found

```json
{
  "internalIdentifier": "itea-10196",
  "number": "13001",
  "name": "Sample Project",
  "title": "Sample Project Title",
  "description": "Sample Description",
  "programme": "ITEA 3",
  "programmeCall": "ITEA 4 Call 2021",
  "technicalArea": "Smart communities",
  "primaryCluster": {
    "identifier": "itea",
    "name": "ITEA",
    "description": "ITEA Cluster"
  },
  "officialStartDate": "2021-02-01T00:00:00+01:00",
  "officialEndDate": "2023-09-30T00:00:00+02:00",
  "projectStatus": "Running",
  "projectLeader": {
    "id": 30809,
    "cluster": "itea",
    "fullName": "Dr. Ir. Johan van der Heide",
    "firstName": "Johan",
    "lastName": "van der Heide",
    "email": "johan.van.der.heide@itea4.org",
    "clusterPermissions": [
      "itea"
    ],
    "isFunder": false,
    "funderCountry": null,
    "address": {
      "address": "High Tech Campus 69-3",
      "city": "Eindhoven",
      "zipCode": "5656AG",
      "country": "NL"
    }
  },
  "labelDate": "2015-05-01T00:00:00+02:00",
  "cancelDate": "2022-03-17T00:00:00+01:00",
  "versions": {
    "po": {
      "version": 1,
      "type": "Project Outline",
      "submissionDate": "2012-05-28T00:00:00+02:00",
      "status": "PO Rejected",
      "totalEffort": 0,
      "totalCosts": 0,
      "countries": [],
      "partners": []
    },
    "fpp": {
      "version": 1,
      "type": "Full Project Proposal",
      "submissionDate": "2020-03-04T00:00:00+01:00",
      "status": "FPP Approved",
      "totalEffort": 0,
      "totalCosts": 0,
      "countries": {
        "NL": "Netherlands",
        "BE": "Belgium",
        "FR": "France"
      },
      "partners": [
        {
          "id": 11631,
          "partner": "ITEA Office",
          "country": "NL",
          "type": "Others",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 23135,
            "cluster": "itea",
            "fullName": "Frank Weirater",
            "firstName": "Frank",
            "lastName": "Weirater",
            "email": "frank.weirater@itea4.org",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Hendrik van Linburgplein 12",
              "city": "Eindhoven",
              "zipCode": "5611 PE",
              "country": "NL"
            }
          }
        },
        {
          "id": 21979,
          "partner": "Jield BV",
          "country": "BE",
          "type": "SME",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 17523,
            "cluster": "itea",
            "fullName": "Dr. Ir. Johan van der Heide",
            "firstName": "Johan",
            "lastName": "van der Heide",
            "email": "info@jield.nl",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Boekweitbeemd 12",
              "city": "Valkenswaard",
              "zipCode": "5551 HM",
              "country": "NL"
            }
          }
        },
        {
          "id": 11630,
          "partner": "Sample organisation",
          "country": "FR",
          "type": "Others",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 16368,
            "cluster": "itea",
            "fullName": "Lies van den Borne",
            "firstName": "Lies",
            "lastName": "van den Borne",
            "email": "lies@hotmail.com",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Jozefstraat, 51",
              "city": "Ramsel",
              "zipCode": "5541CB",
              "country": "NL"
            }
          }
        },
        {
          "id": 21982,
          "partner": "Sample organisation 3",
          "country": "BE",
          "type": "Unknown",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 23135,
            "cluster": "itea",
            "fullName": "Frank Weirater",
            "firstName": "Frank",
            "lastName": "Weirater",
            "email": "frank.weirater@itea4.org",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Hendrik van Linburgplein 12",
              "city": "Eindhoven",
              "zipCode": "5611 PE",
              "country": "NL"
            }
          }
        },
        {
          "id": 21984,
          "partner": "Sample organisation Test",
          "country": "NL",
          "type": "SME",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 4922,
            "cluster": "itea",
            "fullName": "Frank Yellowbach",
            "firstName": "Erik",
            "lastName": "Rodenbach",
            "email": "erik.rodenbach@itea4.org",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "HTC 69",
              "city": "EINDHOVEN",
              "zipCode": "5656 AG",
              "country": "NL"
            }
          }
        }
      ]
    },
    "latest": {
      "version": 1,
      "type": "Change Request",
      "submissionDate": "2016-10-12T00:00:00+02:00",
      "status": "CR Approved",
      "totalEffort": 0,
      "totalCosts": 0,
      "countries": {
        "NL": "Netherlands",
        "BE": "Belgium",
        "FR": "France"
      },
      "partners": [
        {
          "id": 11631,
          "partner": "ITEA Office",
          "country": "NL",
          "type": "Others",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 23135,
            "cluster": "itea",
            "fullName": "Frank Weirater",
            "firstName": "Frank",
            "lastName": "Weirater",
            "email": "frank.weirater@itea4.org",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Hendrik van Linburgplein 12",
              "city": "Eindhoven",
              "zipCode": "5611 PE",
              "country": "NL"
            }
          }
        },
        {
          "id": 21979,
          "partner": "Jield BV",
          "country": "BE",
          "type": "SME",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 17523,
            "cluster": "itea",
            "fullName": "Dr. Ir. Johan van der Heide",
            "firstName": "Johan",
            "lastName": "van der Heide",
            "email": "info@jield.nl",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Klaverbeemd 12",
              "city": "Valkenswaard",
              "zipCode": "5551 HM",
              "country": "NL"
            }
          }
        },
        {
          "id": 11630,
          "partner": "Sample organisation",
          "country": "FR",
          "type": "Others",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 16368,
            "cluster": "itea",
            "fullName": "Lies van den Borne",
            "firstName": "Lies",
            "lastName": "van den Borne",
            "email": "lies@hotmail.com",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Jozefstraat, 51",
              "city": "Ramsel",
              "zipCode": "5541CB",
              "country": "NL"
            }
          }
        },
        {
          "id": 21982,
          "partner": "Sample organisation 3",
          "country": "BE",
          "type": "Unknown",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 23135,
            "cluster": "itea",
            "fullName": "Frank Weirater",
            "firstName": "Frank",
            "lastName": "Weirater",
            "email": "frank.weirater@itea4.org",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "Hendrik van Linburgplein 12",
              "city": "Eindhoven",
              "zipCode": "5611 PE",
              "country": "NL"
            }
          }
        },
        {
          "id": 21984,
          "partner": "Sample organisation Test",
          "country": "NL",
          "type": "SME",
          "isActive": true,
          "isCoordinator": false,
          "isSelfFunded": false,
          "costsAndEffort": [],
          "technicalContact": {
            "id": 4922,
            "cluster": "itea",
            "fullName": "Frank Yellowbach",
            "firstName": "Erik",
            "lastName": "Rodenbach",
            "email": "erik.rodenbach@itea4.org",
            "clusterPermissions": [
              "itea"
            ],
            "isFunder": false,
            "funderCountry": null,
            "address": {
              "address": "HTC 69",
              "city": "EINDHOVEN",
              "zipCode": "5656 AG",
              "country": "NL"
            }
          }
        }
      ]
    }
  }
}
```