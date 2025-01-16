<?php

$attributes = shortcode_atts(
    [
        'menu' => 0,
		'layout' => 'horizontal',
		'editMode' => false,
    ],
    $attributes
);

$wrapper_attributes = get_block_wrapper_attributes( [
	'data-menu' => $attributes['menu'],
	'data-layout' => $attributes['layout'],
] );

// printf( '<div %s>%s</div>',

// 	$wrapper_attributes,
// 	wp_nav_menu( [
// 		'menu' => $attributes['menu'],
// 		'container' => false,
// 		'fallback_cb' => false,
// 		'before' => '<span class="menu-item-text" itemprop="name">',
// 		'after' => '</span>',
// 		'generator' => 'simple-menu-block',
// 	] )
// );

?>
<div <?php echo $wrapper_attributes; ?> >
    <?php
        wp_nav_menu(
            [
                'menu' => $attributes['menu'],
                'container' => false,
                'fallback_cb' => false,
                'before' => '<span class="menu-item-text" itemprop="name">',
                'after' => '</span>',
                'generator' => 'simple-menu-block',
				'block_attributes' => $attributes,
				'in_editor' => $attributes['editMode'],
            ]
        );
    ?>
</div>