## 6.4.x to 6.5: Cache access tokens for their platform lifetime

No changes are required. A cache passed to `LtiServiceConnector` can now also implement `Packback\Lti1p3\Interfaces\IAccessTokenExpiryCache`. When it does and the platform's token response includes `expires_in`, the connector calls `cacheAccessTokenWithExpiry()` with that value instead of `cacheAccessToken()`, so the cache can keep the token until shortly before it expires. Caches that only implement `ICache` behave as before.

## 6.3.x to 6.4: Platform Notification Service and Asset Processor

### Summary of Changes

- **NEW**: Support for Platform Notification Service and Notices
- **NEW**: Message types for Asset Processor, EULA, and Report Review requests
- **NEW**: `MessageFactory` for creating structured message objects from JWT payloads
- **NEW**: Individual claim classes in `Packback\Lti1p3\Claims\*` namespace with type-safe access methods
- **NEW**: Message classes in `Packback\Lti1p3\Messages\*` namespace for different LTI message types
- **NEW**: Support for Platform Notification Service (PNS) messages and Asset Processor functionality
- **DEPRECATED**: `LtiMessageLaunch` class (will be removed in v7.0)
- **DEPRECATED**: Constants in `LtiMessageLaunch` and `LtiConstants` (moved to `Claim` class and factories)
- **CHANGED**: `IMigrationDatabase` interface methods now accept both old and new message object types

### Migration Recommendations

1. **Migrate from LtiMessageLaunch to MessageFactory:** Replace `LtiMessageLaunch` usage with the new `MessageFactory` pattern for creating structured message objects
2. **Update constant usage:** Replace deprecated constants from `LtiMessageLaunch` and `LtiConstants` with new structured equivalents in `Claim` class and factories
3. **Access claims through message objects:** Use message-specific claim methods instead of direct array access to JWT body
4. **Update migration database implementations:** Ensure `IMigrationDatabase` implementations handle both `LtiMessageLaunch` and new `LaunchMessage` objects

The deprecated constants and `LtiMessageLaunch` will continue to work in version 6.* but will be removed in version 7.0. Plan to migrate away from them before upgrading to version 7.

### NEW FEATURES: Structured Message and Claims handling

Version 6.4 introduces structured message factories and claim classes that replace direct array access patterns.

**Example usage for a Deep Linking Request:**

```php
use Packback\Lti1p3\Factories\MessageFactory;
use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Packback\Lti1p3\Claims\ContextClaim;
use Packback\Lti1p3\Claims\DeepLinkSettings;

// Use the message Factory to create a message from the request
$messageFactory = new MessageFactory($db, $serviceConnector, $cache, $cookie);
/** @var DeepLinkingRequest $message */
$message = $messageFactory->create($request->all());

// Access claims through the message object
$messageType = $message::messageType(); // LtiDeepLinkingRequest
/** @var ContextClaim */
$context = $message->contextClaim(); // {"id":"8893483","label":"Biology 102","title":"Bio Adventures"}

// Access structured message-specific data
if ($message instanceof DeepLinkingRequest) {
    /** @var DeepLinkSettings $settings */
    $settings = $message->deepLinkSettingsClaim();
    if ($settings->canAcceptType('file')) {
        // Do something
    }
}
```

### NEW FEATURES: Support for new Platform Notification Service (PNS) and Asset Processor

- New message validators for Asset Processor messages, EULA requests, and Report Review requests
- Added support for Platform Notification Service (PNS) messages
- Enhanced deep linking support with structured settings and content items
- New payload classes for Asset Processor and Report functionality

### HIGH LIKELIHOOD OF IMPACT: Deprecation of constants in `LtiMessageLaunch` and `LtiConstants`

The `LtiMessageLaunch` has been deprecated in favor of using the new `MessageFactory` to create `LtiMessage` objects.

Many constants have been deprecated in favor of new structured classes. The deprecated constants will be removed in version 7.0:

