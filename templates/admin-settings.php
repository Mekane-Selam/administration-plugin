<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('administration_options');
        do_settings_sections('administration_options');
        ?>
        
        <h2>Access Control</h2>
        <p>Select which WordPress roles can access the Administration interface:</p>
        
        <table class="form-table">
            <tr>
                <th scope="row">Access Roles</th>
                <td>
                    <?php foreach ($wp_roles as $role_key => $role_details) : ?>
                        <label>
                            <input type="checkbox" name="administration_access_roles[]" value="<?php echo esc_attr($role_key); ?>" <?php checked(in_array($role_key, $access_roles)); ?>>
                            <?php echo esc_html($role_details['name']); ?>
                        </label><br>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>