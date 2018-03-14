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

}
