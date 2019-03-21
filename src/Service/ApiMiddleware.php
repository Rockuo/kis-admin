<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 06.03.19
 * Time: 20:15
 */

namespace App\Service;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ApiMiddleware
{
    /**
     * @var Client
     */
    private $client;

    const BASE_CONF = [
        'base_uri' => 'https://su-dev.fit.vutbr.cz/kis/api/',
        'defaults' => [
            RequestOptions::ALLOW_REDIRECTS => false,
        ]
    ];

    const ROUTE_USERS_ME = 'users/me';

    const ROUTE_AUTH_EDUID_LOGIN = 'auth/eduid/login';
    const ROUTE_AUTH_EDUID_REG = 'auth/eduid/register';
    const ROUTE_AUTH_EDUID = 'auth/eduid';
    const ROUTE_AUTH_REFRESH = 'auth/fresh_token';


    const ROUTE_USERS = 'users';
    const ROUTE_USERS_ID = 'users/{user_id}';
    const ROUTE_USERS_EMAIL = 'users/{user_id}/email';
    const ROUTE_USERS_GAM = 'users/{user_id}/gamification_consent';
    const ROUTE_USERS_NAME = 'users/{user_id}/name';
    const ROUTE_USERS_NICKNAME = 'users/{user_id}/nickname';
    const ROUTE_USERS_ROLE = 'users/{user_id}/role';

    const ROUTE_ARTICLES = 'articles';
    const ROUTE_ARTICLES_ID = 'articles/{article_id}';
    const ROUTE_ARTICLES_COMPONENTS = 'articles/{article_id}/components';
    const ROUTE_ARTICLES_IMAGE = 'articles/{article_id}/image';
    const ROUTE_ARTICLES_LABELS = 'articles/{article_id}/labels';
    const ROUTE_ARTICLES_TARIFFS = 'articles/{article_id}/tariffs';

    const ROUTE_CASHBOXES = 'cashboxes';
    const ROUTE_CASHBOX_ID = 'cashbox/{cashbox_id}';

    const ROUTE_TAPS = 'beer/taps';
    const ROUTE_TAPS_ID = 'beer/taps/{tap_id}';

    const ROUTE_LABELS = 'labels';
    const ROUTE_LABELS_ID = 'labels/{label_id}';


    const ROUTE_INHERITABLE_KEGS = 'articles/inheritable_kegs';

    const ROUTE_ME_PIN = 'users/me/pin';


    const USER_ROLES = [
            'disabled_user' => 'Disabled user',
            'sympathizing_member' => 'Sympathizing member',
            'regular_member' => 'Regular member',
            'manager' => 'Manager',
            'administrator' => 'Administrator',
    ];

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ApiMiddleware constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->initClient($session);
    }

    public function initClient(SessionInterface $session)
    {
        $this->session = $session;
        $conf = self::BASE_CONF;

        $authData = $session->get('auth_data');
        if ($authData && $authData['token_type'] === 'Bearer') {
            $conf[RequestOptions::HEADERS]['Authorization'] = 'Bearer' . ' ' . $authData['auth_token'];
        }

        $this->client = new Client($conf);
    }


    protected function processURL(string $url, array &$values)
    {
        foreach ($values as $key => $value)
        {
            if (strpos($url, '{'.$key.'}') !== false)
            {
                $url = str_replace('{'.$key.'}', $value, $url);
                unset($values[$key]);
            }
        }
        return $url;
    }

    public function get(string $url, array $query = [])
    {
        return $this->client->get($this->processURL($url, $query), [RequestOptions::QUERY => $query,]);
    }


    public function delete(string $url, array $query = [])
    {
        return $this->client->delete($this->processURL($url, $query), [RequestOptions::QUERY => $query,]);
    }

    public function put(string $url, array $query = [],  array $data = [])
    {
        return $this->client->put(
            $this->processURL($url, $query),
            [RequestOptions::QUERY => $query, RequestOptions::JSON => $data]
        );
    }

    public function putJSON(string $url, array $query = [], array $data = [], $asoc = true)
    {
        $response = $this->put($url, $query, $data);
        return json_decode($response->getBody()->getContents(), $asoc);
    }


    public function getJSON(string $url, array $query = [], $asoc = true)
    {
        $response = $this->get($url, $query);
        return json_decode($response->getBody()->getContents(), $asoc);
    }

    public function post(string $url, array $query = [], array $data = [])
    {
        return $this->client->post(
            $this->processURL($url, $query),
            [RequestOptions::QUERY => $query, RequestOptions::JSON => $data]
        );
    }

    public function postJSON(string $url, array $query = [], array $data = [], $asoc = true)
    {
        $response = $this->post($url, $query, $data);
        return json_decode($response->getBody()->getContents(), $asoc);
    }

    public function refreshToken():bool
    {
        try {
            $authData = $this->session->get('auth_data');
            if ($authData && $authData['token_type'] === 'Bearer') {
                $conf = self::BASE_CONF;

                $authData = $this->session->get('auth_data');
                if ($authData && $authData['token_type'] === 'Bearer') {
                    $conf[RequestOptions::HEADERS]['Authorization'] = 'Bearer' . ' ' . $authData['auth_token'];
                }

                $client = new Client($conf);
                $response = $client->get(self::ROUTE_AUTH_REFRESH, [RequestOptions::QUERY => ['refresh_token' => $authData['refresh_token']],]);
                $responseData = json_decode($response->getBody()->getContents(), true);
                if ($responseData['token_type'] !== 'Bearer') {
                    return false;
                }
                $responseData['refresh_token'] = $authData['refresh_token'];
                $this->session->set('auth_data', $responseData);
                $this->initClient($this->session);
            }
        } catch (\Exception $e)
        {
            return false;
        } catch (\Error $e)
        {
            return false;
        }
        return true;
    }

    public function sendFile(string $url, UploadedFile $file, array $urlParams = [])
    {
        $size = $file->getSize();
        $fp = fopen("php://temp/maxmemory:$size", 'r+');
        fputs($fp, $file->openFile()->fread($size));
        rewind($fp);


        $ch = curl_init(self::BASE_CONF['base_uri'].$this->processURL($url, $urlParams));
        $authorization = "Authorization: Bearer ".$this->session->get('auth_data')['auth_token'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/octet-stream' , $authorization ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        $result = curl_exec($ch);
        curl_close($ch);

        fclose($fp);

        return json_decode($result, true);
    }
}