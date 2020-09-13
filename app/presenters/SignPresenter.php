<?php

namespace App\Presenters;


use Jumbojett\OpenIDConnectClient;
use Models\Uzivatele;
use Nette\Application\LinkGenerator;
use Nette\Http\Request;
use Nette\Http\Session;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
    /** @var Uzivatele @inject */
    public $uzivatele;

    /** @var Request @inject */
    public $httpRequest;

    /** @var Session @inject */
    public $session;

    /** @var LinkGenerator @inject */
    public $generator;

    private $sso;

    public function __construct($sso) {
        $this->sso = $sso;
    }

    public function actionKeycloak() {
        $oidc = new OpenIDConnectClient($this->sso['server'],
            $this->sso['id'],
            $this->sso['secret']);
        $oidc->setRedirectURL($this->generator->link('Sign:keycloak'));
        try {
            $oidc->authenticate();
            $userInfo = $oidc->requestUserInfo();
        } catch (\Exception $e) {
            $this->flashMessage('Přihlášení přes Keycloak se nezdařilo.');
            $this->redirect("Homepage:");
        }

        $uzivatel = $this->uzivatele->addKeycloak($userInfo->sub, $userInfo->preferred_username, $userInfo->email);
        $role = $this->uzivatele->getRole($uzivatel->id);
        $identity = new \Nette\Security\Identity($uzivatel->id, $role, $uzivatel);
        $this->getUser()->login($identity);
        $this->flashMessage("Uživatel přihlášen přes Keycloak");
        $this->redirect("Homepage:");

    }


    public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen.');
		$this->redirect('Homepage:');
	}

}
