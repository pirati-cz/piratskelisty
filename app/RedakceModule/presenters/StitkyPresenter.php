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
    public function startup() {
        parent::startup();
        if (!$this->getUser()->isAllowed("stitky")) {
            $this->flashMessage("Nemáte oprávnění pro vstup do této sekce.");
            $this->redirect(":Homepage:");
        }
    }
    public function createComponentStitek() {
        $form = new Form();
        $form->addText("stitek", "Štítek")
            ->setRequired("Zadejte prosím štítek.");
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = function($form) {
            $vals = $form->getValues();
            try {
                $this->clanky->addStitek($vals['stitek']);
            } catch (\Nette\Database\UniqueConstraintViolationException $e) {
              $this->flashMessage("Štítek ".$vals['stitek']." se nepodařilo přidat; není unikátní.");
              $this->redirect("this");
            }
            $this->flashMessage("Štítek ".$vals['stitek']." přidán.");
            $this->redirect("default");
        };
        return $form;
    }

    public function renderDefault() {
        $this->template->stitky = $this->clanky->getStitky();
    }

}
