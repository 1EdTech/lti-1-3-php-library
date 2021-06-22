## 2.0

### Renamed Interfaces

A standard naming convention was implemented for interfaces: `Packback\Lti1p3\Interfaces\IObject`. Any implementations of these interfaces should be renamed:
* `Cache` to `ICache`
* `Cookie` to `ICookie`
* `Database` to `IDatabase`
* `LtiRegistrationInterface` to `ILtiRegistration`
* `LtiServiceConnectorInterface` to `ILtiServiceConnector`
* `MessageValidator` to `IMessageValidator`

### New methods implemented on the `ICache`

Version 2.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface, adding two new methods: `cacheAccessToken()` and `getAccessToken()`. These methods must be implemented to any custom implementations of the interface. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cache) contains an example.

## 1.0

Initial release. View the [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide).
