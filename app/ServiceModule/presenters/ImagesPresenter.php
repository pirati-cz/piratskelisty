<?php
namespace App\ServiceModule\Presenters;

class ImagesPresenter extends \App\Presenters\BasePresenter {

    /** @var Models/Upload */
    private $upload;

    public function injectUpload(\Models\Upload $upload)
    {
        $this->upload = $upload;
    }

    public function renderJson() {
        $images = $this->upload->getImages();
        $arr = array();
        foreach ($images as $image) {
            $arr[] = array(
                "thumb"=>  $this->link("//:Service:Thumbnail:default", array('id' => $image->id, 'width' => 100, 'height' => 100, 'crop' => "crop", 'format' => "png")),
                "image"=>  $this->link("//:Service:Thumbnail:default", array('id' => $image->id, 'width' => 600,'format' => "jpg")),
                //"folder" => $image['folder'],
                "title" =>  $image['title'],
                "alt" => $image['alt']
                );
        }
        
        $this->sendResponse(new \Nette\Application\Responses\JsonResponse($arr));
    }

}