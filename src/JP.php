<?php
namespace {
    class JP
    {

    }

    function cd()
    {
        \Kint::dump(func_get_args());
    }

    function cs()
    {
        echo '<pre>';
        print_r(func_get_args());
        echo '<pre>';
    }
}