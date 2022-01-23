<?php
if ( function_exists('register_sidebar') )
    register_sidebars(1,array(
        'before_widget' => '<div id="archive">',
    'after_widget' => '</div>',
            'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
?>