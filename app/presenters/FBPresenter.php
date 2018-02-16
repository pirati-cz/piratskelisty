<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class FBPresenter extends BasePresenter
{



	public function renderDefault()
	{
		$this->template->feeds = array("www.facebook.com/odspraha",
		"www.facebook.com/zelenapraha",
		"www.facebook.com/TOP09.Praha",
		"www.facebook.com/KduCslPraha",
		"www.facebook.com/kscmpraha",
		"www.facebook.com/CeskaPiratskaStranaPraha",
		"www.facebook.com/ANOPraha",
		"www.facebook.com/socdempraha",
		"www.facebook.com/TomasHudecek.politik"
		
		
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
