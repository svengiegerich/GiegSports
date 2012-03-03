<!--<li class="slide">
	<div class="row">
		<div class="span6">
			<?php if (isset($count_competitors_have_taken_part)) : ?>
				<h1>Es haben <?php echo $count_competitors_have_taken_part; ?> Schüler teilgenommen.</h1>
			<?php endif; ?>
		</div>
		<div class="span6">
		
		</div>
	</div>
</li>-->

<?php foreach ($max_points as $sport_group_name => $sport_group):
	$average_points = $sport_group['average_points'];
	$best_competitor = $sport_group['competitor'];
	$most_points = $sport_group['max_points'];
?>
	<?php if (!empty($max_points)): ?>
		<?php switch ($sport_group['calcus']) {
					case 1:
						$unit = 'min.';
						break;
					case 2:
						$unit = 'sec.';
						break;
					case 3:
						$unit = 'm.';
						break;
					default:
						$unit = 'pkt.';		
			  } ?>
		
		<li>
			<h1 class="slider-h1"><?php echo $sport_group_name; ?>:</h1>
			<br /><br />
			<div class="row">
				<div class="span4">
					<h2>Der Durschnitt:</h2>
					<div class="result">
						<h1 class="points">
							<?php echo round($sport_group['average_result'], 2) . ' ' . $unit; ?>
							
						</h1>
					</div>
				</div>
				<div class="span8">
					<h2>Der <strong>Beste</strong>:</h2>
					<div class="result">
						<h1 class="points">
							<!--<?php echo $most_points; ?>-->
							<?php echo round($best_competitor['competitor_result'], 2) . ' ' . $unit; ?>
						</h1>
						<div class="competitor_box">
							<div class="competitor_content_triangle"></div>
							<div class="competitor_content">
								<h3><?php echo $best_competitor['competitor_name']; ?></h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</li>
	<?php endif; ?>
<?php endforeach; ?>