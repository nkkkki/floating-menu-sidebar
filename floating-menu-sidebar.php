<?php
/*
Plugin Name: Floating Menu Sidebar
Description: Display any WordPress menu as a floating sidebar with position control.
Version: 1.1
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

class Floating_Menu_Sidebar {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_footer', array($this, 'render_floating_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function add_plugin_menu() {
        add_options_page(
            'Floating Menu Sidebar',
            'Floating Menu Sidebar',
            'manage_options',
            'floating_menu_sidebar',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('fms_settings_group', 'fms_menu');
        register_setting('fms_settings_group', 'fms_position');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h2>Floating Menu Sidebar Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('fms_settings_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Select Menu</th>
                        <td>
                            <select name="fms_menu">
                                <?php
                                $menus = wp_get_nav_menus();
                                $selected = get_option('fms_menu');
                                foreach ($menus as $menu) {
                                    echo '<option value="' . esc_attr($menu->term_id) . '" ' . selected($selected, $menu->term_id, false) . '>' . esc_html($menu->name) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Sidebar Position</th>
                        <td>
                            <select name="fms_position">
                                <?php
                                $positions = ['top-left', 'top-right', 'middle-left', 'middle-right', 'bottom-left', 'bottom-right'];
                                $current = get_option('fms_position', 'middle-left');
                                foreach ($positions as $pos) {
                                    echo '<option value="' . esc_attr($pos) . '" ' . selected($current, $pos, false) . '>' . ucfirst(str_replace('-', ' ', $pos)) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_styles() {
        wp_register_style('fms-style', false);
        wp_enqueue_style('fms-style');
        wp_add_inline_style('fms-style', $this->custom_css());
    }

    private function custom_css() {
        return <<<CSS
.floating-menu-sidebar {
    position: fixed;
    z-index: 9999;
    background: #fff;
    padding: 10px 15px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    font-family: sans-serif;
}
.floating-menu-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.floating-menu-sidebar li {
    margin: 8px 0;
}
.floating-menu-sidebar a {
    text-decoration: none;
    color: #0073aa;
}
.floating-menu-sidebar a:hover {
    color: #005177;
}

.floating-menu-sidebar.top-left    { top: 20px; left: 20px; }
.floating-menu-sidebar.top-right   { top: 20px; right: 20px; }
.floating-menu-sidebar.middle-left { top: 50%; left: 20px; transform: translateY(-50%); }
.floating-menu-sidebar.middle-right{ top: 50%; right: 20px; transform: translateY(-50%); }
.floating-menu-sidebar.bottom-left { bottom: 20px; left: 20px; }
.floating-menu-sidebar.bottom-right{ bottom: 20px; right: 20px; }
CSS;
    }

    public function render_floating_menu() {
        $menu_id = get_option('fms_menu');
        $position = get_option('fms_position', 'middle-left');

        if (!$menu_id) return;

        echo '<div class="floating-menu-sidebar ' . esc_attr($position) . '">';
        wp_nav_menu([
            'menu' => (int)$menu_id,
            'container' => false,
            'menu_class' => '',
            'items_wrap' => '<ul>%3$s</ul>'
        ]);
        echo '</div>';
    }
}

new Floating_Menu_Sidebar();