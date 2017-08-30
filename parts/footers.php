<footer class="site-footer">

	<section>

		<a href="https://wsu.edu/">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/wsu-logo.png' ); ?>" width="198" height="55" alt="Washington State University" />
		</a>

		<div>
			<p><?php echo esc_html( spine_get_option( 'contact_streetAddress' ) ) . ' ' . esc_attr( spine_get_option( 'contact_addressLocality' ) ) . ' ' . esc_attr( spine_get_option( 'contact_postalCode' ) ); ?> <span>|</span> <?php echo esc_attr( spine_get_option( 'contact_telephone' ) ); ?></p>
			<p>Washington State University <span>|</span>
				<a href="https://access.wsu.edu/">Access</a>
				<a href="https://policies.wsu.edu/">Policies</a>
				<a href="http://public.wsu.edu/~forms/ProposedWAC.html">Rule Making</a>
				<a href="https://copyright.wsu.edu/">© Copyright</a>
			</p>
		</div>

		<?php if ( spine_social_options() ) { ?>
		<ul>
			<?php foreach ( spine_social_options() as $socialite => $social_url ) { ?>
			<li class="<?php echo esc_attr( $socialite ); ?>-channel">
				<a href="<?php echo esc_url( $social_url ); ?>"><?php echo esc_html( $socialite ); ?></a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>

	</section>

</footer>
