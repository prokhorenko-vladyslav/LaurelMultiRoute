<?php


namespace Laurel\MultiRoute\App\Traits;


/**
 * Trait, which change/return status of path.
 *
 * Trait Activetable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Activetable
{
    /**
     * Activates path
     *
     * @return void
     */
    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    /**
     * Deactivates path
     *
     * @return void
     */
    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Returns true, if path is deactivated
     *
     * @return bool
     */
    public function deactivated()
    {
        return !$this->is_active;
    }

    /**
     * Return true, if path is activated
     *
     * @return mixed
     */
    public function activated()
    {
        return $this->is_active;
    }

    /**
     * Changed status of the path
     *
     * @param bool $status
     */
    public function setIsActiveAttribute(bool $status)
    {
        $this->attributes['is_active'] = $status;
    }
}
