<?php

namespace App\RedakceModule\Presenters;

use Nette,
    App\Model;


/**
 * Homepage presenter.
 */
class BasePresenter extends \App\Presenters\BasePresenter
{
    public function startup() {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage("Pro vstup do této sekce je vyžadováno přihlášení.");
            $this->redirect(":Homepage:");
        }
    }

}
