<?php
/**
 * Created by PhpStorm.
 * User: Programmer
 * Date: 8/20/2017
 * Time: 3:03 PM
 */

namespace AppBundle\Traits;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait ExceptionTrait
{
    /**
     * @param string $message
     * @throws ConflictHttpException
     */
    public function throwConflicException($message) {

        throw new ConflictHttpException($message);
    }

    /**
     * @param $message
     * @throws UnauthorizedHttpException
     */
    public function throwUnauthorizedException($message) {

        throw new UnauthorizedHttpException(null, $message);
    }
}
