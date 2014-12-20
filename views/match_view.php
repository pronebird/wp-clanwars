<div class="wp-clanwars-page-v2">
<p class="teams">
	<span class="team1"><?php echo $team1_flag; ?> <?php esc_html_e($m->team1_title); ?></span>
	<span class="vs"><?php _e('vs.', WP_CLANWARS_TEXTDOMAIN); ?></span>
	<span class="team2"><?php esc_html_e($m->team2_title); ?> <?php echo $team2_flag; ?></span>
</p>
<div class="maplist clearfix">
<?php
// render maps/rounds
foreach($rounds as $map_group) :
	$first = $map_group[0];
	$image = wp_get_attachment_image_src($first->screenshot);
?>

	<div class="item">
		<div class="map">
			<?php if(!empty($image)) : ?>
			<img src="<?php esc_attr_e($image[0]); ?>" alt="<?php esc_attr_e($first->title); ?>" style="width: <?php echo $image[1]; ?>px; height: <?php echo $image[2]; ?>px;" />
			<?php endif; ?>
			<div class="title"><?php esc_html_e($first->title); ?></div>
		</div>

		<?php
		foreach($map_group as $round) :
			$t1 = $round->tickets1;
			$t2 = $round->tickets2;
			$round_class = $t1 < $t2 ? 'loss' : ($t1 > $t2 ? 'win' : 'draw');
		?>

		<div class="round">
			<span class="scores <?php esc_attr_e($round_class); ?>"><?php echo sprintf(__('%d:%d', WP_CLANWARS_TEXTDOMAIN), $t1, $t2); ?></span>
		</div>

		<?php endforeach; // .rounds ?>

	</div>

<?php endforeach; // maps ?>
</div> <!-- .maplist -->

<?php
	$t1 = $m->team1_tickets;
	$t2 = $m->team2_tickets;
	$round_class = $t1 < $t2 ? 'loss' : ($t1 > $t2 ? 'win' : 'draw');

	$score_text = sprintf(__('%d:%d', WP_CLANWARS_TEXTDOMAIN), $t1, $t2);
?>

<div class="summary">
	<div class="scores <?php esc_attr_e($round_class); ?>"><?php echo $score_text; ?></div>
</div>

<ul class="match-props">
	<li class="date"><?php echo mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $m->date); ?></li>

<?php if($match_status_text) : ?>
	<li class="status type-<?php echo $m->match_status; ?>"><?php esc_html_e($match_status_text);?></li>
<?php endif; ?>

<?php if(!empty($m->external_url)) : ?>
	<li class="external_url">
		<a href="<?php esc_attr_e($m->external_url); ?>" target="_blank"><?php echo esc_url($m->external_url); ?></a>
	</li>
<?php endif; ?>

</ul> <!-- .match-props -->

<?php if(!empty($m->description)) : 
	$description = nl2br(esc_html($m->description));
	$description = make_clickable($description);
	$description = wptexturize($description);
	$description = convert_smilies($description);

	// add target=_blank to all links
	$description = preg_replace('#(<a.*?)(>.*?</a>)#i', '$1 target="_blank"$2', $description);
?>
	<p class="description"><?php echo $description; ?></p>
<?php endif; ?>

</div> <!-- .wp-clanwars-page -->