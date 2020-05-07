<?php

namespace Laurel\MultiRoute\App\Traits;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait Throwable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Throwable
{
    /**
     * @param string $id
     * @throws Exception
     */
    protected static function throwPathNotFoundException(string $id)
    {
        throw new Exception("Path `{$id}` has not been found");
    }

    /**
     * @param string $id
     * @throws NotFoundHttpException
     */
    protected static function throw404Exception(string $id)
    {
        throw new NotFoundHttpException("Path `{$id}` has not been found");
    }

    /**
     *
     * @throws Exception
     */
    protected static function throwIncorrectCallbackException()
    {
        throw new Exception('Path callback is incorrect');
    }

    /**
     * @param string $slug
     * @throws Exception
     */
    protected static function throwPathAlreadyExistsException(string $slug)
    {
        throw new Exception("Path with slug `{$slug}` already exists");
    }
}
