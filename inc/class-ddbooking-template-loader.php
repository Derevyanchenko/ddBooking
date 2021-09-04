<?php

class ddBooking_Template_Loader extends Gamajo_Template_Loader {

    protected $filter_prefix = 'ddbooking';

    protected $theme_template_directory = 'ddbooking';

    protected $plugin_directory = DDBOOKING_PATH;

    protected $plugin_template_directory = 'templates';

    public $templates;

    // custom functions

    public function register() {
        add_filter('template_include', [$this, 'ddbooking_templates']);

        $this->templates = array(
            'pages/template-addproperty.php' => 'Add Property',
            'pages/template-listproperty.php' => 'List Personal Properies',
            'pages/template-wishlist.php' => 'Template Wishlist',
        );
        add_filter('theme_page_templates', [$this, 'custom_template']);
        add_filter('template_include', [$this, 'load_template']);
    }

    public function load_template($template) {
        global $post;
        $template_name = get_post_meta( $post->ID, '_wp_page_template', true);

        if ( $template_name ) {
            if ( $this->templates[$template_name] ) {
                $file = DDBOOKING_PATH . $template_name; 
                if ( file_exists( $file ) ) {
                    return $file;
                }
            }  else {
                
            }
        }

        return $template;
    }

    public function custom_template($templates) {
        $templates = array_merge($templates, $this->templates);
        return $templates;
    }

    public function ddbooking_templates($template) {

        if ( is_post_type_archive('property') ) {

            $theme_files = ['archive-property.php', 'ddbooking/archive-property.php'];
            $exist = locate_template($theme_files, false);
            if ( $exist != '' ) {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/archive-property.php';
            }

        } elseif ( is_post_type_archive('agent') ) {

            $theme_files = ['archive-agent.php', 'ddbooking/archive-agent.php'];
            $exist = locate_template($theme_files, false);
            if ( $exist != '' ) {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/archive-agent.php';
            }

        } elseif ( is_singular('property') ) {
            
            $theme_files = ['single-property.php', 'ddbooking/single-property.php'];
            $exist = locate_template($theme_files, false);
            if ( $exist != '' ) {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/single-property.php';
            }

        }  elseif ( is_singular('agent') ) {
            
            $theme_files = ['single-agent.php', 'ddbooking/single-agent.php'];
            $exist = locate_template($theme_files, false);
            if ( $exist != '' ) {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/single-agent.php';
            }

        }

        return $template;
    }

}

$ddBooking_Template_Loader = new ddBooking_Template_Loader();
$ddBooking_Template_Loader->register();