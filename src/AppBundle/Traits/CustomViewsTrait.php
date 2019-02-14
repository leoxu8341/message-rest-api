<?php
/**
 * Created by PhpStorm.
 * User: Programmer
 * Date: 8/20/2017
 * Time: 3:03 PM
 */

namespace AppBundle\Traits;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

trait CustomViewsTrait
{
    /**
     * @param $item
     * @return View
     */
    public function getCreatedView(
        $item
    ) {
        return View::create($item, Response::HTTP_CREATED);
    }

    /**
     * @param $item
     * @return View
     */
    public function getView($item)
    {
        return View::create($item, Response::HTTP_OK);
    }

    /**
     * @return View
     */
    public function getDeletedView() {
        return View::create(null,Response::HTTP_OK);
    }
}
