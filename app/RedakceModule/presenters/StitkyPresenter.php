<?php

namespace App\RedakceModule\Presenters;

use Nette,
    App\Model;
use Nette\Application\UI\Form;

/**
 * Homepage presenter.
 */
class StitkyPresenter extends BasePresenter
{

    public function createComponentStitek() {
        $form = new Form();
        $form->addText("stitek", "Štítek")
            ->setRequired("Zadejte prosím štítek.");
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = function($form) {
            $vals = $form->getValues();
            $this->clanky->addStitek($vals['stitek']);
            $this->flashMessage("Štítek přidán.");
            $this->redirect("default");
        }
        return $form;
    }

    public function renderDefault() {
        $this->template->stitky = $this->clanky->getStitky();
    }

}
