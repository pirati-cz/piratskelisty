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
