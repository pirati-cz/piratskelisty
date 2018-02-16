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


    public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen.');
		$this->redirect('Homepage:');
	}

}
