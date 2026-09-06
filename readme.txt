=== Styler for WooCommerce Bookings ===
Contributors: fahadkhalid211
Tags: woocommerce, bookings, appointment, calendar, customizer, styling, booking form, elementor, gutenberg
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transform WooCommerce Bookings with modern layout flows (Wizard, Split-View, Drawer, Modal, Bottom-Sheet), live visual customizer, designer themes, time slot grouping, staff cards, and live price breakdown.

== Description ==

**Styler for WooCommerce Bookings** revitalizes your WooCommerce Bookings frontend with modern, app-like booking experiences designed to boost conversion rates and delight customers.

The default WooCommerce Bookings interface relies on legacy datepicker styling, clunky tables, and rigid vertical forms. **Styler for WooCommerce Bookings** acts as a companion upgrade that wraps around the official WooCommerce Bookings engine, allowing you to choose from 6 layout styles, switch between pre-designed themes with 1-click, and visually tweak every color, border, and typography element.

### 🚀 Key Features

* **6 Layout & Flow Presentation Modes**:
  * **Two-Column Split View**: Side-by-side calendar and time slot selector with sticky price breakdown (inspired by Calendly & Airbnb).
  * **Multi-Step Wizard Flow**: App-style step-by-step booking journey (Service -> Date & Time -> Guests/Options -> Instant Confirmation).
  * **Slide-over Drawer (Off-Canvas)**: "Book Now" trigger button opens a smooth sliding side panel with backdrop.
  * **Popup / Lightbox Modal**: Clean modal overlay keeping product landing pages distraction-free.
  * **Mobile-First Bottom Sheet**: Converts the calendar and slot pickers into an iOS/Android-style bottom drawer on smartphones.
  * **Modern Standard Layout**: Streamlined, responsive overhaul of the standard inline booking form.

* **Live Visual Customizer**:
  * Interactive WordPress admin dashboard with real-time preview before saving.
  * 4 One-click Design Themes: *Clean Minimalist*, *Modern Dark*, *Luxury & Spa*, and *Vibrant Modern*.
  * Granular controls for brand colors, day shapes (circle, rounded square, square, pill), typography, border-radius, and box shadows.

* **Time Slot & Calendar Enhancements**:
  * Slot presentation styles: Button Chips/Pills, Grid, or List.
  * Time-of-Day Tab Grouping: Automatically categorizes available slots under **Morning**, **Afternoon**, and **Evening** tabs.
  * Urgency Badges: Dynamically alerts customers when slot capacity is low (e.g., *"Only 2 spots left!"*).

* **Conversion & UX Boosters**:
  * **Resource / Staff Visual Cards**: Replaces plain dropdown menus with profile cards featuring staff photos, titles, and active badges.
  * **Sticky Live Price Breakdown**: Real-time summary card updating duration, date, time slot, guest count, and calculated total.
  * **Timezone Auto-Detector**: Automatically detects visitor's local timezone and converts slot times with an interactive switcher.
  * **Skeleton Shimmer Loading**: Smooth modern pulse animations instead of standard spinning loaders during AJAX availability calculations.
  * **Add to Calendar Generator**: Generates instant links for Google Calendar, Apple iCal, and Outlook upon slot selection.

* **Page Builder & Universal Shortcode**:
  * Dedicated **Elementor Widget** (`WC_Bookings_Styler_Widget`).
  * Native **Gutenberg Block** (`wcbs/booking-form`).
  * Universal Shortcode: `[wc_booking_form product_id="123" layout="wizard" theme="modern-dark"]`.
  * Per-Product override meta box on the bookable product edit screen.

### ⚠️ Requirements

This plugin is an extension and requires the following to be installed and active:
1. [WooCommerce](https://wordpress.org/plugins/woocommerce/) (v6.0 or higher)
2. [WooCommerce Bookings](https://woocommerce.com/products/woocommerce-bookings/) (official extension by WooCommerce/Automattic)

== Installation ==

1. Upload the `styler-for-woo` folder to your `/wp-content/plugins/` directory (or upload the .zip file via **Plugins > Add New > Upload Plugin** in WordPress admin).
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Navigate to **Bookings > Booking Styler** in your WordPress admin menu to configure layouts, choose your design theme, and customize styles.
4. View any bookable product on the frontend to experience the new layout!

== Frequently Asked Questions ==

= Does this modify my existing bookings or order data? =
No. This plugin is purely a frontend presentation and styling enhancer. It seamlessly works with the existing WooCommerce Bookings calculation engine, cart, checkout, and order management.

= Can I use different layouts on different products? =
Yes! In addition to global settings, each bookable product has a "Booking Styler Options" meta box where you can select a custom layout (e.g. Wizard on consultations, Split-View on hotel/rentals) or specific theme for that product.

= Does it support Elementor and Gutenberg? =
Yes, it includes both an Elementor Widget and a Gutenberg Block.

== Changelog ==

= 1.1.3 =
* Fix: Resolve end time dropdown selection and prevent premature reset during customer-defined duration selection.
* Fix: Eliminate rapid recursive DOM rebuilds and event cascading on form updates.
* Fix: Synchronize dynamic AJAX responses for get_end_time_html with sleek custom dropdown menus.
* Fix: Robust calculation of booking totals and live summary duration display.

= 1.0.0 =
* Initial release.
* Added 6 layout flows: Standard, Split-View, Multi-Step Wizard, Slide-Over Drawer, Popup Modal, Bottom Sheet.
* Added Live Visual Customizer with real-time preview.
* Added 4 pre-designed themes (Clean Minimalist, Modern Dark, Luxury & Spa, Vibrant Modern).
* Added Staff / Resource visual avatar cards.
* Added Morning / Afternoon / Evening time slot grouping tabs.
* Added Urgency Badges for limited availability.
* Added Sticky Live Price Breakdown card.
* Added Timezone auto-detector and converter.
* Added Add-to-Calendar generator (Google, iCal, Outlook).
* Added Elementor Widget and Gutenberg Block support.
* Added Universal Shortcode `[wc_booking_form]`.
