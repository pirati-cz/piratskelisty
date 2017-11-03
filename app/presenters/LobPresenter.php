<?php

namespace App\Presenters;

use Nette,
    App\Model;


/**
 * Lob presenter.
 */
class LobPresenter extends BasePresenter
{
    /** @var  \Models\Rss */
    private $rss;

	public function injectRss(\Models\Rss $rss)
    {
        $this->rss = $rss;
    }


    public function renderDefault($limit = 10,$offset=0, $format = 'html')
    {
       $lobs = $this->rss->getLobs($limit, $offset);
       if ($format=='json') {
           $lobs2 = array();
           foreach ($lobs as $lob) {
               $lobs2[] = array(
                   "id" => $lob->id,
                   "content" => $lob->content,
                   "published" => $lob->published,
                   "author" => $lob->author,
                   "source" => "https://forum.pirati.cz/post".$lob->post_id.".html");
           }
           $this->getHttpResponse()->addHeader("Access-Control-Allow-Origin","*");
           $this->sendResponse(new Nette\Application\Responses\JsonResponse($lobs2, "text/json"));
       }
       
        $this->template->lobs = $lobs;
		$this->template->next_offset = $offset + $limit;
		$this->template->limit = $limit;
		$this->payload->append = 'snippet--lobs';

        $this->redrawControl("dalsi");
        $this->redrawControl("lobs");
	}


}

