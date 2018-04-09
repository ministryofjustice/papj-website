<?php
namespace ppj;

function template($data, $slug, $name = '')
{
    global $ppj_template_data;
    $ppj_template_data = $data;

    ob_start();
    get_template_part($slug, $name);
    $output = ob_get_contents();
    ob_end_clean();

    $ppj_template_data = null;
    return $output;
}

function partial($data, $slug, $name = '')
{
    return template($data, 'partials/' . $slug, $name);
}


function dump($var)
{
    echo "<pre>" . print_r($var, true) . "</pre>";
}

function renderPageBlockData($acf)
{
    $output = '';
    if (isset($acf) && is_array($acf)) {
        foreach ($acf as $fieldGroup) {
            if (isset($fieldGroup['show']) && $fieldGroup['show'] == '') {
                continue;
            } else {
                $blockType = $fieldGroup['acf_fc_layout'];

                switch ($blockType) {

                    case 'text_block':
                        $output .= partial($fieldGroup, 'textBlock');
                        break;

                    case 'search':
                        $output .= partial($fieldGroup, 'search');
                        break;

                    case 'video':
                        $output .= partial($fieldGroup, 'videoPlayer');
                        break;

                    case 'image_block':
                        $output .= partial($fieldGroup, 'imageBlock');
                        break;

                    case 'navigation_block':
                        $output .= partial($fieldGroup, 'navigationBlock');
                        break;

                    case 'landing_page':
                        $output .= partial($fieldGroup, 'landingPage');

                    default:
                        error_log('renderPageBlockData unrecognized block type ' . $blockType);
                }
            }
        }
    }
    return $output;
}

function my_acf_admin_head() {
    ?>
    <style type="text/css">
        /*        .acf-flexible-content {
					background-color: black;
				}*/

        .acf-flexible-content .layout .acf-fc-layout-handle {
            /*background-color: #00B8E4;*/
            background-color: #202428;
            color: #eee;
        }

        .acf-repeater.-row > table > tbody > tr > td,
        .acf-repeater.-block > table > tbody > tr > td {
            border-top: 2px solid #202428;
        }

        .acf-repeater .acf-row-handle {
            vertical-align: top !important;
            padding-top: 16px;
        }

        .acf-repeater .acf-row-handle span {
            font-size: 20px;
            font-weight: bold;
            color: #202428;
        }

        .imageUpload img {
            width: 75px;
        }

        .acf-repeater .acf-row-handle .acf-icon.-minus {
            top: 30px;
        }

    </style>
    <?php
}

add_action('acf/input/admin_head', __NAMESPACE__ . '\\my_acf_admin_head');


function stopAutoInsertionOfPTags()
{
    remove_filter('the_content', 'wpautop');
    remove_filter('acf_the_content', 'wpautop');
}
add_action('acf/init', __NAMESPACE__ . '\\stopAutoInsertionOfPTags', 15);

function disable_emojicons_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

function stopEmojicons()
{
    // https://wordpress.stackexchange.com/questions/185577/disable-emojicons-introduced-with-wp-4-2
    // all actions related to emojis
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');

    // filter to remove TinyMCE emojis
    add_filter('tiny_mce_plugins', __NAMESPACE__ . '\\disable_emojicons_tinymce');
}
add_action('acf/init', __NAMESPACE__ . '\\stopEmojicons', 15);

function videoPlayer($attrs)
{
    $a = shortcode_atts(array(
        'host' => 'youtube',
        'id' => '',
        'cover-image' => ''
    ), $attrs);

    return partial($a, 'videoPlayer');
}
add_shortcode('video-player', __NAMESPACE__ . '\\videoPlayer');

function shortcodeQuote($attrs)
{
    $a = shortcode_atts(array(
        'quote' => '',
        'quote-source' => '',
        'origin' => 'top-right',
        'style' => 'strong',
        'no-border' => '',
        'position' => 'left'
    ), $attrs);

    return partial($a, 'shortcode-quote');
}
add_shortcode('ppj-quote', __NAMESPACE__ . '\\shortcodeQuote');

function shortcodeCurrentYear($attrs)
{
    return date("Y");
}
add_shortcode('ppj-current-year', __NAMESPACE__ . '\\shortcodeCurrentYear');

function inlineSVG($svgFileName) {
    $templateDirectory = get_template_directory();
    $fullSVGPath = $templateDirectory . '/dest/img/svg/' . $svgFileName . '.svg';
    include($fullSVGPath);
}

function initializeAcfSettingsPage() {
    if ( function_exists( 'acf_add_options_page' ) ) {

        acf_add_options_page( array(
            'page_title' => 'PPJ Settings',
            'menu_title' => 'PPJ Settings',
            'menu_slug'  => 'ppj-general-settings',
            'capability' => 'edit_posts',
            'redirect'   => false
        ));

        acf_add_options_sub_page( array(
            'page_title'  => 'PPJ Footer Settings',
            'menu_title'  => 'Footer',
            'parent_slug' => 'ppj-general-settings',
        ));

    }
}
add_action('acf/init', __NAMESPACE__ . '\\initializeAcfSettingsPage', 15);

/**
 * This functions ensures that the ACF json file will now be saved
 * in a theme agnostic location, allowing ACF structures to be shared between
 * themes which may be beneficial in a multi-site scenario.
 *
 * https://www.advancedcustomfields.com/resources/local-json/
 */
function acf_json_save_point( $path )
{
    // update path
    $path = WPMU_PLUGIN_DIR .  '/ppj/acf-json';

    return $path;
}
add_filter('acf/settings/save_json', __NAMESPACE__ . '\\acf_json_save_point');

/**
 * ACF schema now loaded from /mu-plugins/ppj/acf-json
 *
 * See acf_json_save_point for rationale.
 */
function acf_json_load_point( $paths )
{
    // remove original path
    unset($paths[0]);

    // append path
    $paths[] = WPMU_PLUGIN_DIR . '/ppj/acf-json';

    return $paths;
}
add_filter('acf/settings/load_json', __NAMESPACE__ . '\\acf_json_load_point');

/**
 * The site is being divided into legs.
 * One leg for each job type.
 *
 * This function return the top level directory of the relative path
 * to derive the name of the leg.
 */
function getLegNameFromPath() {
    // remove any URL parameters
    $noParametersPath = explode('?', $_SERVER['REQUEST_URI'])[0];

    // take only the top level directory name
    $legName = explode('/', $noParametersPath)[1];

    return ($legName) ? $legName : 'landing-page';
}
