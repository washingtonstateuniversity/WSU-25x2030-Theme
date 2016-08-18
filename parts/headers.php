<header class="desktop-header">
	<div>
	<?php
		$site_nav_args = array(
			'theme_location'  => 'site',
			'menu'            => 'site',
			'container'       => false,
			'container_class' => false,
			'container_id'    => false,
			'menu_class'      => 'site-menu',
			'menu_id'         => null,
			'items_wrap'      => '<ul class="%2$s">%3$s</ul>',
			'depth'           => 1,
		);
		wp_nav_menu( $site_nav_args );
	?>
	</div>
</header>
