<?php

namespace Larapress\Context;

class Context
{
    protected static $instance = null;
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function preGetPosts($wp_query)
    {
        if (1 || $wp_query->is_main_query()) {
            echo "<div style='float:right'>";
            var_dump($wp_query);
            echo "</div>";
        }
        return;
    }



}
