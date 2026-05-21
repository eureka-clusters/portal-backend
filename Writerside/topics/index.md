# Welcome

The Eureka Clusters backend is the shared data repository between the individual Eureka Clusters websites and the PA
portal at [eurekaclusters.eu](https://eurekaclusters.eu). It centralises project, partner, organisation, status, and
related portfolio data so the PA portal can work with one consistent dataset across clusters.

## What the portal is used for

The platform supports two main data flows:

1. **Inbound data exchange**: cluster backends push project updates into the central repository by using the API.
2. **Outbound data delivery**: the stored dataset can be exported for downstream reporting and analytics, including
   delivery to data lakes in **Parquet** or **CSV** form.

In addition to the import flow, the platform exposes a secured API for reading project data, organisations, partners,
statistics, and user details. The OpenAPI definition is generated in [available online](https://api.eurekaclusters.eu/swagger/).

## Documentation map

- [Importing project data](project-import.md) explains authentication, the upload flow, and the current JSON structure
  expected from cluster systems.
- [Exporting data to data lakes](export-to-parquet-files.md) describes the exported dataset and field-level structure.
- [Swagger / OpenAPI documentation](swagger-documentation.md) explains the generated API specification and how to use
  it.
- [Support](support.md) lists the contact path for access, data, and API questions.
