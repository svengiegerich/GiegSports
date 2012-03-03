<script src="<?php echo base_url();?>js/libs/jquery.tablesorter.min.js"></script>
<script>
  $(function() {
    $(".sortTable").tablesorter({ sortList: [[0, 0]] });
  });
</script>
<div class="row">
	<div class="span12">
		<?php $table_start = '<table class="table table-bordered table-striped sortTable">
							<thead>
								<tr>
									<th id="competitor_name" style="width: 200px">Namen</th>
									<th>Gesamtpunktzahl</th>';
									foreach ($sport_groups as $sport_group) {
										$table_start .= '<th>' . $sport_group['name'] . '</th>';
									}		
			$table_start .= '</tr></thead><tbody>';
			$table_end = '	</tbody></table>'; ?>
			
		<?php if (isset($who_gets_what['honorcharter'])): ?>
			<h3>Ehrenurkunde:</h3>
			<?php echo $table_start; ?>
			<?php foreach ($who_gets_what['honorcharter'] as $competitor_id): ?>
				<tr>	
					<td id="<?php echo $competitor_id; ?>">
						<?php echo $comeptitor_names[$competitor_id]; ?>
					</td>
					<td>
						<?php echo $competitor_points[$competitor_id]['total_points']; ?>
					</td>
						<?php foreach ($competitor_points[$competitor_id] as $competitor_id => $competitor_point): ?>
							<?php if ($competitor_id != 'total_points'): ?>
								<td><?php echo $competitor_point; ?></td>
							<?php endif; ?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			<?php echo $table_end; ?>
		<?php endif; ?>
				
		<?php if (isset($who_gets_what['winnercharter'])): ?>
			<h3>Siegerurkunde:</h3>
			<?php echo $table_start; ?>
			<?php foreach ($who_gets_what['winnercharter'] as $competitor_id): ?>
				<tr>	
					<td id="<?php echo $competitor_id; ?>">
						<?php echo $comeptitor_names[$competitor_id]; ?>
					</td>
					<td> 
						<?php echo $competitor_points[$competitor_id]['total_points']; ?>
					</td>
					<?php foreach ($competitor_points[$competitor_id] as $competitor_id => $competitor_point): ?>
						<?php if ($competitor_id != 'total_points'): ?>
							<td><?php echo $competitor_point; ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			<?php echo $table_end; ?>
		<?php endif; ?>
				
		<?php if (isset($who_gets_what['participantcharter'])): ?>
			<h3>Teilnehmerurkunde:</h3>
			<?php echo $table_start; ?>
			<?php foreach ($who_gets_what['participantcharter'] as $competitor_id): ?>
				<tr>	
					<td id="<?php echo $competitor_id; ?>">
						<?php echo $comeptitor_names[$competitor_id]; ?>
					</td>
					<td> 
						<?php echo $competitor_points[$competitor_id]['total_points']; ?>
					</td>
					<?php foreach ($competitor_points[$competitor_id] as $competitor_id => $competitor_point): ?>
						<?php if ($competitor_id != 'total_points'): ?>
							<?php if ($competitor_point == ''): ?>
								<td>-</td>
							<?php else: ?>
								<td><?php echo $competitor_point; ?></td>
							<?php endif; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			<?php echo $table_end; ?>
		<?php endif; ?>
		
		<input type="submit" name="export" value="Daten exportieren" class="btn submit"/>
		<input type="submit" name="print" value="Urkunden" class="btn pull-right submit">
	</div>
</div>

<style>
@media only screen and (max-width: 500px), (min-device-width: 768px) and (max-device-width: 1024px)  {/*
	Label the data*/
	td:nth-of-type(1):before {
		content: "Teilnehmer: ";
	}
	td:nth-of-type(2):before {
		content: "Gesamtpunktzahl: ";
	}
	
	<?php $i = 3; ?>
	<?php foreach ($sport_groups as $sport_group_id => $sport_group): ?>
		td:nth-of-type(<?php echo $i; ?>):before {
			content: "<?php echo $sport_group['name']; ?>: ";
		}
		<?php $i++; ?>
	<?php endforeach; ?>
}
</style>