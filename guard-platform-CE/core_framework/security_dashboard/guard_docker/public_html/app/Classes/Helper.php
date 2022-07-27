<?php


namespace App\Classes;


class Helper
{
    public static function sy_arrayExpand($arr, $htmlEncode = false, $arrLayer = 1) {
        if(!is_object($arr) && !is_array($arr)) return $arr;//not an array/object...

        $equalGreaterThan = ($htmlEncode)?' =&gt; ' : " => ";
        $ln = $htmlEncode?"<br/>":"\n";
        $out = is_object($arr)?"Object (\n":"Array (\n";
        $baseLayer = $arrLayer;
        foreach($arr as $k => $v) {
            $out .= self::sy_space(($baseLayer) * 4).self::sy_quoteStr($k) . $equalGreaterThan;
            if(is_array($v) || is_object($v)) {
                //for the while $v is an array do the whole thing recursively!
                $arrLayer++;
//                $curFunc = __FUNCTION__;
                $out .= self::sy_arrayExpand($v, $htmlEncode, $arrLayer);
//                $out .= $curFunc($v, $htmlEncode, $arrLayer);
            } else {
                //value is neither array or object... easy way out!
                $out .= self::sy_quoteStr($v);
            }

            if(end($arr) === $v) {
                $lineEnd = $ln;// last element of array... no comma needed
            } else {
                $lineEnd = ",$ln";
            }
            $out .= $lineEnd;
        }

        $closingSpacesNum = (($baseLayer* 4) - 4);
        $out .= self::sy_space($closingSpacesNum).")";
        return $out;
    }

    public static function sy_space($n = 1, $html = false){
        $out = "";
        $sp = $html?'&nbsp;':" ";
        while($n>1) {
            $out .= $sp;
            $n--;
        }
        return $out;
    }

    public static function sy_quoteStr($str) {
        return "\"$str\"";
    }
}