# Freee Socialite Provider

This repository contains [Freee](https://developer.freee.co.jp/) provider for Laravel [Socialite](https://github.com/laravel/socialite).

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Problems](#problems)
- [Credits](#credits)
- [License](#license)

## Installation

```bash
$ composer require setemares/freee-socialite
```

* You will need to extend Socialite in order to add Freee provider, go to AppServiceProvider.php and add:
```php
use SeteMares\Freee\Provider as FreeeProvider;
```

* To the `boot()` method add:
```php
    $this->bootFreeeSocialite();
```

* And create this method:
```php
    private function bootFreeeSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('freee', function ($app) use ($socialite) {
            $config = $app['config']['services.freee'];
            return $socialite->buildProvider(FreeeProvider::class, $config);
        });
    }
```

## Configuration

* Add to `config/services.php`:
```php
'freee' => [
    'client_id' => env('FREEE_CLIENT_ID', ''),
    'client_secret' => env('FREEE_CLIENT_SECRET', ''),
    'redirect' => env('FREEE_CALLBACK', 'urn:ietf:wg:oauth:2.0:oob')
]
```

* Add config variables to your `.env` file:
```ini
# Freee
FREEE_CLIENT_ID=Your_client_id
FREEE_CLIENT_SECRET=Your_client_secret
FREEE_CALLBACK=Your_callback_url
```

## Usage

* To get token use as any other socialite provider, e.g.
```php
    public function oauthRedirect()
    {
        return Socialite::driver('freee')
            ->with(['access_type' => 'offline'])
            ->redirect();
    }
    public function oauthCallback()
    {
        try {
            $user = Socialite::driver('freee')
                ->user();
        } catch (\Exception $e) {
            return $this->respondError(Lang::getFromJson("No user in oauth response"), 422);
        }
        // $user contains freee user `id` and `companies` array besides
        // token information (`token`, `expiresIn`, `refreshToken`)
    }
```

* To refresh token, call provided method refreshToken($refresh_token)
```php
    try {
        $data = Socialite::driver('freee')->refreshToken($refreshToken);
    } catch (\Exception $e) {
        // GuzzleHttp\Exception\ClientException:
        return $e->getMessage());
    }
```

## Problems

- Freee will reject issuing access code needed for obtaining access token if there is anything besides `urn:ietf:wg:oauth:2.0:oob` configured in `redirect_uri`, having `urn:ietf:wg:oauth:2.0:oob` in it will present token on screen, requiring user to manually copy the code and paste it into your app to continue the authentication flow.
- Another, more serious problem is with the ability to refresh token, when access token being issued without problem but upon it's expiration Freee api will refuse to provide new access token with `invalid_grant` error requiring user to repeat authentication flow.

## Credits

- [tectiv3](https://github.com/tectiv3)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
