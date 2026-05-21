# Importing project data

Clusters can push project data into the central repository through the secured API. The import endpoint is designed for
backend-to-backend integration and accepts a JSON file upload for a single project.

## Authentication

Request an access token first by using the OAuth endpoint and the credentials provided for your cluster.

```bash
curl --request POST "https://api.eurekaclusters.eu/oauth" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data '{
    "grant_type": "password",
    "redirect_uri": "https://your-cluster-domain/oauth",
    "username": "your-api-user@example.org",
    "password": "redacted",
    "client_id": "redacted",
    "client_secret": "redacted"
  }'
```

The response contains a JWT access token. Use that token as a Bearer token for all API requests.

## Uploading a project

Send the project payload as a `multipart/form-data` request to `POST /api/update/project`. The request body contains one
required field named `file`.

```bash
curl --request POST "https://api.eurekaclusters.eu/api/update/project" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer <access_token>" \
  --form "file=@project.json;type=application/json"
```

## Import behaviour

The import is keyed by `internalIdentifier`. If the project already exists, the backend updates the main project record
and replaces the stored **versions**, **partners**, and **areas** with the content from the uploaded file. Treat each
upload as the full current state for that project instead of a partial patch.

## Current JSON structure

The current payload structure groups the project into a top-level record plus nested cluster, area, version, partner,
and yearly budget data.

| Section                                          | Purpose                                        | Main fields                                                                                                                                           |
|--------------------------------------------------|------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------|
| Top-level project fields                         | Project identity and lifecycle data            | `internalIdentifier`, `number`, `name`, `title`, `description`, `programme`, `programmeCall`, `projectStatus`, dates, `technicalArea`, `isSuccessful` |
| `primaryCluster` / `secondaryCluster`            | Cluster ownership                              | `identifier`, `name`, `description`                                                                                                                   |
| `projectLeader`                                  | Main contact information stored on the project | `id`, `cluster`, `fullName`, `firstName`, `lastName`, `email`, funder flags                                                                           |
| `areas[]`                                        | Project classification                         | `code`, `label`, `type`                                                                                                                               |
| `versions.po`, `versions.fpp`, `versions.latest` | Version snapshots used by the portal           | `id`, `type`, `submissionDate`, `reviewDate`, `status`, `totalEffort`, `totalCosts`, `countries`, `partners`                                          |
| `partners[]`                                     | Consortium members within a version            | `partner`, `country`, `type`, VAT/registration data, coordinator flags, `technicalContact`, `costsAndEffort`                                          |
| `costsAndEffort`                                 | Year-by-year effort and cost breakdown         | `<year>.costs`, `<year>.effort`                                                                                                                       |

## Version model

The `versions` object uses three named entries:

- `po`: Project Outline data
- `fpp`: Full Project Proposal data
- `latest`: the latest version that should be exposed by the portal

Each version contains its own partner list and yearly `costsAndEffort` values. The backend recalculates stored totals
from the partner-level yearly data during import.

## Example payload

The full sample below reflects the current structure expected by the import endpoint.

<resource src="sample-file.json"></resource>
