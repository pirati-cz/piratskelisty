<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Feeds presenter.
 */
class FeedsPresenter extends BasePresenter
{
  private $feeds;
  private $feedGroups;

  public function injectUzivatele(\Models\Feeds $feeds, \Models\FeedGroups $feedGroups) {
      $this->feeds = $feeds;
      $this->feedGroups = $feedGroups;
  }

	public function renderDefault($url = null)
	{
    if (empty($url)) {
      throw new \Nette\Application\BadRequestException();
    }
    $group = $this->feedGroups->getByUrl($url);
    if (empty($group)) {
      throw new \Nette\Application\BadRequestException();
    }
    $this->template->group = $group;
    $this->template->feeds = $this->feeds->getAllByFeedGroupId($group->id);

  }
  public function renderPraha() {
		$this->template->feeds = array("odspraha",
		"zelenapraha",
		"TOP09.Praha",
		"KduCslPraha",
		"kscmpraha",
		"CeskaPiratskaStranaPraha",
		"ANOPraha",
		"socdempraha",
		"TomasHudecek.politik"


		);
    }

	public function renderKraje() {
		$this->template->feeds = array(
			"www.facebook.com/CPS.JMK",
			"www.facebook.com/olomoucko.pirati",
			"www.facebook.com/plzenska.piratska.strana",
			"www.facebook.com/piratizl",
			"www.facebook.com/pirati.pardubicko",
			"www.facebook.com/cpsmsk",
			"www.facebook.com/pirati.stc",
			"www.facebook.com/pirati.khk",
			"www.facebook.com/pirati.jck",
			"www.facebook.com/cpslbc",
			"www.facebook.com/pirati.ulk",
			"www.facebook.com/pirati.vysocina",
			"www.facebook.com/pirati.karlovarsko",
			"www.facebook.com/CeskaPiratskaStranaPraha"
			);
	}




}
