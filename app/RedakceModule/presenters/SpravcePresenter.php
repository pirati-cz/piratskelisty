<?php

namespace App\RedakceModule\Presenters;

use Nette,
    App\Model;
use Nette\Application\UI\Form;

/**
 * Homepage presenter.
 */
class SpravcePresenter extends BasePresenter
{
    private $uzivatele;

    public function injectUzivatele(\Models\Uzivatele $uzivatele) {
        $this->uzivatele = $uzivatele;
    }

    protected function createComponentRole() {
        $form = new Form;
        $form->addHidden("id");
        $cont = $form->addContainer("role");
        $role = $this->uzivatele->getRolePairs();
        foreach ($role as $k => $v) {
            $cont->addCheckbox($k,$v);
        }
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = $this->saveRole;
        return $form;
    }
    public function saveRole(Form $form) {
        $vals = $form->getValues();
        $this->uzivatele->setRole($vals['id'],$vals['role']);

        $this->flashMessage("Oprávnění uložena");
        $this->redirect("default");
    }
    public function startup() {
        parent::startup();
        if (!$this->getUser()->isAllowed("spravci")) {
            $this->flashMessage("Nemáte oprávnění pro vstup do této sekce.");
            $this->redirect(":Homepage:");
        }
    }
    public function renderDefault() {
        $this->template->uzivatele = $this->uzivatele->getAll();
    }

    public function actionRole($id) {
        $this->template->uzivatel = $this->uzivatele->get($id);
        $this['role']['id']->setValue($id);
        $role = $this->uzivatele->getRole($id);
        foreach ($role as $k => $r) {
            $this['role']['role'][$k]->setDefaultValue(1);
        }
    }
}
