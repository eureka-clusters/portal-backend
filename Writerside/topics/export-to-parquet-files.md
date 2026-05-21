# Export to data lake

The backend repository can be used as a source for downstream analytics and data lake ingestion. The dataset documented
below is available for export in **Parquet** and can also be delivered in **CSV** form where required by downstream
consumers.

Each heading below represents one exported dataset. The tables document the logical structure and columns that are made
available from the central cluster repository.

## cluster_project

This export contains all projects and their main metadata. PrimaryClusterId and SecondaryClusterId link to
cluster_cluster, StatusId links to the project status export, and ProjectLeader is exported as a JSON string with the
project leader details.

| Column                    | Type    | Nullable | Description                                                              |
|---------------------------|---------|----------|--------------------------------------------------------------------------|
| Id                        | integer |          | The unique identifier of the project                                     |
| Identifier                | string  |          | The unique identifier string of the project                              |
| Slug                      | string  |          | The URL-friendly identifier generated from the project name              |
| DateCreated               | date    |          | The date when the project record was created                             |
| DateUpdated               | date    | Yes      | The date when the project record was last updated                        |
| Number                    | string  |          | The project reference number                                             |
| Name                      | string  |          | The name of the project                                                  |
| Title                     | string  |          | The title of the project                                                 |
| Description               | string  | Yes      | The detailed description of the project                                  |
| TechnicalArea             | string  | Yes      | The technical area assigned to the project                               |
| Programme                 | string  |          | The programme under which the project is submitted                       |
| ProgrammeCall             | string  |          | The programme call associated with the project                           |
| ProgrammeCallPoOpenDate   | date    | Yes      | The opening date of the project outline call                             |
| ProgrammeCallPoCloseDate  | date    | Yes      | The closing date of the project outline call                             |
| ProgrammeCallFppOpenDate  | date    |          | The opening date of the full project proposal call                       |
| ProgrammeCallFppCloseDate | date    |          | The closing date of the full project proposal call                       |
| PrimaryClusterId          | integer |          | The id of the primary cluster, which links to cluster_cluster            |
| SecondaryClusterId        | integer | Yes      | The id of the secondary cluster, which links to cluster_cluster          |
| LabelDate                 | date    | Yes      | The date when the project received its label                             |
| CancelDate                | date    | Yes      | The date when the project was cancelled                                  |
| OfficialStartDate         | date    | Yes      | The official start date of the project                                   |
| OfficialEndDate           | date    | Yes      | The official end date of the project                                     |
| isSuccessful              | boolean | Yes      | Whether the project is marked as successful                              |
| StatusId                  | integer |          | The id of the project status, which links to the cluster_project_partner |
| ProjectLeader             | string  | Yes      | The project leader details encoded as JSON                               |
| ProjectOutlineCosts       | float   | Yes      | The total project outline costs                                          |
| ProjectOutlineEffort      | float   | Yes      | The total project outline effort                                         |
| FullProjectProposalCosts  | float   |          | The total costs in the full project proposal                             |
| FullProjectProposalEffort | float   |          | The total effort in the full project proposal                            |
| LatestVersionCosts        | float   |          | The total costs in the latest project version                            |
| LatestVersionEffort       | float   |          | The total effort in the latest project version                           |

## cluster_cluster

This export contains all clusters. Use the Id column to resolve cluster references from other exports, such as the
PrimaryClusterId and SecondaryClusterId columns in cluster_project.

| Column      | Type    | Nullable | Description                                                |
|-------------|---------|----------|------------------------------------------------------------|
| Id          | integer |          | The unique identifier of the cluster                       |
| Name        | string  |          | The name of the cluster                                    |
| Identifier  | string  |          | The internal identifier of the cluster                     |
| Description | string  | Yes      | The description of the cluster                             |
| DateCreated | string  |          | The date and time when the cluster record was created      |
| DateUpdated | string  | Yes      | The date and time when the cluster record was last updated |

## cluster_cluster_group

| Column      | Type    | Nullable | Description |
|-------------|---------|----------|-------------|
| Id          | integer |          |             |
| Name        | string  |          |             |
| Description | string  | Yes      |             |
| DateCreated | string  |          |             |
| DateUpdated | string  | Yes      |             |

## cluster_project_status

This export contains the available project statuses. Use the Id column to resolve StatusId references from
ProjectColumns.

