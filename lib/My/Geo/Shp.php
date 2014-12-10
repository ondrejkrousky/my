<?php

namespace My\Geo;

/**
 * Description of Shp
 *
 * @author Ondra
 */
class Shp extends \Nette\Object{
   
    private $handler;
    
    public function __construct($filename){
        if(!file_exists($filename)){
            throw new Exception("File does not exists.");
        }
        $this->handler = fopen($filename, 'rb');
    }
    
    private function readInt($bytes, $mode);
    
    
    
}
