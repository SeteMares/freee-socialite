<?php

namespace SeteMares\Freee;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;
use GuzzleHttp\ClientInterface;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'FREEE';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            // 'https://secure.freee.co.jp/oauth/authorize/',
            'https://accounts.secure.freee.co.jp/public_api/authorize/',
            $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.freee.co.jp/oauth/token';
        // return 'https://accounts.secure.freee.co.jp/public_api/token';
    }

    /**
     * Refresh token
     *
     * @param $refresh_token
     *
     * @return object
     */
    public function refreshToken($refresh_token)
    {
        $params = array(
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refresh_token
        );

        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1)
            ? 'form_params'
            : 'body';
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            $postKey => $params
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://api.freee.co.jp/hr/api/v1/users/me',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'companies' => $user['companies'],
            'nickname' => null,
            'name' => null,
            'email' => null,
            'avatar' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }
}
