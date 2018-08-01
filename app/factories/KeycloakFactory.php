<?php

namespace App\Factories;

use \Stevenmaguire\OAuth2\Client\Provider\Keycloak;

class KeycloakFactory {

    private $serverUrl;
    private $realm;
    private $clientId;
    private $clientSecret;

    public function __construct($serverUrl, $realm, $clientId, $clientSecret) {
        $this->serverUrl = $serverUrl;
        $this->realm = $realm;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }


    /**
     * @return \Stevenmaguire\OAuth2\Client\Provider\Keycloak
     */
    public function create($redirectUri)
    {
      $keycloak = new Keycloak([
          'authServerUrl'         => $this->serverUrl,
          'realm'                 => $this->realm,
          'clientId'              => $this->clientId,
          'clientSecret'          => $this->clientSecret,
          'redirectUri'           => $redirectUri,
      ]);
      return $keycloak;
    }
}
