<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sealsix\MediaBundle;

/**
 * Description of MediaArray
 *
 * @author Aurélien
 */
class MediaArray implements \ArrayAccess{
    private $data = [];
    
    public function getData() {
        return $this->data;
    }
    
    public function &__get ($key) {
        return $this->data[$key];
    }
    
    public function __set($key,$value) {
        $this->data[$key] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->data[serialize($offset)]);
    }

    public function offsetGet($offset) {
        return $this->offsetExists(serialize($offset)) ? $this->data[serialize($offset)] : null;
    }

    public function offsetSet($offset, $value) {
        if(is_null($offset)) {
            $this->data[] = $value;
        } elseif ($this->offsetExists($offset)) {
            $this->data[serialize($offset)] = $this->data[serialize($offset)] + 1;
        } else {
            $this->data[serialize($offset)] = $value;
        }
    }

    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->data[serialize($offset)]);
        }
    }
}
