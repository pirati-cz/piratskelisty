<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{

    private $uzivatele;

    public function injectUzivatele(\Models\Uzivatele $uzivatele) {
        $this->uzivatele = $uzivatele;
    }

    private $httpRequest;

    public function injectRequest(\Nette\Http\Request $httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    private $keycloak;

    public function injectKeycloak(\App\Factories\KeycloakFactory $keycloak) {
        $this->keycloak = $keycloak;
    }

    /** @var Nette\Http\Session */
    private $session;

    public function injectSession(\Nette\Http\Session $session) {
        $this->session = $session;
    }


    public function actionPirateId() {
        $openId = new \LightOpenID($this->httpRequest->getUrl()->getAuthority());
        if(!$openId->mode) {
            $openId->identity = "https://openid.pirati.cz";
            $openId->required = array(
                'namePerson',
                'namePerson/first',
                'namePerson/last',
                'contact/email',
            );
            $this->redirectUrl($openId->authUrl());
        } elseif($openId->mode == 'cancel') {
            $this->flashMessage('Uživatel zrušil přihlašování.');
        } else {
            if ($openId->validate()) {
                $uzivatel = $this->uzivatele->add($openId);
                $role = $this->uzivatele->getRole($uzivatel->id);
                $identity = new \Nette\Security\Identity($openId->identity, $role, $uzivatel);
                $this->getUser()->login($identity);
                $this->flashMessage("Uživatel přihlášen");
            } else {
                $this->flashMessage("Přihlášení se nepodařilo.");
            }
        }


        $this->redirect(":Homepage:");
    }

    public function actionMojeId() {
        $openId = new \LightOpenID($this->httpRequest->getUrl()->getAuthority());
        if(!$openId->mode) {
            $openId->identity = "https://mojeid.cz/endpoint/";
            $openId->required = array(
                'namePerson',
                'namePerson/first',
                'namePerson/last',
                'contact/email',
            );
            $this->redirectUrl($openId->authUrl());
        } elseif($openId->mode == 'cancel') {
            $this->flashMessage('Uživatel zrušil přihlašování.');
        } else {
            if ($openId->validate()) {
                $uzivatel = $this->uzivatele->add($openId);
                $role = $this->uzivatele->getRole($uzivatel->id);
                $identity = new \Nette\Security\Identity($openId->identity, $role, $uzivatel);
                $this->getUser()->login($identity);
                $this->flashMessage("Uživatel přihlášen");
            } else {
                $this->flashMessage("Přihlášení se nepodařilo.");
            }
        }


        $this->redirect(":Homepage:");
    }

    public function actionKeycloak($code=null, $state=null) {
        $provider = $this->keycloak->create($this->link("//Sign:keycloak"));
        $oauth2state = $this->session->getSection('keycloak')->oauth2state;
        if (empty($code)) {
            $authUrl = $provider->getAuthorizationUrl();
            $this->session->getSection("keycloak")->oauth2state = $provider->getState();
            $this->redirectUrl($authUrl);
        } elseif (empty($state) || $state!==$oauth2state) {
            unset($this->session->getSection('keycloak')->oauth2state);
            $this->flashMessage("Neplatný stav přihlášení.");
            $this->redirect("Homepage:");
        } else {
          // Try to get an access token (using the authorization coe grant)
            try {
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $code
                ]);
            } catch (\Exception $e) {
                \Tracy\Debugger::log($e);
                $this->flashMessage('Přihlášení přes Keycloak se nezdařilo.');
                $this->redirect("Homepage:");
            }
            try {

                $oAuthUser = $provider->getResourceOwner($token);
                $uzivatel = $this->uzivatele->addKeycloak($oAuthUser);
                $role = $this->uzivatele->getRole($uzivatel->id);
                $identity = new \Nette\Security\Identity($uzivatel->id, $role, $uzivatel);
                $this->getUser()->login($identity);
                $this->flashMessage("Uživatel přihlášen přes KeyCloak");

            } catch (\Exception $e) {
                \Tracy\Debugger::log($e);
                $this->flashMessage('Získání údajů o uživateli z Keycloak se nezdařilo.');
                $this->redirect("Homepage:");
            }

        }
        $this->redirect("Homepage:");

    }


    public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen.');
		$this->redirect('Homepage:');
	}

}