**LtiMessageLaunch constants:**
```php
// Deprecated constants (use new ones instead):
LtiMessageLaunch::TYPE_DEEPLINK -> LtiConstants::MESSAGE_TYPE_DEEPLINK
LtiMessageLaunch::TYPE_SUBMISSIONREVIEW -> LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW  
LtiMessageLaunch::TYPE_RESOURCELINK -> LtiConstants::MESSAGE_TYPE_RESOURCE

// Error constants moved to factories:
LtiMessageLaunch::ERR_FETCH_PUBLIC_KEY -> JwtPayloadFactory::ERR_FETCH_PUBLIC_KEY
LtiMessageLaunch::ERR_NO_PUBLIC_KEY -> JwtPayloadFactory::ERR_NO_PUBLIC_KEY
LtiMessageLaunch::ERR_NO_MATCHING_PUBLIC_KEY -> JwtPayloadFactory::ERR_NO_MATCHING_PUBLIC_KEY
LtiMessageLaunch::ERR_STATE_NOT_FOUND -> JwtPayloadFactory::ERR_STATE_NOT_FOUND
LtiMessageLaunch::ERR_MISSING_ID_TOKEN -> JwtPayloadFactory::ERR_MISSING_ID_TOKEN
LtiMessageLaunch::ERR_INVALID_ID_TOKEN -> JwtPayloadFactory::ERR_INVALID_ID_TOKEN
LtiMessageLaunch::ERR_MISSING_NONCE -> MessageFactory::ERR_MISSING_NONCE
LtiMessageLaunch::ERR_INVALID_NONCE -> MessageFactory::ERR_INVALID_NONCE
```

**LtiConstants claim constants:**
```php
// All claim constants moved to Claim class:
LtiConstants::VERSION -> Claim::VERSION
LtiConstants::DEPLOYMENT_ID -> Claim::DEPLOYMENT_ID
LtiConstants::ROLES -> Claim::ROLES
LtiConstants::FOR_USER -> Claim::FOR_USER
LtiConstants::MESSAGE_TYPE -> Claim::MESSAGE_TYPE
LtiConstants::TARGET_LINK_URI -> Claim::TARGET_LINK_URI
LtiConstants::RESOURCE_LINK -> Claim::RESOURCE_LINK
```

### LOW LIKELIHOOD OF IMPACT: Changes to `IMigrationDatabase` interface

The `IMigrationDatabase` interface methods now accept either `LtiMessageLaunch` or the new `LaunchMessage` objects:

```php
// Updated method signatures:
public function findLti1p1Keys($launch): array; // was: LtiMessageLaunch $launch
public function shouldMigrate($launch): bool; // was: LtiMessageLaunch $launch  
public function migrateFromLti1p1($launch): ?ILtiDeployment; // was: LtiMessageLaunch $launch
```

Custom implementations should update to handle both object types.

### MEDIUM LIKELIHOOD OF IMPACT: PHP-JWT 7 rejects RSA keys under 2048 bits

Version 6.4 requires `firebase/php-jwt` 7, which refuses to verify a token signed with an RSA key shorter than 2048 bits. Launches from a platform that signs with a shorter key will fail until the platform rotates to a 2048-bit or longer key.

Every failure to decode the `id_token` throws an `LtiException`, with the original php-jwt exception available from `getPrevious()`. The message says what went wrong:

```php
JwtPayloadFactory::ERR_UNUSABLE_PLATFORM_KEY // the platform key is too short or cannot be read
JwtPayloadFactory::ERR_TOKEN_NOT_YET_VALID   // iat or nbf is in the future, usually clock drift
JwtPayloadFactory::ERR_INVALID_SIGNATURE     // expired, tampered, malformed, or any other failure
```

Earlier 6.4 releases let a short key, a tampered signature, or a token that was not yet valid escape as a raw php-jwt exception (`DomainException`, `SignatureInvalidException`, or `BeforeValidException`). Code that caught those exceptions directly should catch `LtiException` instead. The deprecated `LtiMessageLaunch` reports all of these as `ERR_INVALID_SIGNATURE`.

## 5.x to 6.0

### HIGH LIKELIHOOD OF IMPACT: Changes to `LtiMessageLaunch`

When handling a new launch, the new `initialize()` method should be used instead of the previous `validate()` method. The validate method no longer accepts arguments, and requires that the request be set on the message launch object first (which happens in `initialize()`). This fixes some separation-of-concern issues with the `validate()` method, and allows for seamless integration of LTI 1.1 to 1.3 migrations if enabled.

```php
// instead of doing this:
$message->validate($request);

// you should do this:
$message->initialize($request);
```

