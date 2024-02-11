<?php
/**
 * Plugin Name:     Polonizer for FUPI
 * Plugin URI:      https://mpress.cc
 * Description:     Quickly translate Full Picture plugin slugs to polish.
 * Author:          Mateusz Zadorożny
 * Author URI:      https://mpress.cc
 * Text Domain:     polonizer-for-full-picture
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Polonizer_For_Full_Picture
 */

register_activation_hook(__FILE__, 'polonizer_activate');
add_action('admin_init', 'polonizer_redirect_after_activation');
add_action('admin_menu', 'polonizer_create_menu');

function polonizer_activate()
{
    add_option('polonizer_activate_redirect', true);
}

function polonizer_redirect_after_activation()
{
    if (get_option('polonizer_activate_redirect', false)) {
        delete_option('polonizer_activate_redirect');
        if (!isset($_GET['activate-multi'])) {
            wp_redirect(admin_url('options-general.php?page=polonizer-settings'));
            exit;
        }
    }
}

function polonizer_create_menu()
{
    add_options_page('Polonizer for Full Picture', 'Polonizer for Full Picture', 'manage_options', 'polonizer-settings', 'polonizer_settings_page');
}


function polonizer_settings_page()
{
    if (get_option('polonizer_activate_redirect', false)) {
        delete_option('polonizer_activate_redirect');
        wp_redirect(admin_url('options-general.php?page=polonizer-settings'));
        exit;
    }

    // Check if the `fupi_cookie_notice` option exists
    if (!get_option('fupi_cookie_notice')) {
        echo '<p>There is no cookie notice available.</p>';
    } else {
        echo '<form method="post"><input type="submit" name="polonize" value="Polonize the strings" class="button button-primary"></form>';

        if (isset($_POST['polonize'])) {
            polonizer_polonize_strings();
            echo '<p>Strings have been polonized.</p>';
            echo '<a href="/wp-admin/plugins.php">Go to plugins</a>';
        }
    }
}

function polonizer_polonize_strings()
{
    // Fetch the current option value
    // Fetch the current option value
    $option_values = get_option('fupi_cookie_notice');

    // Check if the option is already set and is an array
    if (is_array($option_values)) {
        // Update the texts within the array
        $option_values['agree_text'] = 'Zgoda';
        $option_values['decline_text'] = 'Odmowa';
        $option_values['cookie_settings_text'] = 'Więcej opcji';
        $option_values['notif_text'] = 'Używamy plików cookie, aby zapewnić najlepszą jakość przeglądania, spersonalizować zawartość naszej witryny, analizować jej ruch i wyświetlać odpowiednie reklamy. Więcej informacji można znaleźć w naszej {{polityce prywatności}}.';
        $option_values['necess_headline_text'] = 'Niezbędne pliki cookie.';
        $option_values['stats_headline_text'] = 'Statystyki';
        $option_values['stats_text'] = 'Statystyczne pliki cookie pomagają nam zrozumieć, w jaki sposób odwiedzający wchodzą w interakcję z naszą witryną, skąd pochodzą i w jaki sposób powracają do naszej witryny.';
        $option_values['pers_headline_text'] = 'Personalizacja';
        $option_values['pers_text'] = 'Preferencyjne pliki cookie umożliwiają witrynie zapamiętanie informacji, które zmieniają sposób zachowania lub wygląd witryny, takich jak preferowany język lub region, w którym się znajdujesz, a także dostosowanie na podstawie historii poprzednich wizyt.';
        $option_values['agree_to_selected_text'] = 'Zgoda na wybrane';
        $option_values['return_text'] = 'Powrót';
        $option_values['marketing_text'] = 'Marketingowe pliki cookie służą do śledzenia odwiedzających na stronach internetowych. Celem jest wyświetlanie reklam, które są odpowiednie i angażujące dla poszczególnych użytkowników, a tym samym bardziej wartościowe dla wydawców i zewnętrznych reklamodawców.';

        // Save the modified array back to the database
        update_option('fupi_cookie_notice', $option_values);
    }

    // If the option does not exist or is not an array, do nothing
}