<?php

namespace App\Presenters;

use Nette,
    App\Model, Nette\Application\UI\Form;


/**
 * Homepage presenter.
 */
class ClanekPresenter extends BasePresenter
{

    /** @var  \Models\Komentare */
    private $komentare;

    /** @persistent */
    public $id;

    public function injectKomentare(\Models\Komentare $komentare)
    {
        $this->komentare = $komentare;
    }

    protected function createComponentKomentar()
    {
        $form = new Form;
        $form->addText("jmeno", "Jméno")
            ->addRule(Form::FILLED, "Vyplňte prosím jméno.")
            ->addRule(Form::MAX_LENGTH, "Jméno je příliš dlouhé. Maximální délka jména je 64 znaků.", 64);

        $form->addTextArea("text", "Komentář")
            ->addRule(Form::FILLED, "Vyplňte prosím text komentáře.")
            ->addRule(Form::MAX_LENGTH, "Text komentáře je příliš dlouhý. Maximální délka textu je 1000 znaků.", 1000);
        $form->addHidden("skryte");
        $form->addSubmit("komentuj", "Přidat komentář");
        $form->addHidden("clanek_id")->setValue($this->id);
        $form->onSuccess[] = function($form) {
            $vals = $form->getValues();
            if ($vals['skryte'] == 'asdf') {
                $this->komentare->add($vals);
                $this->template->pridej_komentar = false;
            }
            $this->redrawControl("komentare");
            $this->redrawControl("pridej_komentar");

            if (!$this->isAjax()) {
                $this->redirect("this");
            }
        };
        return $form;
    }

    public function handleKomentuj()
    {
        $this->template->pridej_komentar = true;
        $this->redrawControl("pridej_komentar");
    }

    public function renderDefault($id, $nazev)
    {
        $clanek = $this->clanky->get($this->id);
        if (empty($clanek) || empty($clanek['id'])) {
            throw new \Nette\Application\BadRequestException();
        }

        $this->template->skupina = $this->clanky->getSouvisejici($id);
        $web_nazev = Nette\Utils\Strings::webalize($clanek['titulek']);

        if ($nazev != $web_nazev) {
            // throw new \Nette\Application\BadRequestException();

            $this->redirect("this", array("id" => $clanek['id'], "nazev" => $web_nazev));
        }
        $this->clanky->precteno($this->id);
        $this->template->clanek = $clanek;
        $this->template->title = $clanek['titulek'];
        $this->template->komentare = $this->komentare->getForClanek($this->id);
        $hodnoceni = $this->clanky->getHodnoceni($this->id);
        if (empty($hodnoceni['plus'])) $hodnoceni['plus'] = 0;
        if (empty($hodnoceni['minus'])) $hodnoceni['minus'] = 0;

        $this->template->hodnoceni = $hodnoceni;
        $this->template->moje_hodnoceni = $this->clanky->getHodnoceniByIp($this->id, $_SERVER['REMOTE_ADDR']);
    }


}
