<?php
/**
 * Public-facing Program Page Controller
 */
class Administration_Program_Page {
    public static function init() {
        add_action('template_redirect', [__CLASS__, 'maybe_render_program_page']);
        add_action('init', [__CLASS__, 'add_program_query_var']);
    }

    public static function add_program_query_var() {
        add_rewrite_tag('%program_id%', '([^&]+)');
        add_rewrite_rule('^program/([^/]+)/?$', 'index.php?program_id=$1', 'top');
    }

    public static function maybe_render_program_page() {
        if (get_query_var('program_id')) {
            $program_id = sanitize_text_field(get_query_var('program_id'));
            global $wpdb;
            $table = $wpdb->prefix . 'core_programs';
            $program = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ProgramID = %s", $program_id));
            if (!$program) {
                wp_die(__('Program not found.', 'administration-plugin'));
            }
            $type = strtolower($program->ProgramType);
            $template_file = ADMINISTRATION_PLUGIN_PATH . 'templates/public/program-types/' . $type . '.php';
            if (!file_exists($template_file)) {
                $template_file = ADMINISTRATION_PLUGIN_PATH . 'templates/public/program-types/default.php';
            }
            // Make $program available in template
            global $program_page_data;
            $program_page_data = $program;
            include $template_file;
            exit;
        }
    }
}
Administration_Program_Page::init(); 