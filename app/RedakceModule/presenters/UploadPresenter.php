<?php

namespace App\RedakceModule\Presenters;

use \Nette\Application\UI\Form;

class UploadPresenter extends BasePresenter
{
    /** @var Models/Upload */
    private $upload;

    public function injectUpload(\Models\Upload $upload)
    {
        $this->upload = $upload;
    }

    public function startup()
    {
        parent::startup();
        if (!$this->getUser()->isAllowed('soubory')) {
            $this->flashMessage("Nemáte oprávnění pro vstup do této sekce.");
            $this->redirect(":Homepage:");
        }
    }

    public function createComponentUpload()
    {
        $form = new Form;
        $form->addUpload('foto', 'Fotografie (max. 2 MB):')
				    ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 2 MB.', 2 * 1024 * 1024 /* v bytech */);

        $form->addTextarea("alt", "Popis");
        $form->addTextarea("title", "Titulek");

        $form->addSubmit('submit', 'Nahrát');
        $form->onSuccess[] = callback($this, 'handleSavePhoto');
        return $form;
    }

    public function handleSavePhoto($form)
    {
        $vals = $form->getValues();
        /*
         * Kontrola, zda-li byl obrazek skutecne nahran
         */
        if ($vals['foto']->isOk()) {
            if (empty($vals['alt'])) {
                $vals['alt'] = $vals['foto']->name;
            }
            $path_parts = pathinfo($vals['foto']->name);
            $extension = strtolower($path_parts['extension']);

            $extensions = array("jpg", "jpeg", "png", "pdf", "sla");
            if (!in_array($extension, $extensions )) {
                $this->flashMessage("Nepovolená přípona souboru. Povolené přípony jsou ".implode(", ", $extensions).".");
                $this->redirect("this");
            }

            $vals['extension'] = $extension;

            $upload = $this->upload->save($vals);

            $dir = WWW_DIR . "/upload/";

            $path = $dir . $upload['id'] . "." . $extension;

            $vals['foto']->move($path);

            $this->flashMessage('Soubor byl nahrán.', 'warning');
            //zpráva pro vypsání

        } else {
            $this->flashMessage('Obrázek se nezdařilo nahrát na server.', 'warning');
        }
        $this->redirect("this");
    }

    public function renderDefault()
    {
        $this->template->upload = $this->upload->getAll();
    }
}
