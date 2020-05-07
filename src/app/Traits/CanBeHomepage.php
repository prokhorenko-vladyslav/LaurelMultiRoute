<?php


namespace Laurel\MultiRoute\App\Traits;


use Laurel\MultiRoute\MultiRoute;

trait CanBeHomepage
{
    public function isHomepage()
    {
        return is_null($this->slug);
    }

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
            return $homepagePath->save();
        }
    }
}
