
<?php
/**
 * Plugin Name: Metabox Series
 * Plugin URI: https://talib.netlify.com
 * Description:
 * Version: 1.0.0
 * Author: TALIB
 * Author URI: https://talib.netlify.com
 * Text Domain: metabox
 */

function add_metabox()
{
    add_meta_box(
        'metabox_fields',
        __('metabox Fields', 'domain'),
        'metabox_fields_callback',
        'post'
    );
}
add_action('admin_init', 'add_metabox');

// ADMIN CONTENT
function metabox_fields_callback($post)
{
    // nonce
    wp_nonce_field('metabox_action', 'metabox_field');

    //input content text
    $label_text = __('text', 'domain');
    $text = get_post_meta($post->ID, 'text', true);

    // input content checkbox
    $label_checkbox = __('checkbox', 'domain');
    $checkbox = get_post_meta($post->ID, 'checkbox', true);
    $checked = $checkbox == 1 ? 'checked' : '';

    // input content dropbox
    $label_dropdown = __('Dropdown', 'domain');
    $dropdown = get_post_meta($post->ID, 'dropdown', true);
    $dropdown_values = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $dropdown_html = "<select id='dropdown' name='dropdown'>";
    foreach ($dropdown_values as $key => $value) {
        if ($value == $dropdown) {
            $dropdown_html .= "<option selected>$value</option>";
        } else {
            $dropdown_html .= "<option>$value</option>";
        }
    }
    $dropdown_html .= "</select>";





    $metabox_html = <<<BDT
        <div class="form-group">
            <label for="text">{$label_text}</label>
            <input type="text" name="text" id="text" value="{$text}"/>
        </div>
        <div class="form-group">
            <label for="checkbox">{$label_checkbox}</label>
            <input type="checkbox" name="checkbox" id="checkbox" value="1" {$checked}/>
            </div>
        <div class="form-group">
            <label for="dropdown">{$label_dropdown}</label>
            {$dropdown_html}
        </div>
BDT;
    echo $metabox_html;

}

// SAVE DATA
function save_metabox($post_id)
{
    //varify nonce
    if (!is_secured('metabox_action', 'metabox_field', $post_id)) {
        return $post_id;
    }

    //input content text
    $text = isset($_POST['text']) ? $_POST['text'] : '';
    $text = sanitize_text_field($text);
    update_post_meta($post_id, 'text', $text);

    //input content checkbox
    $checkbox = isset($_POST['checkbox']) ? $_POST['checkbox'] : '';
    $checkbox = sanitize_text_field($checkbox);
    update_post_meta($post_id, 'checkbox', $checkbox);

    //input content dropdown
    $dropdown = isset($_POST['dropdown']) ? $_POST['dropdown'] : '';
    // $dropdown = sanitize_text_field($dropdown);
    update_post_meta($post_id, 'dropdown', $dropdown);

}
add_action('save_post', 'save_metabox');

// SEQURITY CHECK
function is_secured($action, $nonce_field, $post_id)
{
    $nonce = isset($_POST[$nonce_field]) ? $_POST[$nonce_field] : '';

    if ($nonce == '') {
        return false;
    }
    if (!wp_verify_nonce($nonce, $action)) {
        return false;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return false;
    }

    if (wp_is_post_autosave($post_id)) {
        return false;
    }

    if (wp_is_post_revision($post_id)) {
        return false;
    }

    return true;

}
