<?php
    if (!function_exists('homepage')) {
        /**
         * Helper for loading homepage
         *
         * @return \Laurel\MultiRoute\App\Models\Path|null
         */
        function homepage() : ?\Laurel\MultiRoute\App\Models\Path
        {
            return \Laurel\MultiRoute\MultiRoute::getHomepage();
        }
    }

    if (!function_exists('pathForSlug')) {
        /**
         * Helper for creating path chain using slug
         *
         * @param string $slug
         * @return array
         */
        function pathForSlug(string $slug) : array
        {
            return \Laurel\MultiRoute\MultiRoute::pathForSlug($slug);
        }
    }

    if (!function_exists('pathForId')) {
        /**
         * Helper for creating path chain using id
         *
         * @param int $id
         * @return array
         */
        function pathForId(int $id) : array
        {
            return \Laurel\MultiRoute\MultiRoute::pathForId($id);
        }
    }

    if (!function_exists('uriForSlug')) {
        /**
         * Helper for creating path uri using slug
         *
         * @param string $slug
         * @return string
         */
        function uriForSlug(string $slug) : string
        {
            return \Laurel\MultiRoute\MultiRoute::uriForSlug($slug);
        }
    }

    if (!function_exists('uriForId')) {
        /**
         * Helper for creating path uri using id
         *
         * @param int $id
         * @return string
         */
        function uriForId(int $id) : string
        {
            return \Laurel\MultiRoute\MultiRoute::uriForId($id);
        }
    }
