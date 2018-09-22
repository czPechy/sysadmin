<?php
namespace ProfiCloS\Monitor\Tools;

class Numbers
{

    public static function float($input) {
        return (float) str_replace(',', '.', $input);
    }

}