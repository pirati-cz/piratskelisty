<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();

        $router[] = new Route('api/images', array("module" => "Service", "presenter" => "Images", "action" => "json"));
            $router[] = new Route('upload/thumbs[/w<width [0-9]+>][/h<height [0-9]+>][/<crop crop>]/<id [0-9]+>.<format>',
                array('module' => 'Service',
                    'presenter' => 'Thumbnail',
                    'action' => 'default'));
        $router[] = new Route('art/<id>', "Homepage:stary");
        $router[] = new Route('hledani/<search>', "Homepage:default");

		$router[] = new Route('rss/forum/<key>', "Rss:forum");
		$router[] = new Route('rss/<action>', "Rss:default");

        $router[] = new Route('kontakt', "Homepage:kontakt");
        $router[] = new Route('podpora', "Homepage:podpora");
        $router[] = new Route('redakce', "Homepage:redakce");
        $router[] = new Route('kategorie/<url>', "Homepage:default");
        $router[] = new Route('stitek/<stitek>', "Homepage:default");
        $router[] = new Route('clanek-<id>-<nazev>', "Clanek:default");
        $router[] = new Route('evidence-lobbistickych-kontaktu', "Lob:default");

        $router[] = new Route('sprava/<presenter>/<action>[/<id>]',
            array("module" => "Redakce",
                  "presenter" => "Spravce",
                  "action" => "default" ));

	    $router[] = new Route('fbfeed',array("presenter" => "FB", "action" => "default"));
	    $router[] = new Route('fbfeed/kraje',array("presenter" => "FB", "action" => "kraje"));

		$router[] = new Route('<presenter>/<action>[/<id>]', "Homepage:default");

		return $router;
	}

}
