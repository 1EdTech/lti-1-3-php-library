
| **Note** : If you are looking for the example tool that uses this library, it has been moved into its own repo https://github.com/IMSGlobal/lti-1-3-php-example-tool |
| --- |

# LTI 1.3 Advantage Library
This code consists of a library for creating LTI tool providers in PHP.

# Library Documentation

## Importing the library
### Using Composer
Add the following to your `composer.json` file
```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/packbackbooks/lti-1-3-php-library"
    }
],
"require": {
    "packbackbooks/lti-1p3-tool": "dev-master"
}
```
Run `composer install` or `composer update`
In your code, you will now be able to use classes in the `\LTI` namespace to access the library.

## Accessing Registration Data

To allow for launches to be validated and to allow the tool to know where it has to make calls to, registration data must be stored.
Rather than dictating how this is store, the library instead provides an interface that must be implemented to allow it to access registration data.
The `LTI\Database` interface must be fully implemented for this to work.
```php
class Example_Database implements LTI\Database
{
    public function findRegistrationByIssuer($iss)
    {
        ...
    }
    public function findDeployment($iss, $deployment_id)
    {
        ...
    }
}
```

The `find_registration_by_issuer` method must return an `LTI\LtiRegistration`.
```php
return LTI\LtiRegistration::new()
    ->setAuthLoginUrl($auth_login_url)
    ->setAuthTokenUrl($auth_token_url)
    ->setClientId($client_id)
    ->setKeySetUrl($key_set_url)
    ->setKid($kid)
    ->setIssuer($issuer)
    ->setToolPrivateKey($private_key);
```

The `find_deployment` method must return an `LTI\LtiDeployment` if it exists within the database.
```php
return LTI\LtiDeployment::new()
    ->setDeploymentId($deployment_id);
```

Calls into the Library will require an instance of `LTI\Database` to be passed into them.

### Creating a JWKS endpoint
A JWKS (JSON Web Key Set) endpoint can be generated for either an individual registration or from an array of `KID`s and private keys.
```php
// From issuer
LTI\JwksEndpoint::fromIssuer(new Example_Database(), 'http://example.com')->outputJwks();
// From registration
LTI\JwksEndpoint::fromRegistration($registration)->outputJwks();
// From array
LTI\JwksEndpoint::new(['a_unique_KID' => file_get_contents('/path/to/private/key.pem')])->outputJwks();
```

## Handling Requests

### Open Id Connect Login Request
LTI 1.3 uses a modified version of the OpenId Connect third party initiate login flow. This means that to do an LTI 1.3 launch, you must first receive a login initialization request and return to the platform.

To handle this request, you must first create a new `LTI\LtiOidcLogin` object.
```php
$login = LtiOidcLogin::new(new Example_Database());
```

Now you must configure your login request with a return url (this must be preconfigured and white-listed on the tool).
If a redirect url is not given or the registration does not exist an `LTI\OidcException` will be thrown.
```php
try {
    $redirect = $login->doOidcLoginRedirect("https://my.tool/launch");
} catch (LTI\OidcException $e) {
    echo 'Error doing OIDC login';
}
```

With the redirect, we can now redirect the user back to the tool.
There are three ways to do this:

This will add a 302 location header and then exit.
```php
$redirect->doRedirect();
```

This will echo out some javascript to do the redirect instead of using a 302.
```php
$redirect->doJsRedirect();
```

You can also get the url you need to redirect to, with all the necessary query parameters, if you would prefer to redirect in a custom way.
```php
$redirect_url = $redirect->getRedirectUrl();
```

Redirect is now done, we can move onto the launch.

### LTI Message Launches
Now that we have done the OIDC log the platform will launch back to the tool. To handle this request, first we need to create a new `LTI\LtiMessageLaunch` object.
```php
$launch = LTI\LtiMessageLaunch::new(new Example_Database());
```

Once we have the message launch, we can validate it. This will check signatures and the presence of a deployment and any required parameters.
If the validation fails an exception will be thrown.
```php
try {
    $launch->validate();
} catch (Exception $e) {
    echo 'Launch validation failed';
}
```

Now we know the launch is valid we can find out more information about the launch.

