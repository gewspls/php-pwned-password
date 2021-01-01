# php-pwned-password

A PHP class library which uses the Have I Been Pwned Password API to detect if passwords your users are providing have been exposed in a data breach.

# How does it work?
This library uses the Have I Been Pwned Password API to check if a supplied password is present in their repository of exposed passwords. The API can be found [here](https://haveibeenpwned.com/API/v3) and all credit for the repository of exposed passwords and the service for checking them is attributed to [Have I Been Pwned](https://haveibeenpwned.com).

# Is it safe?
The API uses k-Anonimity, you can find more information in the API documentation [here](https://haveibeenpwned.com/API/v3#PwnedPasswords) but the TL;DR of the library is:

1. Generates an SHA-1 hash of the provided password.
2. Passes the first 5 characters of the generated SHA-1 hash to the API.
3. Searches through the returned responses to determine whether or not the password has been exposed.

The full SHA-1 hash of the provided password is never sent in the HTTP request to the API.

# Usage

View examples in the "examples" folder.

Basic usage is as follows:

```php
$passwordCheck = new gewspls\PwnedPassword;

if($passwordCheck->CheckPasswordExposure())
{
    echo "Exposed ".$passwordCheck->GetExposureCount()." times.";
}
```

# Public facing methods

### CheckPasswordExposure (bool)

Returns a boolean which is true if a match is found in the API response, meaning the password has been exposed.

### GetExposureCount (int)

Returns an integer relating to how many times the password was found in the repository of exposed passwords.