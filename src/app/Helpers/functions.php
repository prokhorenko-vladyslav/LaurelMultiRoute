<?php
    if (!function_exists('homepage')) {
        function homepage() : ?\Laurel\MultiRoute\App\Models\Path
        {
            return \Laurel\MultiRoute\MultiRoute::getHomepage();
        }
    }

    if (!function_exists('pathForSlug')) {
        function pathForSlug(string $slug) : array
        {
            return \Laurel\MultiRoute\MultiRoute::pathForSlug($slug);
        }
    }

    if (!function_exists('pathForId')) {
        function pathForId(int $id) : array
        {
            return \Laurel\MultiRoute\MultiRoute::pathForId($id);
        }
    }

    if (!function_exists('uriForSlug')) {
        function uriForSlug(string $slug) : string
        {
            return \Laurel\MultiRoute\MultiRoute::uriForSlug($slug);
        }
    }

    if (!function_exists('uriForId')) {
        function uriForId(int $id) : string
        {
            return \Laurel\MultiRoute\MultiRoute::uriForId($id);
        }
    }