| Column | Type    | Nullable | Description                                 |
|--------|---------|----------|---------------------------------------------|
| Id     | integer |          | The unique identifier of the project status |
| Status | string  |          | The label of the project status             |

## cluster_project_partner

This export contains the partner records for projects. OrganisationId links to OrganisationColumns, ProjectId links to
ProjectColumns, and TechnicalContact is exported as a JSON string.

| Column                    | Type    | Nullable | Description                                                           |
|---------------------------|---------|----------|-----------------------------------------------------------------------|
| Id                        | integer |          | The unique identifier of the project partner record                   |
| OrganisationId            | integer |          | The id of the linked organisation, which links to OrganisationColumns |
| ProjectId                 | integer |          | The id of the linked project, which links to ProjectColumns           |
| Slug                      | string  |          | The URL-friendly identifier generated for the partner record          |
| OrganisationName          | string  |          | The name of the linked organisation                                   |
| ProjectName               | string  |          | The name of the linked project                                        |
| IsActive                  | boolean | Yes      | Whether the partner record is active                                  |
| IsCoordinator             | boolean | Yes      | Whether this partner is the coordinator for the project               |
| IsSelfFunded              | boolean | Yes      | Whether this partner is marked as self-funded                         |
| TechnicalContact          | string  | Yes      | The technical contact details encoded as JSON                         |
| ProjectOutlineCosts       | float   | Yes      | The partner costs in the project outline phase                        |
| ProjectOutlineEffort      | float   | Yes      | The partner effort in the project outline phase                       |
| FullProjectProposalCosts  | float   | Yes      | The partner costs in the full project proposal phase                  |
| FullProjectProposalEffort | float   | Yes      | The partner effort in the full project proposal phase                 |
| LatestVersionCosts        | float   | Yes      | The partner costs in the latest project version                       |
| LatestVersionEffort       | float   | Yes      | The partner effort in the latest project version                      |

## cluster_organisation

This export contains all organisations. CountryId links to cluster_country and TypeId links to the organisation type
export.

| Column                       | Type    | Nullable | Description                                                                          |
|------------------------------|---------|----------|--------------------------------------------------------------------------------------|
| Id                           | integer |          | The unique identifier of the organisation                                            |
| Name                         | string  |          | The name of the organisation                                                         |
| Slug                         | string  |          | The URL-friendly identifier generated from the organisation name                     |
| CountryId                    | integer |          | The id of the organisation country, which links to cluster_country                   |
| TypeId                       | integer |          | The id of the organisation type, which links to the cluster_organisation_type export |
| VatNumber                    | string  | Yes      | The VAT number of the organisation                                                   |
| CompanyRegistrationNumber    | string  | Yes      | The company registration number of the organisation                                  |
| CompanyRegistrationAuthority | string  | Yes      | The authority that manages the organisation registration                             |

## cluster_organisation_type

| Column | Type    | Nullable | Description |
|--------|---------|----------|-------------|
| Id     | integer |          |             |
| Type   | string  |          |             |

## cluster_project_evaluation

This export contains project evaluation records. StatusId links to cluster_funding_status, CountryId links to
cluster_country, ProjectId links to cluster_project, and ProjectVersionId links to cluster_project_version when the
evaluation applies to a specific project version.

| Column           | Type    | Nullable | Description                                                                                                                     |
|------------------|---------|----------|---------------------------------------------------------------------------------------------------------------------------------|
| Id               | integer |          | The unique identifier of the evaluation record                                                                                  |
| Description      | string  |          | The text description of the evaluation                                                                                          |
| DateCreated      | string  |          | The date and time when the evaluation was created                                                                               |
| StatusId         | integer |          | The id of the funding status for this evaluation, which links to cluster_funding_status                                         |
| UserId           | integer |          | The id of the user who created or owns the evaluation                                                                           |
| CountryId        | integer |          | The id of the country for this evaluation, which links to cluster_country                                                       |
| ProjectId        | integer |          | The id of the related project, which links to cluster_project                                                                   |
| ProjectVersionId | integer | Yes      | The id of the related project version, which links to cluster_project_version; this is empty for a project-level funding status |

## cluster_funding_status

