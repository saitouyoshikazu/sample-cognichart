<?php

namespace App\Domain\AbstractArtistMusic\Strategy;

class SymbolHandler
{

    public function removeSymbol(string $str)
    {
        $str = preg_replace("/[!-\/:-@≠\\\\[-`{-~]/iu", '', $str);
        return preg_replace("/  /iu", " ", $str);
    }

    public function isAlphaNumberSymbol(string $value) {
        if (preg_match("/\A[!-\~]+\z/", $value)) {
            return true;
        }
        return false;
    }

}
