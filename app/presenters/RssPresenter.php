<?php

namespace App\Presenters;

use Nette,
    App\Model;


/**
 * Homepage presenter.
 */
class RssPresenter extends BasePresenter
{
    /** @var  \Models\Rss */
    private $rss;

	public function injectRss(\Models\Rss $rss)
    {
        $this->rss = $rss;
    }


    public function renderDefault()
    {
        $this->template->clanky = $this->clanky->getClanky(10,0);

    }

    public function renderAktuality()
    {
        $this->template->clanky = $this->clanky->getClanky(10,0,"aktuality",null,true);
    }

	public function renderForum($key) {
		$channel = $this->rss->getChannelByKey($key);
		if (empty($channel)) {
			throw new Nette\Application\BadRequestException;
		}
		$this->template->channel = $channel;
		$this->template->items = $this->rss->getItemsByKey($key);
	}
}
