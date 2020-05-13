<?php


namespace Laurel\MultiRoute\App\Traits;


/**
 * Trait for middleware manipulating
 *
 * Trait HasMiddleware
 * @package Laurel\MultiRoute\App\Traits
 */
trait HasMiddleware
{
    /**
     * Returns array with all middlewares
     *
     * @return array
     */
    public function middleware() : array
    {
        return is_array($this->middleware) ? $this->middleware : [$this->middleware];
    }

    /**
     * Add new middleware
     *
     * @param $middleware
     * @return $this
     */
    public function addMiddleware($middleware)
    {
        $this->middleware[] = $middleware;
        return $this;
    }
}