| Column           | Type    | Nullable | Description |
|------------------|---------|----------|-------------|
| Id               | integer |          |             |
| Code             | string  |          |             |
| Status           | string  |          |             |
| Color            | string  |          |             |
| StatusFunding    | string  |          |             |
| IsEvaluation     | boolean |          |             |
| StatusEvaluation | string  | Yes      |             |
| Sequence         | integer |          |             |

## country

This export contains all countries. Use the Id column to resolve CountryId references from other exports, such as
OrganisationColumns and FunderColumns.

| Column  | Type    | Nullable | Description                          |
|---------|---------|----------|--------------------------------------|
| Id      | integer |          | The unique identifier of the country |
| Cd      | string  | Yes      | The two-letter country code          |
| Country | string  | Yes      | The name of the country              |
| Iso3    | string  | Yes      | The three-letter ISO country code    |

## cluster_project_version

This export contains the versions of projects. ProjectId links to ProjectColumns, TypeId links to cluster_version_type,
StatusId links to cluster_version_type, and Countries is exported as a JSON array.

| Column         | Type    | Nullable | Description                                                        |
|----------------|---------|----------|--------------------------------------------------------------------|
| Id             | integer |          | The unique identifier of the project version                       |
| Identifier     | string  |          | The unique identifier string of the project version                |
| ProjectId      | integer |          | The id of the related project, which links to cluster_project      |
| TypeId         | integer |          | The id of the version type, which links to cluster_version_type    |
| SubmissionDate | date    | Yes      | The submission date of the project version                         |
| ReviewDate     | date    | Yes      | The review date of the project version                             |
| StatusId       | integer |          | The id of the version status, which links to cluster_version_type  |
| Costs          | float   |          | The total costs recorded for the project version, in EUR           |
| Effort         | float   |          | The total effort recorded for the project version, in PY           |
| Countries      | string  | Yes      | The countries associated with the project version, encoded as JSON |

## cluster_version_type

| Column      | Type    | Nullable | Description |
|-------------|---------|----------|-------------|
| Id          | integer |          |             |
| Type        | string  |          |             |
| Description | string  |          |             |

## cluster_version_status

| Column | Type    | Nullable | Description |
|--------|---------|----------|-------------|
| Id     | integer |          |             |
| Status | string  |          |             |

## cluster_project_version_costs_and_effort

This export contains the yearly costs and effort breakdown per project version and partner. PartnerId links to
PartnerColumns and VersionId links to VersionColumns.

| Column    | Type    | Nullable | Description                                                          |
|-----------|---------|----------|----------------------------------------------------------------------|
| Id        | integer |          | The unique identifier of the costs and effort record                 |
| PartnerId | integer |          | The id of the related project partner, which links to PartnerColumns |
| VersionId | integer |          | The id of the related project version, which links to VersionColumns |
| Year      | integer |          | The year for this costs and effort entry                             |
| Effort    | float   |          | The effort value for this year, partner, and project version, in PY  |
| Costs     | float   |          | The costs value for this year, partner, and project version, in EUR  |

## funder

This export contains all funders. UserId identifies the linked user account and CountryId links to CountryColumns.

| Column    | Type    | Nullable | Description                                                 |
|-----------|---------|----------|-------------------------------------------------------------|
| Id        | integer |          | The unique identifier of the funder record                  |
| UserId    | integer |          | The id of the user account linked to the funder             |
| CountryId | integer |          | The id of the funder country, which links to CountryColumns |

## country

This export contains all countries. Use the Id column to resolve CountryId references from other exports, such as
OrganisationColumns and FunderColumns.

| Column  | Type    | Nullable | Description                          |
|---------|---------|----------|--------------------------------------|
| Id      | integer |          | The unique identifier of the country |
| Cd      | string  | Yes      | The two-letter country code          |
| Country | string  | Yes      | The name of the country              |
| Iso3    | string  | Yes      | The three-letter ISO country code    |

## cluster_project_area

This export contains the project area records linked to projects. ProjectId links each area to the related project in
ProjectColumns.

| Column    | Type    | Nullable | Description                                                  |
|-----------|---------|----------|--------------------------------------------------------------|
| Id        | integer |          | The unique identifier of the project area record             |
| ProjectId | integer |          | The id of the related project, which links to ProjectColumns |
| Code      | string  |          | The code of the project area                                 |
| Label     | string  |          | The display label of the project area                        |
| Type      | string  |          | The type of project area                                     |
