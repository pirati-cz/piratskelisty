<?php

namespace App\ServiceModule\Presenters;

class ThumbnailPresenter extends \App\Presenters\BasePresenter
{
    /** @var Models/Upload */
    private $upload;

    public function injectUpload(\Models\Upload $upload)
    {
        $this->upload = $upload;
    }

    public function renderDefault($id, $width = "", $height = "", $format = null, $crop = null)
    {
        if (!empty($width) && !preg_match('/^\d+$/', $width)) $this->terminate();
        if (!empty($height) && !preg_match('/^\d+$/', $height)) $this->terminate();
        if ($width > 2000 || $height > 2000) $this->reminate();
        if (!in_array($format, array("jpg", "jpeg", "png"))) $this->terminate();
        if (!empty($crop)) {$crop = true;} else {$crop = false;}

        $image = $this->upload->get($id);

        $filename = THUMBS_DIR;

        if (!empty($width)) {
            $filename .= "/w" . $width;
        }
        if (!empty($height)) {
            $filename .= "/h" . $height;
        }
        if ($crop) {
            $filename .= "/crop";
        }
        if (!file_exists($filename)) {
            mkdir($filename, 0755,true);
        }
        $filename .= "/" . $id . "." . $format;
        $inputfile = UPLOAD_DIR . "/" . $image['id'] . "." . $image["extension"];
        if (!file_exists($inputfile)) {
            throw new \Nette\Application\BadRequestException();
        }

        try {
            $img = \Nette\Image::fromFile($inputfile);
        } catch (\Nette\Utils\UnknownImageFileException $e) {
            throw new \Nette\Application\BadRequestException();
        }

        if (!empty($width) && !empty($height)) {
            if (!empty($crop)) {
                if ($img->getHeight() / $img->getWidth() > $height / $width) {
                    $img->resize($width, null);
                    $img->crop(0, ($img->getHeight() - $height) / 2, $width, $height);
                } else {
                    $img->resize(null, $height);
                    $img->crop(($img->getWidth() - $width) / 2, 0, $width, $height);
                }
            } else {
                $img->resize($width, $height);
            }
        } elseif (!empty($width)) {
            $img->resize($width, null);
        } elseif (!empty($height)) {
            $img->resize(null, $height);
        }

        $img->save($filename);

        $img->send();
        $this->terminate();

    }
}
