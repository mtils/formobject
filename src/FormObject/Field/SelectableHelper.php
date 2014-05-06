<?php namespace FormObject\Field;

use Collection\Map\Extractor;
use Collection\Iterator\CastableIterator;
use \Traversable;
use \DomainException;

class SelectableHelper{
    public static function createIterator($src, $field, $manualExtractor=NULL){
        $srcExtractor = $manualExtractor;
        $iteratorSrc = array();

        if($src instanceof Collection\Map){
            $iteratorSrc = $src->getSrc();
            if(!$srcExtractor){
                $srcExtractor = $src->getExtractor();
            }
        }
        elseif(is_array($src)){
            $iteratorSrc = $src;
            // Pseudo Check for numeric indexed array
            if(isset($src[0])){
                if(!$srcExtractor){
                    $srcExtractor = new Extractor(Extractor::VALUE,
                                                  Extractor::VALUE);
                }
            }
            else{
                if(!$srcExtractor){
                    $srcExtractor = new Extractor(Extractor::KEY,
                                                  Extractor::VALUE);
                }
            }
        }
        elseif($src instanceof Traversable){
            $iteratorSrc = $src;
        }
        else{
            throw new DomainException("Src is not traversable ".\gettype($src));
        }

        if(!$srcExtractor){
            throw new DomainException("No Extractor found for src ".\gettype($src));
        }
        $extractor = new SelectableExtractor($srcExtractor);
        $extractor->_setField($field);
        return new CastableIterator($iteratorSrc, $extractor);
        // if rest $extractor needed (id,title?)
    }
}
