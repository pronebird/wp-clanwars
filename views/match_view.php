<?php
$current_unixtime = \WP_Clanwars\Utils::current_time_fixed('timestamp');
$match_unixtime = mysql2date('U', $match->date);
$match_date = mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $match->date);

$is_upcoming_match = $match_unixtime > $current_unixtime;
$is_playing_match = ($current_unixtime > $match_unixtime && $current_unixtime < ($match_unixtime + 3600) );
?>
<div class="wp-clanwars-match-card">
    <div class="wp-clanwars-match-card-page-curl"></div>
    <div class="wp-clanwars-match-card-meta">
        <?php echo mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $match->date); ?> |
<?php if (!empty($match->external_url)) : ?>
        <a target="_blank" href="<?php echo esc_url($match->external_url); ?>"><?php
            esc_html_e($match_status_text);
        ?></a>
<?php else: ?>
        <?php esc_html_e($match_status_text); ?>
<?php endif; ?>
    </div>

    <div class="wp-clanwars-match-card-header">
        <div class="wp-clanwars-match-card-header-item">
<?php if (is_object($team1_logo)) : ?>
            <img src="<?php echo esc_url($team1_logo->src); ?>"
                width="<?php echo esc_attr($team1_logo->width); ?>"
                height="<?php echo esc_attr($team1_logo->height); ?>"
                    alt="<?php echo esc_attr($match->team1_title); ?>" />
<?php else : ?>
            <div class="wp-clanwars-match-card-header-item-no-logo-team-wrap">
                <img src="<?php echo esc_url(WP_CLANWARS_URL . '/images/no-team-logo-light.png'); ?>" width="80" />
                <div class="wp-clanwars-match-card-header-item-team-name"><?php esc_html_e($match->team1_title); ?></div>
            </div>
<?php endif; ?>
        </div>

        <div class="wp-clanwars-match-card-header-item wp-clanwars-match-card-home-team-score"><?php
            esc_html_e( $match->team1_tickets );
        ?></div>

        <div class="wp-clanwars-match-card-header-item wp-clanwars-match-card-status-caption"><?php
        if ($is_upcoming_match) :
            _e('Upcoming', WP_CLANWARS_TEXTDOMAIN);
        elseif ($is_playing_match) :
            _e('Live', WP_CLANWARS_TEXTDOMAIN);
        else :
            _e('Final', WP_CLANWARS_TEXTDOMAIN);
        endif;
        ?></div>

        <div class="wp-clanwars-match-card-header-item wp-clanwars-match-card-visiting-team-score"><?php
            esc_html_e( $match->team2_tickets );
        ?></div>

        <div class="wp-clanwars-match-card-header-item">
<?php if (is_object($team2_logo)) : ?>
            <img src="<?php echo esc_url($team2_logo->src); ?>"
                width="<?php echo esc_attr($team2_logo->width); ?>"
                height="<?php echo esc_attr($team2_logo->height); ?>"
                alt="<?php echo esc_attr($match->team2_title); ?>" />
<?php else : ?>
            <div class="wp-clanwars-match-card-header-item-no-logo-team-wrap">
                <img src="<?php echo esc_url(WP_CLANWARS_URL . '/images/no-team-logo-light.png'); ?>" width="80" />
                <div class="wp-clanwars-match-card-header-item-team-name"><?php esc_html_e($match->team2_title); ?></div>
            </div>
<?php endif; ?>
        </div>
    </div>
    <table class="wp-clanwars-scores-table">
    <thead>
    <tr>
        <th class="wp-clanwars-scores-table-column-heading" style="width: 15%"></th>
<?php
    foreach($rounds as $map_number => $map_group) :
        $first = $map_group[0];
        $col_width = 85 / count($rounds);
        list($image_src, $width, $height, $is_intermediate) = wp_get_attachment_image_src($first->screenshot);
?>
        <th class="wp-clanwars-scores-table-column-heading" colspan="2" style="width: <?php echo $col_width; ?>%">
            <div class="wp-clanwars-scores-table-visual">
<?php if (!empty($image_src)) : ?>
                <img src="<?php echo esc_attr($image_src); ?>"
                    alt="<?php echo esc_attr($first->title); ?>"
                    class="wp-clanwars-scores-table-image"
                    width="<?php echo esc_attr($width); ?>" />
<?php endif; ?>
                <div class="wp-clanwars-scores-table-image-caption"><?php esc_html_e($first->title); ?></div>
            </div>
        </th>
<?php endforeach; ?>
    </tr>
    </thead>

    <tbody>
<?php
    $max_rounds = array_reduce($rounds, function ($current, $item) {
        return max($current, count($item));
    });

    for($current_round = 0; $current_round < $max_rounds; $current_round++) : ?>
    <tr class="wp-clanwars-scores-table-row <?php echo $current_round % 2 == 0 ? 'wp-clanwars-scores-table-row-even' : '';  ?>">
<?php
    $is_first_column = true;
    foreach($rounds as $map_number => $map_group) :
?>
<?php if ($is_first_column) : $is_first_column = false; ?>
        <td class="wp-clanwars-scores-table-row-heading"><?php
            /* translators: the heading for round number column. */
            esc_html_e( sprintf(_x('#%d', WP_CLANWARS_TEXTDOMAIN), $current_round + 1) );
        ?></td>
<?php endif; ?>
<?php if ($current_round < count($map_group)) : ?>
        <td class="wp-clanwars-scores-table-cell"><?php
            esc_html_e( $map_group[$current_round]->tickets1 );
        ?></td>
        <td class="wp-clanwars-scores-table-cell"><?php
            esc_html_e( $map_group[$current_round]->tickets2 );
        ?></td>
<?php else : ?>
        <td class="wp-clanwars-scores-table-cell"></td>
        <td class="wp-clanwars-scores-table-cell"></td>
<?php endif; ?>
<?php endforeach; ?>
    </tr>
<?php endfor; ?>
    </tbody>

    </table>

</div><!-- .wp-clanwars-match-card -->

<?php if(!empty($match->description)) :
    $description = nl2br(esc_html($match->description));
    $description = make_clickable($description);
    $description = wptexturize($description);
    $description = convert_smilies($description);

    // add target=_blank to all links
    $description = preg_replace('#(<a.*?)(>.*?</a>)#i', '$1 target="_blank"$2', $description);
?>
<p class="wp-clanwars-match-description">
    <?php echo $description; ?>
</p>
<?php endif; ?>