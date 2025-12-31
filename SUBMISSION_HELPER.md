1.  **Plugin Name:** Echo Media Shield
2.  **Short Description:** A unique PHP-only WordPress utility that acts as an echo-style media shield, focused on simplicity and efficiency, adding customizable overlays to images.
3.  **Detailed Description:**
    Echo Media Shield is a lightweight, PHP-only WordPress plugin designed to add a customizable "shield" overlay to images embedded using a simple shortcode. This plugin focuses on simplicity, efficiency, and adhering to WordPress best practices without relying on external files or complex dependencies.

    **Key Features:**

    *   **`[echo_media]` Shortcode:** Easily embed images into your posts or pages using the `[echo_media id="X"]` (using a Media Library ID) or `[echo_media url="path/to/image.jpg"]` (using a direct image URL) shortcode.
    *   **Customizable Shield Overlay:** When enabled, images embedded with the shortcode will display a customizable border and an interactive overlay that appears on hover.
    *   **Visual Customization:** Configure various aspects of the shield from a dedicated admin settings page:
        *   **Enable/Disable Shield:** Toggle the shield functionality on or off globally.
        *   **Border Color:** Set a custom hexadecimal color for the image border.
        *   **Overlay Text:** Define the text that appears within the overlay on hover.
        *   **Overlay Background Color:** Choose the background color for the overlay, supporting hex, RGB, and RGBA values for transparency.
        *   **Overlay Text Color:** Set the color of the overlay text using a hexadecimal code.
    *   **Hover Effects:** The shielded media features a subtle zoom effect on the image and a fade-in effect for the overlay upon hover, enhancing user interaction.
    *   **PHP-Only and No External Files:** True to its design, the plugin is entirely PHP-based, generating all necessary CSS inline to avoid extra HTTP requests and maintain a minimal footprint.
    *   **Admin Settings Page:** All configurations are managed through an intuitive settings page under "Settings" > "Echo Media Shield" in your WordPress admin dashboard.

    Echo Media Shield provides a straightforward and efficient way to add a unique visual layer to your embedded images, offering protection or simply an aesthetic distinction with minimal performance impact.
4.  **GitHub URL:** https://github.com/ogichanchan/echo-media-shield