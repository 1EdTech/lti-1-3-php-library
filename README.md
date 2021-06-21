# LTI 1.3 Tool Library

![Test status](https://github.com/packbackbooks/lti-1-3-php-library/actions/workflows/run_tests.yml/badge.svg?branch=master)

A library used for building IMS-certified LTI 1.3 tool providers in PHP.

This library allows a tool provider (your app) to receive LTI launches from a tool consumer (i.e. LMS). It validates LTI launches and lets an application interact with services like the Names Roles Provisioning Service (to fetch a roster for an LMS course) and Assignment Grades Service (to update grades for students in a course in the LMS).

This library was forked from [IMSGlobal/lti-1-3-php-library](https://github.com/IMSGlobal/lti-1-3-php-library), initially created by @MartinLenord. [Packback](https://packback.co) found the library immensely helpful and extended it over the years. It has been rewritten by Packback to bring it into compliance with the standards set out by the PHP-FIG and the IMS LTI 1.3 Certification process. Packback actively uses and maintains this library.

## Installation

Run:

```bash
composer require packbackbooks/lti-1p3-tool
```

In your code, you will now be able to use classes in the `Packback\Lti1p3` namespace to access the library.

### Configure JWT

Add the following when bootstrapping your app.

```php
Firebase\JWT\JWT::$leeway = 5;
```

### Implement Data Storage Interfaces

This library uses three methods for storing and accessing data: cache, cookie, and database. All three must be implemented in order for the library to work. You may create your own custom implementations so long as they adhere to the following interfaces:

- `Packback\Lti1p3\Interfaces\Cache`
- `Packback\Lti1p3\Interfaces\Cookie`
- `Packback\Lti1p3\Interfaces\Database`

View the [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide) to see examples. Cache and Cookie storage have legacy implementations at `Packback\Lti1p3\ImsStorage\` if you do not wish to implement your own. However you must implement your own database.

### Create a JWKS endpoint

A JWKS (JSON Web Key Set) endpoint can be generated for either an individual registration or from an array of `KID`s and private keys.

```php
use Packback\Lti1p3\JwksEndpoint;

// From issuer
JwksEndpoint::fromIssuer($database, 'http://example.com')->outputJwks();
// From registration
JwksEndpoint::fromRegistration($registration)->outputJwks();
// From array
JwksEndpoint::new(['a_unique_KID' => file_get_contents('/path/to/private/key.pem')])->outputJwks();
```

## Contributing

For improvements, suggestions or bug fixes, make a pull request or an issue. Before opening a pull request, add automated tests for your changes, ensure that all tests pass, and any linting errors are fixed.

### Testing

Automated tests can be run using the command:

```bash
composer test
```

Linting can be run using

```bash
# Display linting errors
composer lint
# Automatically fix linting errors
composer lint-fix
```
