<?php

namespace App\RedakceModule\Presenters;

use Nette,
    App\Model;
use Nette\Application\UI\Form;

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

    protected function createComponentGroup() {
        $form = new Form;
        $form->addHidden("id");
        $form->addText("name", "Název")
            ->setRequired("Zadejte prosím název.");
        $form->addText("url", "URL")
            ->setRequired("Zadejte prosím URL feedu.")
            ->addRule($form::PATTERN, "URL může obsahovat pouze písmena a čísla.", "[a-zA-Z0-9]+");
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = function($form, $vals) {
            $vals = $form->getValues();
            $this->feedGroups->save($vals);
            $this->flashMessage("Skupina feedů uložena");
            $this->redirect("default");
        };
        return $form;
    }
    protected function createComponentFeed() {
        $form = new Form;
        $form->addHidden("id");
        $form->addText("name", "Název")
            ->setRequired("Zadejte prosím název.");
        $form->addText("url", "URL")
            ->setRequired("Zadejte prosím URL feedu.");
        $form->addSelect("feed_group_id", "Skupina", $this->feedGroups->getPairs());
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = function($form, $vals) {
            $vals = $form->getValues();
            $this->feeds->save($vals);

            $this->flashMessage("Feed uložen");
            $this->redirect("detail", $vals['feed_group_id']);
        };
        return $form;
    }


    public function startup() {
        parent::startup();
        if (!$this->getUser()->isAllowed("feeds")) {
            $this->flashMessage("Nemáte oprávnění pro vstup do této sekce.");
            $this->redirect(":Homepage:");
        }
    }
    public function renderDefault() {
        $this->template->groups = $this->feedGroups->getAll();
    }

    public function renderDetail($id) {
        $group = $this->feedGroups->get($id);
        $this->template->group = $group;
        $this->template->feeds = $this->feeds->getAllByFeedGroupId($group->id);
    }

    public function actionEditGroup($id = null) {
        if (!empty($id)) {
            $group = $this->feedGroups->get($id);
        }
        if (!empty($group)) {
            $this['group']->setDefaults($group);
            $this->template->group = $group;
        }
    }

    public function actionEditFeed($feed_group_id) {
        $this['feed']['feed_group_id']->setDefaultValue($feed_group_id);
    }

    public function handleDeleteFeed($id, $confirmed=false) {
        if ($confirmed) {
            $this->feeds->remove($id);
            $this->flashMessage("Feed smazán.");
            $this->redirect("this");
        }
    }

}