### HIGH LIKELIHOOD OF IMPACT: Changed how the OIDC Login URL is retrieved, deprecated the `Redirect` object

When redirecting to the OIDC Login URL, the `Packback\Lti1p3\LtiOidcLogin::getOidcLoginUrl()` method should be used to retrieve the URL. Your application should use this to build the redirect response in whatever way is appropriate for your framework. This replaces `Packback\Lti1p3\LtiOidcLogin::doOidcLoginRedirect()`, which returned a `Redirect` object. See: https://github.com/packbackbooks/lti-1-3-php-library/pull/116

```php
// instead of doing this:
$redirect = $oidLogin->doOidcLoginRedirect($launchUrl, $request);
return redirect($redirect->getRedirectUrl());

// you should do this:
return redirect($oidLogin->getRedirectUrl($launchUrl, $request));
```

### HIGH LIKELIHOOD OF IMPACT - Strict typing added

All arguments and returns are now strictly typed. This includes interfaces that require custom implementations. Notable changes:

```php
Packback\Lti1p3\Interfaces\ICookie
    setCookie(string $name, string $value, int $exp = 3600, array $options = []): void;

Packback\Lti1p3\Interfaces\IDatabase
    findRegistrationByIssuer(string $iss, ?string $clientId = null): ?ILtiRegistration;
    findDeployment(string $iss, string $deploymentId, ?string $clientId = null): ?ILtiDeployment;

Packback\Lti1p3\Interfaces\IMigrationDatabase
    migrateFromLti1p1(LtiMessageLaunch $launch): ?ILtiDeployment;
```

### Dropped support for PHP 7 and PHP-JWT 5

This library now requires PHP 8 and firebase/php-jwt 6.

### `Packback\Lti1p3\DeepLinkResource*` objects moved to their own namespace

Objects named `DeepLinkResource*` have been moved to their own namespace: `Packback\Lti1p3\DeepLinkResources`.  The following classes have been moved:

- `Packback\Lti1p3\DeepLinkResourceDateTimeInterval` is now `Packback\Lti1p3\DeepLinkResources\DateTimeInterval`
- `Packback\Lti1p3\DeepLinkResourceIcon` is now `Packback\Lti1p3\DeepLinkResources\Icon`
- `Packback\Lti1p3\DeepLinkResourceIframe` is now `Packback\Lti1p3\DeepLinkResources\Iframe`
- `Packback\Lti1p3\DeepLinkResource` is now `Packback\Lti1p3\DeepLinkResources\Resource`
- `Packback\Lti1p3\DeepLinkResourceWindow` is now `Packback\Lti1p3\DeepLinkResources\Window`

### `Packback\Lti1p3\DeepLinkResources\Iframe` constructor arguments changed order

To make the interface consistent with other deep link resources, `src` is now the first argument in the constructor:

```php
class Iframe
{
    public function __construct(
        private ?string $src = null,
        private ?int $width = null,
        private ?int $height = null
    ) {
    }
}
```

### Removed `ImsStorage` classes

