<?php
/**
 * Admin Live Customizer View.
 *
 * @package StylerForWooCommerceBookings
 *
 * Available variables:
 * @var array $settings Current saved settings.
 * @var array $presets  Theme presets array.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wcbs-customizer-wrap">
	<!-- Customizer Header -->
	<header class="wcbs-admin-header">
		<div class="wcbs-header-brand">
			<div class="wcbs-header-logo">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
					<line x1="16" y1="2" x2="16" y2="6"></line>
					<line x1="8" y1="2" x2="8" y2="6"></line>
					<line x1="3" y1="10" x2="21" y2="10"></line>
				</svg>
			</div>
			<div>
				<h1 class="wcbs-header-title"><?php esc_html_e( 'Styler for WooCommerce Bookings', 'styler-for-woo' ); ?></h1>
				<span class="wcbs-header-badge"><?php esc_html_e( 'Visual Customizer v1.0.0', 'styler-for-woo' ); ?></span>
			</div>
		</div>

		<!-- Device Preview Switcher -->
		<div class="wcbs-device-switcher" role="group" aria-label="<?php esc_attr_e( 'Device Preview', 'styler-for-woo' ); ?>">
			<button type="button" class="wcbs-device-btn active" data-device="desktop" title="<?php esc_attr_e( 'Desktop View', 'styler-for-woo' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
			</button>
			<button type="button" class="wcbs-device-btn" data-device="tablet" title="<?php esc_attr_e( 'Tablet View', 'styler-for-woo' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
			</button>
			<button type="button" class="wcbs-device-btn" data-device="mobile" title="<?php esc_attr_e( 'Mobile View', 'styler-for-woo' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
			</button>
		</div>

		<!-- Actions -->
		<div class="wcbs-header-actions">
			<button type="button" id="wcbs-reset-btn" class="button">
				<?php esc_html_e( 'Reset to Defaults', 'styler-for-woo' ); ?>
			</button>
			<button type="button" id="wcbs-save-btn" class="button button-primary button-hero">
				<span class="wcbs-btn-spinner dashicons dashicons-update spin" style="display:none;"></span>
				<span class="wcbs-btn-text"><?php esc_html_e( 'Save Changes', 'styler-for-woo' ); ?></span>
			</button>
		</div>
	</header>

	<div class="wcbs-customizer-body">
		<!-- Sidebar Controls -->
		<aside class="wcbs-sidebar">
			<nav class="wcbs-nav-tabs">
				<button type="button" class="wcbs-tab-link active" data-tab="tab-layout">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
					<span><?php esc_html_e( 'Layouts', 'styler-for-woo' ); ?></span>
				</button>
				<button type="button" class="wcbs-tab-link" data-tab="tab-presets">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>
					<span><?php esc_html_e( 'Themes', 'styler-for-woo' ); ?></span>
				</button>
				<button type="button" class="wcbs-tab-link" data-tab="tab-colors">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C22 6.5 17.5 2 12 2Z"/></svg>
					<span><?php esc_html_e( 'Colors', 'styler-for-woo' ); ?></span>
				</button>
				<button type="button" class="wcbs-tab-link" data-tab="tab-typography">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
					<span><?php esc_html_e( 'Styles & Shapes', 'styler-for-woo' ); ?></span>
				</button>
				<button type="button" class="wcbs-tab-link" data-tab="tab-features">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<span><?php esc_html_e( 'Features & UX', 'styler-for-woo' ); ?></span>
				</button>
			</nav>

			<form id="wcbs-customizer-form" class="wcbs-tab-content-container">

				<!-- TAB 1: LAYOUTS -->
				<div class="wcbs-tab-pane active" id="tab-layout">
					<h3 class="wcbs-pane-heading"><?php esc_html_e( 'Booking Layout Flow', 'styler-for-woo' ); ?></h3>
					<p class="wcbs-pane-desc"><?php esc_html_e( 'Choose how your booking form renders on single product pages.', 'styler-for-woo' ); ?></p>

					<div class="wcbs-layout-options-grid">
						<label class="wcbs-layout-card <?php echo 'split' === $settings['default_layout'] ? 'active' : ''; ?>">
							<input type="radio" name="default_layout" value="split" <?php checked( $settings['default_layout'], 'split' ); ?>>
							<div class="wcbs-layout-card-inner">
								<div class="wcbs-layout-preview-icon split-icon">
									<div class="col-left"></div>
									<div class="col-right"></div>
								</div>
								<strong class="wcbs-card-title"><?php esc_html_e( 'Two-Column Split', 'styler-for-woo' ); ?></strong>
								<span class="wcbs-card-caption"><?php esc_html_e( 'Calendar on left, slots & live summary on right (Calendly style).', 'styler-for-woo' ); ?></span>
							</div>
						</label>

						<label class="wcbs-layout-card <?php echo 'wizard' === $settings['default_layout'] ? 'active' : ''; ?>">
							<input type="radio" name="default_layout" value="wizard" <?php checked( $settings['default_layout'], 'wizard' ); ?>>
							<div class="wcbs-layout-card-inner">
								<div class="wcbs-layout-preview-icon wizard-icon">
									<div class="dots"><span class="active"></span><span></span><span></span></div>
									<div class="step-card"></div>
								</div>
								<strong class="wcbs-card-title"><?php esc_html_e( 'Multi-Step Wizard', 'styler-for-woo' ); ?></strong>
								<span class="wcbs-card-caption"><?php esc_html_e( 'Guided step-by-step booking journey with progress bar.', 'styler-for-woo' ); ?></span>
							</div>
						</label>

						<label class="wcbs-layout-card <?php echo 'drawer' === $settings['default_layout'] ? 'active' : ''; ?>">
							<input type="radio" name="default_layout" value="drawer" <?php checked( $settings['default_layout'], 'drawer' ); ?>>
							<div class="wcbs-layout-card-inner">
								<div class="wcbs-layout-preview-icon drawer-icon">
									<div class="side-panel"></div>
								</div>
								<strong class="wcbs-card-title"><?php esc_html_e( 'Slide-Over Drawer', 'styler-for-woo' ); ?></strong>
								<span class="wcbs-card-caption"><?php esc_html_e( 'Opens in an off-canvas sliding panel from the right.', 'styler-for-woo' ); ?></span>
							</div>
						</label>

						<label class="wcbs-layout-card <?php echo 'modal' === $settings['default_layout'] ? 'active' : ''; ?>">
							<input type="radio" name="default_layout" value="modal" <?php checked( $settings['default_layout'], 'modal' ); ?>>
							<div class="wcbs-layout-card-inner">
								<div class="wcbs-layout-preview-icon modal-icon">
									<div class="center-box"></div>
								</div>
								<strong class="wcbs-card-title"><?php esc_html_e( 'Popup Lightbox', 'styler-for-woo' ); ?></strong>
								<span class="wcbs-card-caption"><?php esc_html_e( 'Keeps product page clean; opens in a modern popup modal.', 'styler-for-woo' ); ?></span>
							</div>
						</label>

						<label class="wcbs-layout-card <?php echo 'bottom-sheet' === $settings['default_layout'] ? 'active' : ''; ?>">
							<input type="radio" name="default_layout" value="bottom-sheet" <?php checked( $settings['default_layout'], 'bottom-sheet' ); ?>>
							<div class="wcbs-layout-card-inner">
								<div class="wcbs-layout-preview-icon sheet-icon">
									<div class="bottom-panel"></div>
								</div>
								<strong class="wcbs-card-title"><?php esc_html_e( 'Mobile Bottom Sheet', 'styler-for-woo' ); ?></strong>
								<span class="wcbs-card-caption"><?php esc_html_e( 'App-like bottom drawer optimized for mobile touch.', 'styler-for-woo' ); ?></span>
							</div>
						</label>

						<label class="wcbs-layout-card <?php echo 'standard' === $settings['default_layout'] ? 'active' : ''; ?>">
							<input type="radio" name="default_layout" value="standard" <?php checked( $settings['default_layout'], 'standard' ); ?>>
							<div class="wcbs-layout-card-inner">
								<div class="wcbs-layout-preview-icon standard-icon">
									<div class="block"></div>
								</div>
								<strong class="wcbs-card-title"><?php esc_html_e( 'Modern Standard', 'styler-for-woo' ); ?></strong>
								<span class="wcbs-card-caption"><?php esc_html_e( 'Refined, responsive single-column layout.', 'styler-for-woo' ); ?></span>
							</div>
						</label>
					</div>

					<div class="wcbs-form-group" style="margin-top: 20px;">
						<label for="drawer_button_text"><?php esc_html_e( 'Drawer / Modal Trigger Button Text', 'styler-for-woo' ); ?></label>
						<input type="text" id="drawer_button_text" name="drawer_button_text" value="<?php echo esc_attr( $settings['drawer_button_text'] ); ?>" class="regular-text">
					</div>
				</div>

				<!-- TAB 2: THEMES -->
				<div class="wcbs-tab-pane" id="tab-presets">
					<h3 class="wcbs-pane-heading"><?php esc_html_e( 'Pre-Designed Themes', 'styler-for-woo' ); ?></h3>
					<p class="wcbs-pane-desc"><?php esc_html_e( 'Select a pre-designed theme to instantly transform your booking styles.', 'styler-for-woo' ); ?></p>

					<div class="wcbs-presets-grid">
						<label class="wcbs-preset-card <?php echo 'clean' === $settings['active_theme'] ? 'active' : ''; ?>">
							<input type="radio" name="active_theme" value="clean" <?php checked( $settings['active_theme'], 'clean' ); ?>>
							<div class="wcbs-preset-inner">
								<div class="wcbs-preset-palette">
									<span style="background: #4f46e5;"></span>
									<span style="background: #06b6d4;"></span>
									<span style="background: #ffffff; border: 1px solid #e2e8f0;"></span>
								</div>
								<h4><?php esc_html_e( 'Clean Minimalist', 'styler-for-woo' ); ?></h4>
								<p><?php esc_html_e( 'Modern indigo with clean white calendar and subtle borders.', 'styler-for-woo' ); ?></p>
							</div>
						</label>

						<label class="wcbs-preset-card <?php echo 'dark' === $settings['active_theme'] ? 'active' : ''; ?>">
							<input type="radio" name="active_theme" value="dark" <?php checked( $settings['active_theme'], 'dark' ); ?>>
							<div class="wcbs-preset-inner">
								<div class="wcbs-preset-palette">
									<span style="background: #6366f1;"></span>
									<span style="background: #38bdf8;"></span>
									<span style="background: #0f172a;"></span>
								</div>
								<h4><?php esc_html_e( 'Modern Dark', 'styler-for-woo' ); ?></h4>
								<p><?php esc_html_e( 'High contrast dark calendar with glowing accent colors.', 'styler-for-woo' ); ?></p>
							</div>
						</label>

						<label class="wcbs-preset-card <?php echo 'luxury' === $settings['active_theme'] ? 'active' : ''; ?>">
							<input type="radio" name="active_theme" value="luxury" <?php checked( $settings['active_theme'], 'luxury' ); ?>>
							<div class="wcbs-preset-inner">
								<div class="wcbs-preset-palette">
									<span style="background: #b45309;"></span>
									<span style="background: #d97706;"></span>
									<span style="background: #fdfbf7; border: 1px solid #fde68a;"></span>
								</div>
								<h4><?php esc_html_e( 'Luxury & Spa', 'styler-for-woo' ); ?></h4>
								<p><?php esc_html_e( 'Warm bronze, gold tones, and organic cream surfaces.', 'styler-for-woo' ); ?></p>
							</div>
						</label>

						<label class="wcbs-preset-card <?php echo 'vibrant' === $settings['active_theme'] ? 'active' : ''; ?>">
							<input type="radio" name="active_theme" value="vibrant" <?php checked( $settings['active_theme'], 'vibrant' ); ?>>
							<div class="wcbs-preset-inner">
								<div class="wcbs-preset-palette">
									<span style="background: #ec4899;"></span>
									<span style="background: #8b5cf6;"></span>
									<span style="background: #fdf2f8;"></span>
								</div>
								<h4><?php esc_html_e( 'Vibrant Modern', 'styler-for-woo' ); ?></h4>
								<p><?php esc_html_e( 'Bold magenta accents and playful rounded pill buttons.', 'styler-for-woo' ); ?></p>
							</div>
						</label>

						<label class="wcbs-preset-card <?php echo 'custom' === $settings['active_theme'] ? 'active' : ''; ?>">
							<input type="radio" name="active_theme" value="custom" <?php checked( $settings['active_theme'], 'custom' ); ?>>
							<div class="wcbs-preset-inner">
								<div class="wcbs-preset-palette">
									<span style="background: linear-gradient(135deg, red, blue, green);"></span>
								</div>
								<h4><?php esc_html_e( 'Custom Palette', 'styler-for-woo' ); ?></h4>
								<p><?php esc_html_e( 'Fine-tune individual colors in the Colors tab.', 'styler-for-woo' ); ?></p>
							</div>
						</label>
					</div>
				</div>

				<!-- TAB 3: COLORS -->
				<div class="wcbs-tab-pane" id="tab-colors">
					<h3 class="wcbs-pane-heading"><?php esc_html_e( 'Custom Color Controls', 'styler-for-woo' ); ?></h3>

					<div class="wcbs-color-section">
						<h4><?php esc_html_e( 'Brand & Action Colors', 'styler-for-woo' ); ?></h4>
						<div class="wcbs-color-row">
							<label for="primary_color"><?php esc_html_e( 'Primary Brand Color', 'styler-for-woo' ); ?></label>
							<input type="text" id="primary_color" name="primary_color" value="<?php echo esc_attr( $settings['primary_color'] ); ?>" class="wcbs-color-picker">
						</div>
						<div class="wcbs-color-row">
							<label for="primary_hover"><?php esc_html_e( 'Primary Hover Color', 'styler-for-woo' ); ?></label>
							<input type="text" id="primary_hover" name="primary_hover" value="<?php echo esc_attr( $settings['primary_hover'] ); ?>" class="wcbs-color-picker">
						</div>
						<div class="wcbs-color-row">
							<label for="accent_color"><?php esc_html_e( 'Accent Highlight Color', 'styler-for-woo' ); ?></label>
							<input type="text" id="accent_color" name="accent_color" value="<?php echo esc_attr( $settings['accent_color'] ); ?>" class="wcbs-color-picker">
						</div>
					</div>

					<div class="wcbs-color-section">
						<h4><?php esc_html_e( 'Calendar Colors', 'styler-for-woo' ); ?></h4>
						<div class="wcbs-color-row">
							<label for="calendar_bg"><?php esc_html_e( 'Calendar Background', 'styler-for-woo' ); ?></label>
							<input type="text" id="calendar_bg" name="calendar_bg" value="<?php echo esc_attr( $settings['calendar_bg'] ); ?>" class="wcbs-color-picker">
						</div>
						<div class="wcbs-color-row">
							<label for="calendar_header_bg"><?php esc_html_e( 'Calendar Header Background', 'styler-for-woo' ); ?></label>
							<input type="text" id="calendar_header_bg" name="calendar_header_bg" value="<?php echo esc_attr( $settings['calendar_header_bg'] ); ?>" class="wcbs-color-picker">
						</div>
						<div class="wcbs-color-row">
							<label for="day_available_bg"><?php esc_html_e( 'Available Day Background', 'styler-for-woo' ); ?></label>
							<input type="text" id="day_available_bg" name="day_available_bg" value="<?php echo esc_attr( $settings['day_available_bg'] ); ?>" class="wcbs-color-picker">
						</div>
						<div class="wcbs-color-row">
							<label for="day_selected_bg"><?php esc_html_e( 'Selected Day Background', 'styler-for-woo' ); ?></label>
							<input type="text" id="day_selected_bg" name="day_selected_bg" value="<?php echo esc_attr( $settings['day_selected_bg'] ); ?>" class="wcbs-color-picker">
						</div>
						<div class="wcbs-color-row">
							<label for="day_booked_bg"><?php esc_html_e( 'Booked / Disabled Day', 'styler-for-woo' ); ?></label>
							<input type="text" id="day_booked_bg" name="day_booked_bg" value="<?php echo esc_attr( $settings['day_booked_bg'] ); ?>" class="wcbs-color-picker">
						</div>
					</div>

					<div class="wcbs-color-section">
						<h4><?php esc_html_e( 'Time Slot Colors', 'styler-for-woo' ); ?></h4>
						<div class="wcbs-color-row">
							<label for="slot_bg"><?php esc_html_e( 'Slot Background', 'styler-for-woo' ); ?></label>
							<input type="text" id="slot_bg" name="slot_bg" value="<?php echo esc_attr( $settings['slot_bg'] ); ?>" class="wcbs-color-picker">
						</div>
						<div class="wcbs-color-row">
							<label for="slot_selected_bg"><?php esc_html_e( 'Selected Slot Background', 'styler-for-woo' ); ?></label>
							<input type="text" id="slot_selected_bg" name="slot_selected_bg" value="<?php echo esc_attr( $settings['slot_selected_bg'] ); ?>" class="wcbs-color-picker">
						</div>
					</div>
				</div>

				<!-- TAB 4: STYLES & SHAPES -->
				<div class="wcbs-tab-pane" id="tab-typography">
					<h3 class="wcbs-pane-heading"><?php esc_html_e( 'Shapes, Typography & Spacing', 'styler-for-woo' ); ?></h3>

					<div class="wcbs-form-group">
						<label><?php esc_html_e( 'Calendar Day Shape', 'styler-for-woo' ); ?></label>
						<div class="wcbs-shape-selector">
							<label class="wcbs-shape-opt <?php echo 'circle' === $settings['day_shape'] ? 'active' : ''; ?>">
								<input type="radio" name="day_shape" value="circle" <?php checked( $settings['day_shape'], 'circle' ); ?>>
								<span class="shape-icon circle-icon">15</span>
								<span><?php esc_html_e( 'Circle', 'styler-for-woo' ); ?></span>
							</label>
							<label class="wcbs-shape-opt <?php echo 'rounded' === $settings['day_shape'] ? 'active' : ''; ?>">
								<input type="radio" name="day_shape" value="rounded" <?php checked( $settings['day_shape'], 'rounded' ); ?>>
								<span class="shape-icon rounded-icon">15</span>
								<span><?php esc_html_e( 'Rounded', 'styler-for-woo' ); ?></span>
							</label>
							<label class="wcbs-shape-opt <?php echo 'pill' === $settings['day_shape'] ? 'active' : ''; ?>">
								<input type="radio" name="day_shape" value="pill" <?php checked( $settings['day_shape'], 'pill' ); ?>>
								<span class="shape-icon pill-icon">15</span>
								<span><?php esc_html_e( 'Pill', 'styler-for-woo' ); ?></span>
							</label>
							<label class="wcbs-shape-opt <?php echo 'square' === $settings['day_shape'] ? 'active' : ''; ?>">
								<input type="radio" name="day_shape" value="square" <?php checked( $settings['day_shape'], 'square' ); ?>>
								<span class="shape-icon square-icon">15</span>
								<span><?php esc_html_e( 'Square', 'styler-for-woo' ); ?></span>
							</label>
						</div>
					</div>

					<div class="wcbs-form-group">
						<label for="border_radius_base"><?php esc_html_e( 'Global Border Radius (px)', 'styler-for-woo' ); ?></label>
						<div class="wcbs-range-wrapper">
							<input type="range" id="border_radius_base_range" min="0" max="30" value="<?php echo esc_attr( $settings['border_radius_base'] ); ?>" class="wcbs-range-slider">
							<input type="number" id="border_radius_base" name="border_radius_base" min="0" max="30" value="<?php echo esc_attr( $settings['border_radius_base'] ); ?>" class="small-text">
						</div>
					</div>

					<div class="wcbs-form-group">
						<label for="box_shadow_intensity"><?php esc_html_e( 'Box Shadow Elevation', 'styler-for-woo' ); ?></label>
						<select id="box_shadow_intensity" name="box_shadow_intensity">
							<option value="none" <?php selected( $settings['box_shadow_intensity'], 'none' ); ?>><?php esc_html_e( 'None (Flat)', 'styler-for-woo' ); ?></option>
							<option value="subtle" <?php selected( $settings['box_shadow_intensity'], 'subtle' ); ?>><?php esc_html_e( 'Subtle / Crisp', 'styler-for-woo' ); ?></option>
							<option value="medium" <?php selected( $settings['box_shadow_intensity'], 'medium' ); ?>><?php esc_html_e( 'Medium (Default)', 'styler-for-woo' ); ?></option>
							<option value="deep" <?php selected( $settings['box_shadow_intensity'], 'deep' ); ?>><?php esc_html_e( 'Deep Floating', 'styler-for-woo' ); ?></option>
						</select>
					</div>

					<div class="wcbs-form-group">
						<label for="font_size_base"><?php esc_html_e( 'Base Font Size (px)', 'styler-for-woo' ); ?></label>
						<input type="number" id="font_size_base" name="font_size_base" min="12" max="20" value="<?php echo esc_attr( $settings['font_size_base'] ); ?>" class="small-text">
					</div>
				</div>

				<!-- TAB 5: FEATURES & UX -->
				<div class="wcbs-tab-pane" id="tab-features">
					<h3 class="wcbs-pane-heading"><?php esc_html_e( 'Conversion & UX Features', 'styler-for-woo' ); ?></h3>

					<div class="wcbs-toggle-row">
						<div>
							<strong><?php esc_html_e( 'Staff / Resource Visual Cards', 'styler-for-woo' ); ?></strong>
							<p><?php esc_html_e( 'Converts standard dropdowns into avatar cards with photos and titles.', 'styler-for-woo' ); ?></p>
						</div>
						<label class="wcbs-switch">
							<input type="checkbox" name="enable_staff_cards" value="yes" <?php checked( $settings['enable_staff_cards'], 'yes' ); ?>>
							<span class="slider round"></span>
						</label>
					</div>

					<div class="wcbs-toggle-row">
						<div>
							<strong><?php esc_html_e( 'Time Slot Tab Grouping', 'styler-for-woo' ); ?></strong>
							<p><?php esc_html_e( 'Categorizes available time slots into Morning, Afternoon, and Evening tabs.', 'styler-for-woo' ); ?></p>
						</div>
						<label class="wcbs-switch">
							<input type="checkbox" name="enable_slot_tabs" value="yes" <?php checked( $settings['enable_slot_tabs'], 'yes' ); ?>>
							<span class="slider round"></span>
						</label>
					</div>

					<div class="wcbs-form-group" style="padding: 12px 0;">
						<label for="slot_style"><?php esc_html_e( 'Time Slot Presentation Style', 'styler-for-woo' ); ?></label>
						<select id="slot_style" name="slot_style">
							<option value="chips" <?php selected( $settings['slot_style'], 'chips' ); ?>><?php esc_html_e( 'Interactive Chips / Pills (Recommended)', 'styler-for-woo' ); ?></option>
							<option value="grid" <?php selected( $settings['slot_style'], 'grid' ); ?>><?php esc_html_e( 'Uniform Grid', 'styler-for-woo' ); ?></option>
							<option value="list" <?php selected( $settings['slot_style'], 'list' ); ?>><?php esc_html_e( 'Vertical List', 'styler-for-woo' ); ?></option>
						</select>
					</div>

					<div class="wcbs-toggle-row">
						<div>
							<strong><?php esc_html_e( 'Urgency Capacity Badges', 'styler-for-woo' ); ?></strong>
							<p><?php esc_html_e( 'Displays "Only X spots left!" badge when capacity is low.', 'styler-for-woo' ); ?></p>
						</div>
						<label class="wcbs-switch">
							<input type="checkbox" name="enable_urgency_badge" value="yes" <?php checked( $settings['enable_urgency_badge'], 'yes' ); ?>>
							<span class="slider round"></span>
						</label>
					</div>

					<div class="wcbs-toggle-row">
						<div>
							<strong><?php esc_html_e( 'Sticky Live Price Summary Card', 'styler-for-woo' ); ?></strong>
							<p><?php esc_html_e( 'Displays dynamic real-time price calculation and booking details card.', 'styler-for-woo' ); ?></p>
						</div>
						<label class="wcbs-switch">
							<input type="checkbox" name="enable_live_summary" value="yes" <?php checked( $settings['enable_live_summary'], 'yes' ); ?>>
							<span class="slider round"></span>
						</label>
					</div>

					<div class="wcbs-toggle-row">
						<div>
							<strong><?php esc_html_e( 'Timezone Auto-Detector & Switcher', 'styler-for-woo' ); ?></strong>
							<p><?php esc_html_e( 'Shows slot times in customer local timezone.', 'styler-for-woo' ); ?></p>
						</div>
						<label class="wcbs-switch">
							<input type="checkbox" name="enable_timezone" value="yes" <?php checked( $settings['enable_timezone'], 'yes' ); ?>>
							<span class="slider round"></span>
						</label>
					</div>

					<div class="wcbs-toggle-row">
						<div>
							<strong><?php esc_html_e( 'Add to Personal Calendar Generator', 'styler-for-woo' ); ?></strong>
							<p><?php esc_html_e( 'Instant 1-click Google Calendar, Apple iCal, and Outlook links.', 'styler-for-woo' ); ?></p>
						</div>
						<label class="wcbs-switch">
							<input type="checkbox" name="enable_calendar_sync" value="yes" <?php checked( $settings['enable_calendar_sync'], 'yes' ); ?>>
							<span class="slider round"></span>
						</label>
					</div>

					<div class="wcbs-toggle-row">
						<div>
							<strong><?php esc_html_e( 'Skeleton Shimmer Loading Animations', 'styler-for-woo' ); ?></strong>
							<p><?php esc_html_e( 'Smooth modern skeleton loaders during AJAX calculations.', 'styler-for-woo' ); ?></p>
						</div>
						<label class="wcbs-switch">
							<input type="checkbox" name="enable_skeleton" value="yes" <?php checked( $settings['enable_skeleton'], 'yes' ); ?>>
							<span class="slider round"></span>
						</label>
					</div>
				</div>

			</form>
		</aside>

		<!-- Right Interactive Live Preview Panel -->
		<main class="wcbs-preview-stage">
			<div class="wcbs-preview-toolbar">
				<span class="wcbs-preview-label"><?php esc_html_e( 'Real-Time Interactive Preview', 'styler-for-woo' ); ?></span>
				<span class="wcbs-preview-status"><?php esc_html_e( 'Live Preview Connected', 'styler-for-woo' ); ?></span>
			</div>

			<div class="wcbs-preview-wrapper" data-device="desktop">
				<div class="wcbs-preview-frame">
					<!-- Simulated Booking Page Rendering -->
					<div id="wcbs-live-preview-content" class="wcbs-root-container wcbs-layout-<?php echo esc_attr( $settings['default_layout'] ); ?> wcbs-theme-<?php echo esc_attr( $settings['active_theme'] ); ?>"
					     data-wcbs-layout="<?php echo esc_attr( $settings['default_layout'] ); ?>"
					     data-wcbs-theme="<?php echo esc_attr( $settings['active_theme'] ); ?>">

						<!-- Wizard Navigation Simulation -->
						<div class="wcbs-wizard-nav" style="<?php echo 'wizard' === $settings['default_layout'] ? 'display:flex;' : 'display:none;'; ?>">
							<div class="wcbs-wizard-step active" data-step="1">
								<div class="wcbs-step-badge">1</div>
								<div class="wcbs-step-meta"><span class="wcbs-step-title">Staff</span></div>
							</div>
							<div class="wcbs-wizard-divider"></div>
							<div class="wcbs-wizard-step" data-step="2">
								<div class="wcbs-step-badge">2</div>
								<div class="wcbs-step-meta"><span class="wcbs-step-title">Date & Time</span></div>
							</div>
							<div class="wcbs-wizard-divider"></div>
							<div class="wcbs-wizard-step" data-step="3">
								<div class="wcbs-step-badge">3</div>
								<div class="wcbs-step-meta"><span class="wcbs-step-title">Details</span></div>
							</div>
							<div class="wcbs-wizard-divider"></div>
							<div class="wcbs-wizard-step" data-step="4">
								<div class="wcbs-step-badge">4</div>
								<div class="wcbs-step-meta"><span class="wcbs-step-title">Summary</span></div>
							</div>
						</div>

						<div class="wcbs-layout-grid <?php echo 'split' === $settings['default_layout'] ? 'wcbs-split-grid' : ''; ?>">
							<!-- Left Column (Step 1: Specialist, Calendar, Timezone) -->
							<div class="wcbs-split-col-left wcbs-preview-col-left">
								<div class="wcbs-col-header wcbs-split-only">
									<span class="wcbs-col-step-tag">Step 1</span>
									<h3 class="wcbs-col-title"><?php esc_html_e( 'Select Date', 'styler-for-woo' ); ?></h3>
								</div>

								<!-- Resource Cards Simulation -->
								<div class="wcbs-resource-cards-section">
									<label class="wcbs-section-label"><?php esc_html_e( 'Choose Specialist:', 'styler-for-woo' ); ?></label>
									<div class="wcbs-resource-grid">
										<div class="wcbs-resource-card active">
											<div class="wcbs-resource-avatar">
												<div class="wcbs-avatar-initials">DR</div>
												<div class="wcbs-resource-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
											</div>
											<div class="wcbs-resource-info">
												<div class="wcbs-resource-name">Dr. Sophia Miller</div>
												<div class="wcbs-resource-badge"><?php esc_html_e( 'Available', 'styler-for-woo' ); ?></div>
											</div>
										</div>
										<div class="wcbs-resource-card">
											<div class="wcbs-resource-avatar">
												<div class="wcbs-avatar-initials">AL</div>
												<div class="wcbs-resource-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
											</div>
											<div class="wcbs-resource-info">
												<div class="wcbs-resource-name">Alex Laurent</div>
												<div class="wcbs-resource-badge"><?php esc_html_e( 'Available', 'styler-for-woo' ); ?></div>
											</div>
										</div>
									</div>
								</div>

								<!-- Calendar Simulation -->
								<div class="wcbs-calendar-mockup">
									<div class="wcbs-cal-header-bar">
										<button type="button" class="wcbs-cal-nav-btn" aria-label="Previous Month">
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:auto;"><polyline points="15 18 9 12 15 6"></polyline></svg>
										</button>
										<strong>September 2026</strong>
										<button type="button" class="wcbs-cal-nav-btn" aria-label="Next Month">
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:auto;"><polyline points="9 18 15 12 9 6"></polyline></svg>
										</button>
									</div>
									<div class="wcbs-cal-days-header">
										<span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
									</div>
									<div class="wcbs-cal-days-grid">
										<span class="day-cell past">31</span>
										<span class="day-cell">1</span>
										<span class="day-cell">2</span>
										<span class="day-cell">3</span>
										<span class="day-cell today">4</span>
										<span class="day-cell">5</span>
										<span class="day-cell booked">6</span>
										<span class="day-cell">7</span>
										<span class="day-cell">8</span>
										<span class="day-cell">9</span>
										<span class="day-cell selected">10</span>
										<span class="day-cell">11</span>
										<span class="day-cell">12</span>
										<span class="day-cell booked">13</span>
										<span class="day-cell">14</span>
										<span class="day-cell">15</span>
										<span class="day-cell">16</span>
										<span class="day-cell">17</span>
										<span class="day-cell">18</span>
										<span class="day-cell">19</span>
										<span class="day-cell">20</span>
									</div>
								</div>

								<!-- Timezone Bar Simulation -->
								<div class="wcbs-timezone-bar">
									<div class="wcbs-timezone-display">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
										<span>Displaying times in:</span>
										<strong>America/New_York (EDT)</strong>
									</div>
									<span class="wcbs-timezone-change-btn">Change</span>
								</div>
							</div>

							<!-- Right Column (Step 2: Slots, Summary, Submit, Sync) -->
							<div class="wcbs-split-col-right wcbs-preview-col-right">
								<div class="wcbs-col-header wcbs-split-only">
									<span class="wcbs-col-step-tag">Step 2</span>
									<h3 class="wcbs-col-title"><?php esc_html_e( 'Select Time & Confirm', 'styler-for-woo' ); ?></h3>
								</div>

								<!-- Slot Tabs Simulation -->
								<div class="wcbs-slot-tabs-nav">
									<button type="button" class="wcbs-slot-tab-btn active" data-period="all">All Times</button>
									<button type="button" class="wcbs-slot-tab-btn" data-period="morning">Morning (2)</button>
									<button type="button" class="wcbs-slot-tab-btn" data-period="afternoon">Afternoon (3)</button>
									<button type="button" class="wcbs-slot-tab-btn" data-period="evening">Evening (1)</button>
								</div>

								<!-- Slots Simulation -->
								<div class="wcbs-slots-container wcbs-style-chips">
									<button type="button" class="wcbs-slot-item">
										<span class="wcbs-slot-time">09:00 AM</span>
									</button>
									<button type="button" class="wcbs-slot-item selected">
										<span class="wcbs-slot-time">10:30 AM</span>
										<span class="wcbs-urgency-badge">Only 1 left!</span>
									</button>
									<button type="button" class="wcbs-slot-item">
										<span class="wcbs-slot-time">01:00 PM</span>
									</button>
									<button type="button" class="wcbs-slot-item">
										<span class="wcbs-slot-time">02:30 PM</span>
									</button>
									<button type="button" class="wcbs-slot-item">
										<span class="wcbs-slot-time">04:00 PM</span>
										<span class="wcbs-urgency-badge">Only 2 left!</span>
									</button>
									<button type="button" class="wcbs-slot-item">
										<span class="wcbs-slot-time">06:00 PM</span>
									</button>
								</div>

								<!-- Summary Card Simulation -->
								<div class="wcbs-summary-card">
									<div class="wcbs-summary-header">
										<div class="wcbs-summary-product-info">
											<h4 class="wcbs-summary-title">Executive 1-on-1 Consultation</h4>
											<span class="wcbs-summary-badge">Appointment Summary</span>
										</div>
									</div>
									<div class="wcbs-summary-details">
										<div class="wcbs-summary-row">
											<div class="wcbs-summary-label">Specialist:</div>
											<div class="wcbs-summary-val">Dr. Sophia Miller</div>
										</div>
										<div class="wcbs-summary-row">
											<div class="wcbs-summary-label">Date:</div>
											<div class="wcbs-summary-val">Thursday, Sep 10, 2026</div>
										</div>
										<div class="wcbs-summary-row">
											<div class="wcbs-summary-label">Time Slot:</div>
											<div class="wcbs-summary-val">10:30 AM – 11:30 AM</div>
										</div>
									</div>
									<div class="wcbs-summary-cost-box">
										<div class="wcbs-summary-cost-label">Calculated Total:</div>
										<div class="wcbs-summary-cost-amount">$150.00</div>
									</div>
									<div class="wcbs-summary-guarantee">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
										<span>Instant Booking Confirmation</span>
									</div>
								</div>

								<!-- Submit Mockup -->
								<div class="wcbs-submit-mockup">
									<button type="button" class="button alt button-hero" style="width:100%; border-radius: var(--wcbs-radius);">
										<?php esc_html_e( 'Book Appointment Now &rarr;', 'styler-for-woo' ); ?>
									</button>
								</div>

								<!-- Calendar Sync Mockup -->
								<div class="wcbs-add-to-calendar-block" style="display:block; margin-top: 16px;">
									<div class="wcbs-cal-sync-title">
										<span>Sync to Personal Calendar:</span>
									</div>
									<div class="wcbs-cal-buttons-group">
										<span class="wcbs-cal-btn wcbs-cal-google">Google</span>
										<span class="wcbs-cal-btn wcbs-cal-apple">Apple / iCal</span>
										<span class="wcbs-cal-btn wcbs-cal-outlook">Outlook</span>
									</div>
								</div>
							</div>
						</div>

					</div><!-- /#wcbs-live-preview-content -->
				</div>
			</div>
		</main>
	</div>
</div>
