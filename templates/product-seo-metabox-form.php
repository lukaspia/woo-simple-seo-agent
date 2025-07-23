<?php
/**
 * The template for displaying the product SEO metabox.
 *
 * @var \WP_Post $post The post object.
 * @var string $seoMetaTag The SEO title.
 */

if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="wrap" style="display: flex; gap: 20px;">
    <div class="agent-form" style="flex: 1;">
        <p>
            <input type="checkbox" id="wssa_seo_evaluation" name="wssa_seo_evaluation" checked />
            <label for="wssa_seo_evaluation"><?php esc_html_e('Make evaluation', 'woo-simple-seo-agent'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="wssa_seo_description" name="wssa_seo_description" checked />
            <label for="wssa_seo_description"><?php esc_html_e('Prepare description', 'woo-simple-seo-agent'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="wssa_seo_short_description" name="wssa_seo_short_description" checked />
            <label for="wssa_seo_short_description"><?php esc_html_e('Prepare short description', 'woo-simple-seo-agent'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="wssa_seo_tags" name="wssa_seo_tags" checked />
            <label for="wssa_seo_tags" name="wssa_seo_tags" /><?php esc_html_e('Make tags', 'woo-simple-seo-agent'); ?></label>
        </p>
        <p>
            <label for="wssa_agent_console"><?php esc_html_e('Agent console', 'woo-simple-seo-agent'); ?></label>
            <textarea id="wssa_agent_console" name="wssa_agent_console" rows="3" style="width: 100%;" placeholder="<?php esc_html_e('Add additional information for the agent if needed.', 'woo-simple-seo-agent'); ?>"></textarea>
        </p>
        <p>
            <button type="button" id="wssa-send-button" class="button button-primary" style="display: flex; align-items: center; gap: 5px;">
                <?php esc_html_e('Send', 'woo-simple-seo-agent'); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                </svg>
            </button>
        </p>
    </div>
    <div id="wssa-agent-answer" class="agent-answer" style="flex: 1; max-height: 250px; overflow-y: auto; border: 1px solid #ccd0d4; padding: 12px; background: #fdfdfd; border-radius: 4px;">

    </div>
</div>
