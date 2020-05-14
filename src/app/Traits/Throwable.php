<?php

namespace Laurel\MultiRoute\App\Traits;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait with package exceptions
 *
 * Trait Throwable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Throwable
{
    /**
     * Exception, which will throw, when path has not been found
     *
     * @param string $id
     * @throws Exception
     */
    protected static function throwPathNotFoundException(?string $id)
    {
        throw new Exception("Path `{$id}` has not been found");
    }

    /**
     * Exception, which will throw, when callback for uri has not been found
     *
     * @param string $id
     * @throws NotFoundHttpException
     */
    protected static function throw404Exception(?string $id)
    {
        throw new NotFoundHttpException("Path `{$id}` has not been found");
    }

    /**
     * Exception, which will throw, when path is not active
     *
     * @param string $id
     * @throws NotFoundHttpException
     */
    protected static function throwPathNotActiveException(?string $id)
    {
        throw new NotFoundHttpException("Path `{$id}` is not active");
    }

    /**
     * Exception, which will throw, when path callback is invalid
     *
     * @throws Exception
     */
    protected static function throwIncorrectCallbackException()
    {
        throw new Exception('Path callback is incorrect');
    }

    /**
     * Exception, which will throw, when path with save slug already exists
     *
     * @param string $slug
     * @throws Exception
     */
    protected static function throwPathAlreadyExistsException(?string $slug)
    {
        throw new Exception("Path with slug `{$slug}` already exists");
    }

    protected static function throwIncorrectPrefixException()
    {
        throw new Exception("Path and its parent has different prefix");
    }
}
