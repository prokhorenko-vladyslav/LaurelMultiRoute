<?php


namespace Laurel\MultiRoute\App\Traits;


/**
 * Trait Activetable
 * @package Laurel\MultiRoute\App\Traits
 */
trait Activetable
{
    /**
     *
     */
    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    /**
     *
     */
    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * @return bool
     */
    public function deactivated()
    {
        return !$this->is_active;
    }

    /**
     * @return mixed
     */
    public function activated()
    {
        return $this->is_active;
    }

    /**
     * @param bool $status
     */
    public function setIsActiveAttribute(bool $status)
    {
        $this->attributes['is_active'] = $status;
    }
}
