<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sequence\User\Exception;


use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * AccessDeniedException is thrown when the account has not the required role.
 */
class AccessDeniedException extends AccessDeniedHttpException implements ExceptionInterface
{

}