Everything in the `Packback\Lti1p3\ImsStorage` namespace has been removed, specifically the `Packback\Lti1p3\ImsStorage\ImsCache` and `Packback\Lti1p3\ImsStorage\ImsCookie`. If you were using these classes, you will need to implement your own custom storage services. See the [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#sample-data-store-implementations) for an example.

### Removed deprecated methods and classes

The following classes have been removed:

* `Packback\Lti1p3\ImsStorage\ImsCache`
* `Packback\Lti1p3\ImsStorage\ImsCookie`
* `Packback\Lti1p3\Redirect`

The following methods have been removed:

* `Packback\Lti1p3\JwksEndpoint::outputJwks()` - use `getPublicJwks()` to build your own output
* `Packback\Lti1p3\LtiDeepLink::outputResponseForm()` - use `getResponseJwt()` to build your own output
* `Packback\Lti1p3\LtiDeepLinkResources\Resource::getTarget()` - consider using `getIframe()` or `getWindow()` instead
* `Packback\Lti1p3\LtiDeepLinkResources\Resource::setTarget()` - consider using `setIframe()` or `setWindow()` instead
* `Packback\Lti1p3\Redirect::doHybridRedirect()`
* `Packback\Lti1p3\Redirect::getRedirectUrl()`

### Changes to method signatures

* When instantiating `LtiMessageLaunch`, `LtiOidcLogin`, `LtiDeployment` and `LtiServiceConnector` objects, all arguments are required now (instead of some being optional).
* `Lti1p1Key` methods `setKey()` and `setSecret()` accept strings instead of arrays.
* `LtiServiceConnector::setDebuggingMode()` now returns self instead of void.

## 5.6 to 5.7

No breaking changes were introduced. However, going forward when processing a `LtiOidcLogin`, it is recommended to use the new `getRedirectUrl()` method:

```php
// Do this:
$redirect = $oidcLogin->getRedirectUrl($launchUrl, $request);
// Then do the redirect yourself (Laravel):
return redirect($redirect);
// Or some other method
header('Location: '.$this->location, true, 302);
exit;

// Instead of the old method:
$redirect = $oidcLogin->doOidcLoginRedirect($launchUrl, $request);
$redirect->doRedirect();
```

The `LtiOidcLogin::doOidcLoginRedirect()` method and `Redirect` object will be deprecated in the next major version.

## 5.5 to 5.6

No breaking changes were introduced. However, going forward when processing a `LtiMessageLaunch`, it is recommended to do `$message->initialize($request);` instead of the previous `$message->validate($request);` to support potential migrations.

## 4.0 to 5.0

### Changes to `ICache` methods

Version 5.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface.

* The method `checkNonce()` was renamed to `checkNonceIsValid()`.
* A second required argument (`$state`) was added to the `cacheNonce()` and `checkNonceIsValid()` methods.
* Stricter typing was introduced for several methods: `getLaunchData`, `cacheLaunchData`, `cacheNonce`, `cacheAccessToken`, `getAccessToken`, and `clearAccessToken`.

Stricter typing was added to methods on several interfaces.

* On `ICache` the following methods are now more strictly typed: `getLaunchData`, `cacheLaunchData`, `cacheNonce`, `cacheAccessToken`, `getAccessToken`, and `clearAccessToken`.
* On `ICookie` the following methods are now more strictly typed: `getCookie`, and `setCookie`.

### Stricter typing on `ICache` and `ICookie` methods

Arguments and return types are now strictly typed for methods on the cache and cookie interfaces. Custom implementations of these objects should be updated to adhere to the new typing requirements. View the interface definitions for [`Packback\Lti1p3\Interfaces\ICache`](https://github.com/packbackbooks/lti-1-3-php-library/blob/master/src/Interfaces/ICache.php) and [`Packback\Lti1p3\Interfaces\ICookie`](https://github.com/packbackbooks/lti-1-3-php-library/blob/master/src/Interfaces/ICookie.php) to see specific typing requirements.

## 3.0 to 4.0

### New methods implemented on the `ILtiServiceConnector`

Version 4.0 introduced changes to the `Packback\Lti1p3\Interfaces\ILtiServiceConnector` interface, adding the following methods:

* `setDebuggingMode()`
* `makeRequest()`
* `getRequestBody()`

## 2.0 to 3.0

### New method implemented on the `ICache`

Version 3.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface, adding one method: `clearAccessToken()`. This method must be implemented to any custom implementations of the interface. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cache) contains an example.

### Using `GuzzleHttp\Client` instead of curl

The `Packback\Lti1p3\LtiServiceConnector` now uses Guzzle instead of curl to make requests. This puts control of this client and its configuration in the hands of the developer. The section below contains information on implementing this change.

### Changes to the `LtiServiceConnector` and LTI services

The implementation of the `Packback\Lti1p3\LtiServiceConnector` changed to act as a general API Client for the various LTI service (Assignment Grades, Names Roles Provisioning, etc.) Specifically, the constructor for the following classes now accept different arguments:

* `LtiAssignmentGradesService`
* `LtiCourseGroupsService`
* `LtiNamesRolesProvisioningService`
* `LtiServiceConnector`

The `LtiServiceConnector` now only accepts an `ICache` and `GuzzleHttp\Client`, and does not need an `ILtiRegistration`. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#installation) contains an example of how to implement the service connector and configure the client.

The other LTI services now accept an `ILtiServiceConnector`, `ILtiRegistration`, and `$serviceData` (the registration was added as a new argument since it is no longer required for the `LtiServiceConnector`).

## 1.0 to 2.0

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
