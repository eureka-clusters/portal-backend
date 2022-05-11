# Data model

The following data model is used to store all project information.

## Overview tables

First an overview of all tables is given. In the next section for each table all columns will be shown

### Basic tables

The following tables are needed to create a project and a project version

- [User](#user)
- [Cluster](#cluster)
- [Country](#country)
- [Project status](#project-status)
- [Project version type](#project-version-type)
- [Project version status](#project-version-status)
- [Organisation type](#organisation-type)

### Project tables

The following tables are used to hold all organisations and projects data

- [Funder](#funder)
- [Funder cluster](#funder-cluster)
- [Organisation](#organisation)
- [Project](#project)

### Project verions tables

The following hold all information per project

- [Project version](#project-version)
- [Project partner](#project-partner)
- [Project version costs and effort](#project-version-costs-and-effort)

## Details per table

Find below an overview of all tables and the content per table.

### User

In this table an overview of all users in the backend is stored. A user is created upon authetication via the clusters webistes (
ITEA, Celtic-Next and Xecs)

| Column      | Type     | nullable | Description                                                               |
|-------------|----------|----------|---------------------------------------------------------------------------|
| id          | int      | no       |                                                                           |
| password    | string   | yes      | only store hashed password for accounts which are used for data importing |
| firstName   | string   | no       |                                                                           |
| lastName    | string   | yes      |                                                                           |
| email       | string   | yes      |                                                                           |
| dateCreated | dateTime | no       |                                                                           |
| dateUpdated | dateTime | yes      |                                                                           |
| dateEnd     | dateTime | yes      | If not null,the date when the user has been deactivated                   |

### Cluster

In this table an overview of all clusters is given

| Column      | Type     | nullable | Description                                                           |
|-------------|----------|----------|-----------------------------------------------------------------------|
| id          | int      | no       |                                                                       |
| name        | string   | no       | Name of the cluster                                                   |
| identifier  | string   | no       | Unique identifier of the cluster, has to be the same in all databases |
| description | string   | yes      |                                                                       |
| dateCreated | dateTime | no       |                                                                       |
| dateUpdated | dateTime | yes      |                                                                       |

### Country

In this table an overview of all countries is given

| Column      | Type     | nullable | Description                                                                              |
|-------------|----------|----------|------------------------------------------------------------------------------------------|
| id          | int      | no       |                                                                                          |
| cd          | string   | no       | [ISO 3166-1 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2) country code      |
| country     | string   | no       | Name of the country                                                                      |
| docRef      | string   | no       | Unique key of the country used for URL formation (eg: united-kingdom for United Kingdom) |
| iso3        | string   | yes      | [ISO 3166-1 alpha-3](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3)                   |
| numbode     | integer  | yes      | [ISO 3166-1 numeric](https://en.wikipedia.org/wiki/ISO_3166-1_numeric)                   |

### Project status

In this table an overview of all possible project statuses is given. These statuses are harmonized statuses of all
EUREKA clusters

| Column | Type   | nullable | Description |
|--------|--------|----------|-------------|
| id     | int    | no       |             |
| status | string | no       |             |

Current project statuses are: Completed, Labelled, Running, Stopped

### Project version type

In this table an overview of all project version types is given. These types are harmonized types of all EUREKA
clusters

| Column      | Type   | nullable | Description |
|-------------|--------|----------|-------------|
| id          | int    | no       |             |
| type        | string | no       |             |
| description | string | no       |             |

Current project version statuses are: Project Outline (po), Full project proposal (fpp), Latest version (latest).
The latest version can be any type, including a change request

### Project version status

In this table an overview of all project version statuses is given. These statuses are harmonized statuses
of all EUREKA clusters

| Column | Type   | nullable | Description |
|--------|--------|----------|-------------|
| id     | int    | no       |             |
| status | string | no       |             |

Current project version statuses are: CR Approved, FPP Approved, Labelled, PO Approved, PO Rejected, Running, Stopped

### Organisation type

In this table an overview of all organisation types is given. These types are harmonized types
of all EUREKA clusters

| Column | Type   | nullable | Description |
|--------|--------|----------|-------------|
| id     | int    | no       |             |
| type   | string | no       |             |

Current project version statuses are: <empty>, Government, Industry, Large Industry, Others, Research, SME, University,
Unknown

### Funder

This table indicates if a user is a funder (Public Authority). Each record in the table corresponsds to a user and
country and indicates that the user is Public Authority in that country

| Column     | Type | nullable | Description                       |
|------------|------|----------|-----------------------------------|
| id         | int  | no       |                                   |
| user_id    | int  | no       | FK to `id` in [user](#user)       |
| country_id | int  | no       | FK to `id` in [country](#country) |

### Funder cluster

Not every user in the [funder](#funder) table is funder for all clusters, in this table the funder is connected to the
cluster to indicate for which cluster the funder is active (currently unused)

| Column     | Type | nullable | Description                       |
|------------|------|----------|-----------------------------------|
| funder_id  | int  | no       | FK to `id` in [funder](#funder)   |
| cluster_id | int  | no       | FK to `id` in [cluster](#cluster) |

### Organisation

In this table an overview of all organisations is given

| Column     | Type   | nullable | Description                                           |
|------------|--------|----------|-------------------------------------------------------|
| id         | int    | no       |                                                       |
| country_id | int    | no       | FK to `id` in [country](#country)                     |
| type_id    | int    | no       | FK to `id` in [Organisation type](#organisation-type) |
| name       | string | no       |                                                       |
| slug       | string | no       | Unique key per organiation, used for link creation    |

### Project

In this table an overview of all projects is given

| Column              | Type     | nullable | Description                                                         |
|---------------------|----------|----------|---------------------------------------------------------------------|
| id                  | int      | no       |                                                                     |
| status_id           | int      | no       | FK to `id` in [Project status](#project-status)                     |
| identifier          | string   | no       | Unique identifer per cluster (for example ITEA_124, or CELTIC_4942) |
| number              | string   | no       | Project number                                                      |
| name                | string   | no       | Project name                                                        |
| title               | string   | no       |                                                                     |
| description         | string   | no       |                                                                     |
| technicalArea       | string   | no       |                                                                     |
| programme           | string   | no       |                                                                     |
| programmeCall       | string   | no       |                                                                     |
| labelDate           | dateTime | yes      | Date on which the project received the label                        |
| cancelDate          | dateTime | yes      | If set, date on which the project was cancelled                     |
| officialStartDate   | dateTime | no       |                                                                     |
| officialEndDate     | dateTime | no       |                                                                     |
| projectLeader       | array    | no       | Array with project leader information (name, email)                 |
| primaryCluster_id   | int      | no       | FK to `id` in [Cluster(#cluster)                                    |
| secondaryCluster_id | int      | yes      | FK to `id` in [Cluster(#cluster)                                    |
| slug                | string   | no       | Unique key per project, used for link creation                      |

### Project version

For each version of a project in [Project](#project) an entry is created in this table. The partners
in [Project partner](#project-partner) are linked to the project version to be able to have different project partners
per version of the project

| Column         | Type     | nullable | Description                                                     |
|----------------|----------|----------|-----------------------------------------------------------------|
| id             | int      | no       |                                                                 |
| project_id     | int      | no       | FK to `id` in [Project](#project)                               |
| type_id        | int      | no       | FK to `id` in [Version type](#project-version-type)             |
| submissionDate | dateTime | no       | Date on which the project version has been submitted            |
| effort         | double   | no       | Total effort given in person months (pm)                        |
| costs          | double   | no       | Total costs given in euros                                      |
| countries      | array    | no       | Array of all countries active in the version                    |
| status_id      | string   | no       | FK to `id` in [Project version status](#project-version-status) |

### Project Partner

For each partner in [project version](#project-version) an entry is created in this table. For each partner in this
table multiple [costs and effort](#project-version-costs-and-effort) records per year can be found where the costs and
effort per version is saved

| Column              | Type   | nullable | Description                                             |
|---------------------|--------|----------|---------------------------------------------------------|
| id                  | int    | no       |                                                         |
| organisation_id     | int    | no       | FK to `id` in [Project status](#project-status)         |
| isActive            | bool   | no       | Boolean value to indicate if the partner is active      |
| isCoordinator       | bool   | no       | Boolean value to indicate if the partner is coordinator |
| isSelfFunded        | bool   | no       | Boolean value to indicate if the partner is self-funded |
| technicalContact    | array  | no       | firstName, lastName, email                              |
| project_id          | string | no       | FK to `id` in [Project](#project)                       |
| slug                | string | no       | Unique key for the project partner                      |
| organisationName    | string | no       | Name of organisation (internal reference)               |
| projectName         | string | no       | Name of project (internal reference)                    |
| latestVersionCosts  | float  | yes      | Total costs of partner in latest version                |
| latestVersionEffort | float  | yes      | Total effort of partner in latest version               |

### Project version costs and effort

In this table an overview **per year** of all costs (in Euro) and effort in PY can be found per partner and project
version

| Column     | Type   | nullable | Description                                       |
|------------|--------|----------|---------------------------------------------------|
| id         | int    | no       |                                                   |
| partner_id | int    | no       | FK to `id` in [Project partner](#project-partner) |
| version_id | int    | no       | FK to `id` in [Project version](#project-version) |
| year       | int    | no       | Year of which the costs and effort                |
| effort     | double | no       | Effort in PY                                      |
| costs      | double | no       | Costs in PY                                       |

