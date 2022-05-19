<?php

class Hash {
    public static function getJointHashMap($array, ...$keys) {
        $hashmap = array();
        foreach($array as $item) {
            $jointKey = '';
            foreach($keys as $key) {
                $jointKey .= $item->$key;
            }
            $hashmap[$jointKey] = $item;
        }

        return $hashmap;
    }

    public static function getHashMap($array, $key) {
        $map = array();
        foreach($array as $item) {
            $map[$item->$key] = $item;
        }
        return $map;
    }
}