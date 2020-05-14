<?php


namespace Laurel\MultiRoute\App\Traits;


/**
 * Trait for using prefixes
 *
 * Trait HasPrefix
 * @package Laurel\MultiRoute\App\Traits
 */
trait HasPrefix
{
    /**
     * Returns path prefix
     *
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets path prefix
     *
     * @param string $prefix
     */
    public function setPrefixAttribute(string $prefix)
    {
        $this->attributes['prefix'] = $prefix;
    }
}
