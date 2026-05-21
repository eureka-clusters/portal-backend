# Swagger / OpenAPI documentation

The backend API is documented through the generated OpenAPI file in `public/swagger/swagger.json`.

## What it contains

The Swagger file describes the secured REST API that is used by the portal and connected systems. It includes endpoints
for:

- project, partner, and organisation listings
- project, partner, and organisation detail views
- search and statistics endpoints
- the project import endpoint `POST /api/update/project`
- the current user endpoint `GET /api/me`

The specification declares HTTP Bearer authentication with JWT tokens.

## How to use it

You can use the generated `swagger.json` file in tools such as Swagger Editor, Postman, or any OpenAPI-compatible client
generator. You can also use the [live API documentation](https://api.eurekaclusters.eu/swagger/).

Repository source:

- [public/swagger/swagger.json](https://github.com/eureka-clusters/portal-backend/blob/main/public/swagger/swagger.json)
- [API documentation](API-Reference.topic)
- [OpenAPI specification](https://api.eurekaclusters.eu/swagger/swagger.json)

## Main endpoints in the current specification

| Method | Endpoint                                  | Purpose                                  |
|--------|-------------------------------------------|------------------------------------------|
| `GET`  | `/api/list/project`                       | List projects                            |
| `GET`  | `/api/view/project/{slug}`                | Retrieve project details                 |
| `GET`  | `/api/list/partner`                       | List project partners                    |
| `GET`  | `/api/view/partner/{slug}`                | Retrieve partner details                 |
| `GET`  | `/api/list/organisation`                  | List organisations                       |
| `GET`  | `/api/view/organisation/{slug}`           | Retrieve organisation details            |
| `GET`  | `/api/search/result`                      | Execute search queries                   |
| `GET`  | `/api/statistics/facets/project/{filter}` | Retrieve project facets                  |
| `GET`  | `/api/statistics/facets/partner/{filter}` | Retrieve partner facets                  |
| `POST` | `/api/update/project`                     | Upload project data from cluster systems |
| `GET`  | `/api/me`                                 | Retrieve the authenticated user          |

## Regenerating the specification

When the API changes, regenerate the OpenAPI document with:

```bash
./vendor/bin/openapi module -f json -o public/swagger/swagger.json
```
