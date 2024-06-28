# Zu/HealthCheckBundle
# ⚠️ This bundle is still in heavy development and not ready to be used!!! ⚠️
## Introduction

This bundle provides a simple health check endpoint for your Symfony application. It can be used to check the status of 
your application and its services.

This bundle provides two endpoints:
`/ping` for general health check
`/health-check` for more detailed health check for configured services.

To configure the services to be checked, you can add the following configuration to your `zu_health_check.yaml` file:

```yaml
zu_health_check:
    type:
        smtp: false
        doctrine: false
```

Error messages currently ask you to check logs, this has NOT been implemented yet.

## Requirements

- PHP version (e.g., PHP 8.3 or higher)
- Symfony version (e.g., Symfony 6.4 or higher)

### Doctrine Health Check
Requires `symfony/orm-pack` package.

### Mailer Health Check
Requires `symfony/mailer` package.

## Installation

- TODO

## Development
 - TODO

## Tests
- TODO