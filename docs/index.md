# ZuHealthCheckBundle Documentation

## Overview

The `ZuHealthCheckBundle` provides a simple yet powerful way to add health check capabilities to your Symfony application. It allows you to monitor the status of various services your application depends on, such as SMTP for email and Doctrine for database connectivity.

## Configuration

To configure the health checks, you need to modify the `zu_health_check.yaml` file in your project's `config` directory. Here's a breakdown of the available configuration options:

### `type`

This section allows you to enable or disable specific health checks.

- **`smtp`**: Enables or disables the SMTP health check. When enabled, it checks if your application can successfully send emails using the configured SMTP server.
    - **Requirements**: This check requires the `symfony/mailer` package to be installed.
    - **Default**: `false`
    - **Values**: `true` | `false`

- **`doctrine`**: Enables or disables the Doctrine health check. When enabled, it verifies that your application can communicate with the configured database.
    - **Requirements**: This check requires the `symfony/orm-pack` package to be installed.
    - **Default**: `false`
    - **Values**: `true` | `false`

### Example Configuration

```yaml
zu_health_check:
  type:
    smtp: false
    doctrine: false