<?php

function &array_get_or_add($needle, array &$haystack, $default=null) {
    if (key_exists($needle, $haystack)) {
        return $haystack[$needle];
    } else {
        $haystack[$needle] = $default;
        return $haystack[$needle];
    }
}
