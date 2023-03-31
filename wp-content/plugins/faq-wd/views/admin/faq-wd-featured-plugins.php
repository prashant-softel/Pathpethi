<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ecwd_settings;
global $ecwd_tabs;

?>

<div class="wrap">
	<?php settings_errors(); ?>
	<div id="ecwd-settings">

		<div id="ecwd-settings-content">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<div id="main_featured_plugins_page">
				<form method="post">
					<ul id="featured-plugins-list">
						<li class="photo-gallery ">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Photo Gallery</strong>
							</div>
							<div class="description">
								<p>Photo Gallery is a fully responsive WordPress Gallery plugin with advanced functionality. </p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-photo-gallery-plugin.html" class="download">Download</a>
						</li>
						<li class="form-maker">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Form Maker</strong>
							</div>
							<div class="description">
								<p>Form Maker is a modern and advanced tool for creating WordPress forms easily and fast.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-form.html" class="download">Download</a>
						</li>
						<li class="catalog">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Spider Catalog</strong>
							</div>
							<div class="description">
								<p>Spider Catalog for WordPress is a convenient tool for organizing the products represented on your website into catalogs.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-catalog.html" class="download">Download</a>
						</li>
						<li class="slider_wd">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Slider WD</strong>
							</div>
							<div class="description">
								<p>Create responsive, highly configurable sliders with various effects for your WordPress site. </p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-slider-plugin.html" class="download">Download</a>
						</li>
						<li class="player">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Video Player</strong>
							</div>
							<div class="description">
								<p>Spider Video Player for WordPress is a Flash & HTML5 video player plugin that allows you to easily add videos to your website with the possibility</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-player.html" class="download">Download</a>
						</li>
						<li class="contacts">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Spider Contacts</strong>
							</div>
							<div class="description">
								<p>Spider Contacts helps you to display information about the group of people more intelligible, effective and convenient.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-contacts-plugin.html" class="download">Download</a>
						</li>
						<li class="facebook">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Spider Facebook</strong>
							</div>
							<div class="description">
								<p>Spider Facebook is a WordPress integration tool for Facebook.It includes all the available Facebook social plugins and widgets.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-facebook.html" class="download">Download</a>
						</li>
						<li class="faq">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Spider FAQ</strong>
							</div>
							<div class="description">
								<p>The Spider FAQ WordPress plugin is for creating an FAQ (Frequently Asked Questions) section for your website.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-faq-plugin.html" class="download">Download</a>
						</li>
						<li class="zoom">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Zoom</strong>
							</div>
							<div class="description">
								<p>Zoom enables site users to resize the predefined areas of the web site.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-zoom.html" class="download">Download</a>
						</li>
						<li class="flash-calendar">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Flash Calendar</strong>
							</div>
							<div class="description">
								<p>Spider Flash Calendar is a highly configurable Flash calendar plugin which allows you to have multiple organized events.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-events-calendar.html" class="download">Download</a>
						</li>
						<li class="contact-maker">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Contact Form Maker</strong>
							</div>
							<div class="description">
								<p>WordPress Contact Form Maker is an advanced and easy-to-use tool for creating forms.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-contact-form-maker-plugin.html" class="download">Download</a>
						</li>
						<li class="twitter-widget">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Widget Twitter</strong>
							</div>
							<div class="description">
								<p>The Widget Twitter plugin lets you to fully integrate your WordPress site with your Twitter account.</p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-twitter-integration-plugin.html" class="download">Download</a>
						</li>
						<li class="contact_form_bulder">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Contact Form Builder</strong>
							</div>
							<div class="description">
								<p>Contact Form Builder is the best tool for quickly arranging a contact form for your clients and visitors. </p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-contact-form-builder.html" class="download">Download</a>
						</li>
						<li class="folder_menu">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Folder Menu</strong>
							</div>
							<div class="description">
								<p>Folder Menu Vertical is a WordPress Flash menu module for your website, designed to meet your needs and preferences. </p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-menu-vertical.html" class="download">Download</a>
						</li>
						<li class="random_post">
							<div class="product"></div>
							<div class="title">
								<strong class="heading">Random post</strong>
							</div>
							<div class="description">
								<p>Spider Random Post is a small but very smart solution for your WordPress web site. </p>
							</div>
							<a target="_blank" href="https://web-dorado.com/products/wordpress-random-post.html" class="download">Download</a>
						</li>
					</ul>
				</form>
			</div>
		</div>
		<!-- #ecwd-settings-content -->
	</div>
	<!-- #ecwd-settings -->
</div><!-- .wrap -->