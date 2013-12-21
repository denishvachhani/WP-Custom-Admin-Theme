<?php

/**
 * Plugin Name: HS Custom Admin Theme
 * Plugin URI: http://heliossolutions.in/
 * Description: HS Custom Admin Theme Style endeavours to transform your working experience into more enjoyable, systemised and beneficial way.
 * Version: 1.0
 * Author: Helios Solutions
 * Author URI: http://heliossolutions.in/
 */
$hs_admin_theme_plugin_url = WP_PLUGIN_URL . '/hs-custom-admin-themes';
$hs_filepath = plugin_dir_path(__FILE__);
//echo $hs_filepath;
$options = array();

function hs_admin_theme_plugin_activate() {
    create_hs_admin_theme_table();
    insert_startup_value();
}

register_activation_hook(__FILE__, 'hs_admin_theme_plugin_activate');

function hs_admin_theme_plugin_uninstall() {
    drop_hs_admin_theme_table();
}

register_deactivation_hook(__FILE__, 'hs_admin_theme_plugin_uninstall');

function hs_action_init() {
    // Localization
    load_plugin_textdomain('hs_admin_theme', false, dirname(plugin_basename(__FILE__)) . '/languages');
}


// Add actions
add_action('init', 'hs_action_init');

function admin_theme() {

    /*
     * 	Use the add_options_page function
     * 	add_options_page( $page_title, $menu_title, $capability, $menu-slug, $function )
     *
     */
    add_options_page(
            'HS Custom Admin Theme', 'HS Custom Admin Theme', 'manage_options', 'admin-theme', 'admin_theme_options_page'
    );
    wp_enqueue_script('jscolor', plugins_url('hs-custom-admin-themes/inc/js/hscolor.js'), array('jquery'), '', false);
}

add_action('admin_menu', 'admin_theme');

function admin_theme_options_page() {

    if (!current_user_can('manage_options')) {

        wp_die('You do not have sufficient permissions to access this page.');
    }
    $verify = "AddNewTheme";
    global $hs_admin_theme_plugin_url;
    global $wpdb;
    $table_name = $wpdb->prefix . "hs_admin_theme";
    if (isset($_POST['selecttheme'])) {
        $verify = "EditTheme";
        $selecttheme = esc_html($_POST['selecttheme']);
        $sql = mysql_query("SELECT * FROM $table_name where id =" . $selecttheme);
        while ($row = mysql_fetch_array($sql)) {
            $themeid = $row["id"];
            $themename = $row["theme"];
            $menubgcolor = $row["menubg"];
            $menuhoverbgcolor = $row["menuhoverbg"];
            $submenubgcolor = $row["submenubg"];
            $menufontcolor = $row["menufontcolor"];
            $menufontcolorhover = $row["menufonthover"];
            require ('inc/style-gen.php');
        }
    } elseif (isset($_POST["save_newtheme"])) {
        $themename = $_POST["themename"];
        $menubgcolor = $_POST["menubgcolor"];
        $menuhoverbgcolor = $_POST["menuhoverbgcolor"];
        $submenubgcolor = $_POST["submenubgcolor"];
        $menufontcolor = $_POST["menufontcolor"];
        $menufontcolorhover = $_POST["menufontcolorhover"];

        $insert = "INSERT INTO $table_name (`theme`, `menubg`, `menuhoverbg`, `submenubg`, `menufontcolor`, `menufonthover`)
            VALUES ('$themename','$menubgcolor','$menuhoverbgcolor','$submenubgcolor','$menufontcolor','$menufontcolorhover')";
        $wpdb->query($insert);
        require ('inc/style-gen.php');
    } elseif (isset($_POST["update_theme"])) {
        $themeid = $_POST["themeid"];
        $themename = $_POST["themenameupdate"];
        $menubgcolor = $_POST["menubgcolor"];
        $menuhoverbgcolor = $_POST["menuhoverbgcolor"];
        $submenubgcolor = $_POST["submenubgcolor"];
        $menufontcolor = $_POST["menufontcolor"];
        $menufontcolorhover = $_POST["menufontcolorhover"];

        $update = "UPDATE $table_name SET `theme`='$themename',`menubg`='$menubgcolor',`menuhoverbg`='$menuhoverbgcolor',
            `submenubg`='$submenubgcolor',`menufontcolor`='$menufontcolor',`menufonthover`='$menufontcolorhover' WHERE `id`='$themeid'";
        $wpdb->query($update);
        require ('inc/style-gen.php');
    }

    require( 'inc/front-end.php' );
    require( 'inc/options-page-wrapper.php' );
}

/* Load Style-sheet for plugin */

function admin_theme_backend_styles() {
    wp_enqueue_style('admin_theme_backend_css', plugins_url('hs-custom-admin-themes/admin-theme.css'));
}

add_action('admin_head', 'admin_theme_backend_styles');

/* Load Javascript for plugin */

function admin_theme_frontend_scripts_and_styles() {

    wp_enqueue_script('jscolor', plugins_url('hs-custom-admin-themes/inc/js/hscolor.js'), array('jquery'), '', false);
}

add_action('wp_enqueue_scripts', 'admin_theme_frontend_scripts_and_styles');

/* Load Front End */

function create_hs_admin_theme_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hs_admin_theme";
    if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql2 = "CREATE TABLE " . $table_name . " (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`theme` VARCHAR(255) NULL,
	`menubg` VARCHAR(255) NULL,
	`menuhoverbg` VARCHAR(255) NULL,
	`submenubg` VARCHAR(255) NOT NULL,
	`menufontcolor` VARCHAR(255) NULL,
        `menufonthover` VARCHAR(255) NULL,
	 PRIMARY KEY (`id`)) ENGINE = InnoDB;";
    }
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql2);
}

function insert_startup_value() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hs_admin_theme";
    $insert_initial = "INSERT INTO $table_name (`theme`, `menubg`, `menuhoverbg`, `submenubg`, `menufontcolor`, `menufonthover`)
            VALUES ('Hs Theme-1','#CF4944','#DD823B','#BE3631','#FFFFFF','#FFFFFF'),('Hs Theme-2','#52ACCC','#096484','#4796B3','#FFFFFF','#FFFFFF'),('Hs Theme-3','#222222','#2EA2CC','#333333','#FFFFFF','#EBEBEB')";
    $wpdb->query($insert_initial);
}

/* DROP table on plugin deactivate */

function drop_hs_admin_theme_table() {
    global $table_prefix, $table_suffix, $wpdb;
    $table_name = $wpdb->prefix . "hs_admin_theme";
    $wpdb->query("DROP TABLE {$table_name}");
}

function front_end_load_admin_theme() {
    require( 'inc/front-end.php' );
}

add_action('admin_head', 'front_end_load_admin_theme');
if ($_GET['mode'] == 'delete') {
    global $wpdb;
    $table_name = $wpdb->prefix . "hs_admin_theme";
    $delete_que = "delete from $table_name where id= '" . $_GET['del_id'] . "'";
    $wpdb->query($delete_que);
    header("location:options-general.php?page=admin-theme");
} elseif ($_GET['mode'] == 'addtheme') {
    $verify = "AddNewTheme";
    header("location:options-general.php?page=admin-theme");
} elseif ($_GET['mode'] == 'restoredefault') {
    unlink(__DIR__. '/inc/css/custom.css');
    header("location:options-general.php?page=admin-theme");
}
?>