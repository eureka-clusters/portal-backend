# cluster_project

| Column                    | Type    | Nullable | Description |
|---------------------------|---------|----------|-------------|
| Id                        | integer |          |             |
| Identifier                | string  |          |             |
| Slug                      | string  |          |             |
| DateCreated               | string  |          |             |
| DateUpdated               | string  | Yes      |             |
| Number                    | string  |          |             |
| Name                      | string  |          |             |
| Title                     | string  |          |             |
| Description               | string  | Yes      |             |
| TechnicalArea             | string  | Yes      |             |
| Programme                 | string  |          |             |
| ProgrammeCall             | string  |          |             |
| PrimaryClusterId          | integer |          |             |
| SecondaryClusterId        | integer | Yes      |             |
| LabelDate                 | date    | Yes      |             |
| CancelDate                | date    | Yes      |             |
| OfficialStartDate         | date    | Yes      |             |
| OfficialEndDate           | date    | Yes      |             |
| StatusId                  | integer |          |             |
| ProjectLeader             | string  | Yes      |             |
| ProjectOutlineCosts       | float   | Yes      |             |
| ProjectOutlineEffort      | float   | Yes      |             |
| FullProjectProposalCosts  | float   |          |             |
| FullProjectProposalEffort | float   |          |             |
| LatestVersionCosts        | float   |          |             |
| LatestVersionEffort       | float   |          |             |

# cluster_cluster

| Column      | Type    | Nullable | Description |
|-------------|---------|----------|-------------|
| Id          | integer |          |             |
| Name        | string  |          |             |
| Identifier  | string  |          |             |
| Description | string  | Yes      |             |
| DateCreated | string  |          |             |
| DateUpdated | string  | Yes      |             |

# cluster_cluster_group

| Column      | Type    | Nullable | Description |
|-------------|---------|----------|-------------|
| Id          | integer |          |             |
| Name        | string  |          |             |
| Description | string  | Yes      |             |
| DateCreated | string  |          |             |
| DateUpdated | string  | Yes      |             |

# cluster_project_status

| Column | Type    | Nullable | Description |
|--------|---------|----------|-------------|
| Id     | integer |          |             |
| Status | string  |          |             |

# cluster_project_version

| Column         | Type    | Nullable | Description |
|----------------|---------|----------|-------------|
| Id             | integer |          |             |
| Identifier     | string  |          |             |
| ProjectId      | integer |          |             |
| TypeId         | integer |          |             |
| SubmissionDate | date    | Yes      |             |
| ReviewDate     | date    | Yes      |             |
| StatusId       | integer |          |             |
| Costs          | float   |          |             |
| Effort         | float   |          |             |
| Countries      | string  | Yes      |             |

# cluster_version_type

| Column      | Type    | Nullable | Description |
|-------------|---------|----------|-------------|
| Id          | integer |          |             |
| Type        | string  |          |             |
| Description | string  |          |             |

# cluster_version_status

| Column | Type    | Nullable | Description |
|--------|---------|----------|-------------|
| Id     | integer |          |             |
| Status | string  |          |             |

# cluster_project_version_costs_and_effort

| Column    | Type    | Nullable | Description |
|-----------|---------|----------|-------------|
| Id        | integer |          |             |
| PartnerId | integer |          |             |
| VersionId | integer |          |             |
| Year      | integer |          |             |
| Effort    | float   |          |             |
| Costs     | float   |          |             |

# cluster_project_partner

| Column                    | Type    | Nullable | Description |
|---------------------------|---------|----------|-------------|
| Id                        | integer |          |             |
| OrganisationId            | integer |          |             |
| ProjectId                 | integer |          |             |
| Slug                      | string  |          |             |
| OrganisationName          | string  |          |             |
| ProjectName               | string  |          |             |
| IsActive                  | boolean | Yes      |             |
| IsCoordinator             | boolean | Yes      |             |
| IsSelfFunded              | boolean | Yes      |             |
| TechnicalContact          | string  | Yes      |             |
| ProjectOutlineCosts       | float   | Yes      |             |
| ProjectOutlineEffort      | float   | Yes      |             |
| FullProjectProposalCosts  | float   | Yes      |             |
| FullProjectProposalEffort | float   | Yes      |             |
| LatestVersionCosts        | float   | Yes      |             |
| LatestVersionEffort       | float   | Yes      |             |

# cluster_organisation

| Column    | Type    | Nullable | Description |
|-----------|---------|----------|-------------|
| Id        | integer |          |             |
| Name      | string  |          |             |
| Slug      | string  |          |             |
| CountryId | integer |          |             |
| TypeId    | integer |          |             |

# country

| Column  | Type    | Nullable | Description |
|---------|---------|----------|-------------|
| Id      | integer |          |             |
| Cd      | string  | Yes      |             |
| Country | string  | Yes      |             |
| Iso3    | string  | Yes      |             |

# cluster_organisation_type

| Column | Type    | Nullable | Description |
|--------|---------|----------|-------------|
| Id     | integer |          |             |
| Type   | string  |          |             |

# cluster_project_evaluation

| Column           | Type    | Nullable | Description |
|------------------|---------|----------|-------------|
| Id               | integer |          |             |
| Description      | string  |          |             |
| DateCreated      | string  |          |             |
| StatusId         | integer |          |             |
| UserId           | integer |          |             |
| CountryId        | integer |          |             |
| ProjectId        | integer |          |             |
| ProjectVersionId | integer | Yes      |             |

# cluster_funding_status

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

# country

| Column  | Type    | Nullable | Description |
|---------|---------|----------|-------------|
| Id      | integer |          |             |
| Cd      | string  | Yes      |             |
| Country | string  | Yes      |             |
| Iso3    | string  | Yes      |             |

# cluster_project_version

| Column         | Type    | Nullable | Description |
|----------------|---------|----------|-------------|
| Id             | integer |          |             |
| Identifier     | string  |          |             |
| ProjectId      | integer |          |             |
| TypeId         | integer |          |             |
| SubmissionDate | date    | Yes      |             |
| ReviewDate     | date    | Yes      |             |
| StatusId       | integer |          |             |
| Costs          | float   |          |             |
| Effort         | float   |          |             |
| Countries      | string  | Yes      |             |

# cluster_version_type

| Column      | Type    | Nullable | Description |
|-------------|---------|----------|-------------|
| Id          | integer |          |             |
| Type        | string  |          |             |
| Description | string  |          |             |

# cluster_version_status

| Column | Type    | Nullable | Description |
|--------|---------|----------|-------------|
| Id     | integer |          |             |
| Status | string  |          |             |

# cluster_project_version_costs_and_effort

| Column    | Type    | Nullable | Description |
|-----------|---------|----------|-------------|
| Id        | integer |          |             |
| PartnerId | integer |          |             |
| VersionId | integer |          |             |
| Year      | integer |          |             |
| Effort    | float   |          |             |
| Costs     | float   |          |             |

# funder

| Column    | Type    | Nullable | Description |
|-----------|---------|----------|-------------|
| Id        | integer |          |             |
| UserId    | integer |          |             |
| CountryId | integer |          |             |

# country

| Column  | Type    | Nullable | Description |
|---------|---------|----------|-------------|
| Id      | integer |          |             |
| Cd      | string  | Yes      |             |
| Country | string  | Yes      |             |
| Iso3    | string  | Yes      |             |