Check if we have a resource launch or a deep linking launch.
```php
if ($launch->isResourceLaunch()) {
    echo 'Resource Launch!';
} else if ($launch->isDeepLinkLaunch()) {
    echo 'Deep Linking Launch!';
} else {
    echo 'Unknown launch type';
}
```

Check which services we have access to.
```php
if ($launch->hasAgs()) {
    echo 'Has Assignments and Grades Service';
}
if ($launch->hasNrps()) {
    echo 'Has Names and Roles Service';
}
```

### Accessing Cached Launch Requests

It is likely that you will want to refer back to a launch later during subsequent requests. This is done using the launch id to identify a cached request. The launch id can be found using:
```php
$launch_id = $launch->getLaunchId().
```

Once you have the launch id, you can link it to your session and pass it along as a query parameter.

**Make sure you check the launch id against the user session to prevent someone from making actions on another person's launch.**

Retrieving a launch using the launch id can be done using:
```php
$launch = LtiMessageLaunch::fromCache($launch_id, new Example_Database());
```

Once retrieved, you can call any of the methods on the launch object as normal, e.g.
```php
if ($launch->hasAgs()) {
    echo 'Has Assignments and Grades Service';
}
```

### Deep Linking Responses

If you receive a deep linking launch, it is very likely that you are going to want to respond to the deep linking request with resources for the platform.

To create a deep link response you will need to get the deep link for the current launch.
```php
$dl = $launch->getDeepLink();
```

Now we are going to need to create `LTI\LtiDeepLinkResource` to return.
```php
$resource = LTI\LtiDeepLinkResource::new()
    ->setUrl("https://my.tool/launch")
    ->setCustomParams(['my_param' => $my_param])
    ->setTitle('My Resource');
```

Everything is set to return the resource to the platform. There are two methods of doing this.

The following method will output the html for an aut-posting form for you.
```php
$dl->outputResponseForm([$resource]);
```

Alternatively you can just request the signed JWT that will need posting back to the platform by calling.
```php
$dl->getResponseJwt([$resource]);
```

## Calling Services
### Names and Roles Service

Before using names and roles you should check that you have access to it.
```php
if (!$launch->hasNrps()) {
    throw new Exception("Don't have names and roles!");
}
```

Once we know we can access it, we can get an instance of the service from the launch.
```php
$nrps = $launch->getNrps();
```

From the service we can get an array of all the members by calling:
```php
$members = $nrps->getMembers();
```

### Assignments and Grades Service
Before using assignments and grades you should check that you have access to it.
```php
if (!$launch->hasAgs()) {
    throw new Exception("Don't have assignments and grades!");
}
```

Once we know we can access it, we can get an instance of the service from the launch.
```php
$ags = $launch->getAgs();
```

To pass a grade back to the platform, you will need to create an `LTI\LtiGrade` object and populate it with the necessary information.
```php
$grade = LTI\LtiGrade::new()
    ->setScoreGiven($grade)
    ->setScoreMaximum(100)
    ->setTimestamp(date(DateTime::ISO8601))
    ->setActivityProgress('Completed')
    ->setGradingProgress('FullyGraded')
    ->setUserId($external_user_id);
```

To send the grade to the platform we can call:
```php
$ags->putGrade($grade);
```
This will put the grade into the default provided lineitem. If no default lineitem exists it will create one.

If you want to send multiple types of grade back, that can be done by specifying an `LTI\LtiLineitem`.
```php
$lineitem = LTI\LtiLineitem::new()
    ->setTag('grade')
    ->setScoreMaximum(100)
    ->setLabel('Grade');

$ags->putGrade($grade, $lineitem);
```

If a lineitem with the same `tag` exists, that lineitem will be used, otherwise a new lineitem will be created.


# Contributing
If you have improvements, suggestions or bug fixes, feel free to make a pull request or issue and someone will take a look at it.

You do not need to be an IMS Member to use or contribute to this library, however it is recommended for better access to support resources and certification.

This library was initially created by @MartinLenord from Turnitin to help prove out the LTI 1.3 specification and accelerate tool development.

**Note:** This library is for IMS LTI 1.3 based specifications only. Requests to include custom, off-spec or vendor-specific changes will be declined.

## Don't like PHP?
If you don't like PHP and have a favorite language that you would like to make a library for, we'd love to hear about it!
