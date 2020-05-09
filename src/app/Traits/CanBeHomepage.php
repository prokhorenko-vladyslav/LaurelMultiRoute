<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\MultiRoute;

/**
 * Trait for homepage manipulating
 *
 * Trait CanBeHomepage
 * @package Laurel\MultiRoute\App\Traits
 */
trait CanBeHomepage
{
    /**
     * Returns true, if paths is homepage
     *
     * @return bool
     */
    public function isHomepage()
    {
        return is_null($this->slug);
    }

    /**
     * Sets path as homepage
     *
     * @param bool $deleteOld
     * @return bool
     */
    public function makeHomepage(bool $deleteOld = false)
    {
        if ($this->isHomepage()) {
            return true;
        }

        if ($this->forgetHomepage($deleteOld)) {
            $this->attributes["slug"] = null;
            $this->attributes["parent_id"] = null;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * If @deleteOld is true, method deletes path of the old homepage from DB.
     * If @deletedOld is false, path slug of the old homepage changes to 'homepage-deleted-at-' . time() and it will be deactivated.
     *
     * @param bool $deleteOld
     * @return bool
     */
    public function forgetHomepage(bool $deleteOld = false)
    {
        $homepagePath = MultiRoute::getHomepage();
        if (!$homepagePath) {
            return true;
        }

        if ($deleteOld) {
            return $homepagePath->delete();
        } else {
            $homepagePath->slug = 'homepage-deleted-at-' . time();
            $homepagePath->deactivate();
            return $homepagePath->save();
        }
    }
}
