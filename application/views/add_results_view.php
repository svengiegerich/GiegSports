<table id="add_results" class="table table-bordered sortTable">
	<thead>
		<tr>
			<th class="span3">Namen</th>
			<?php foreach ($sport_groups as $sport_group): ?>
				<th class="span3">
					<?php echo $sport_group['name']; ?>
					(<?php switch ($sport_group['calcus']) {
								case 1:
									echo 'min.';
									break;
								case 2:
									echo 'sec.';
									break;
								case 3:
									echo 'm.';
									break;
								default:
									echo 'pkt.';		
						  } ?>)
				</th>
			<?php endforeach; ?>
			</div>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($competitors_info as $competitor_id => $competitor) {
			echo '<tr>';
			echo '<td id="competitor_name">' . $competitor['name'] . '</td>';
						
			foreach ($sport_groups as $sport_group_id => $sport_group) {
								
				$sport = array(
				'name'	      => $competitor_id . '_' . $sport_group_id,
				'id'          => 'input' . '_' . $competitor_id . '_' . $sport_group_id,
				'class' => 'required numeric',
				);	
				$test = 'sport_' . $sport_group_id;
							
				if (isset($competitor[$test])) { $sport['value'] = $competitor[$test]; }
					
					echo '<td>' . form_input($sport) . '</td>';
				}
			echo '</tr>';
		} ?>
	</tbody>
</table>
<?php echo form_submit('submit', 'Abschicken', 'class="btn btn-primary submit" data-loading-text="Einen Moment..."'); ?>
<?php echo form_close(); ?>
</div>
</div>
</section>

<style type="text/css">
@media only screen and (max-width: 500px), (min-device-width: 768px) and (max-device-width: 1024px)  {/*
	Label the data*/
	
	td:nth-of-type(1):before {
		content: "Teilnehmer: ";
	}
	<?php $i = 2; ?>
	<?php foreach ($sport_groups as $sport_group_id => $sport_group): ?>
		td:nth-of-type(<?php echo $i; ?>):before {
			content: "<?php echo $sport_group['name']; ?>: ";
		}
		<?php $i++; ?>
	<?php endforeach; ?>
	
	/*	td:nth-of-type(1):before { content: "Teilnehmer: "; }
	td:nth-of-type(2):before { content: "Last Name"; }
	td:nth-of-type(3):before { content: "Job Title"; }
	td:nth-of-type(4):before { content: "Favorite Color"; }
	td:nth-of-type(5):before { content: "Wars of Trek?"; }
	td:nth-of-type(6):before { content: "Porn Name"; }
	td:nth-of-type(7):before { content: "Date of Birth"; }
	td:nth-of-type(8):before { content: "Dream Vacation City"; }
	td:nth-of-type(9):before { content: "GPA"; }
	td:nth-of-type(10):before { content: "Arbitrary Data"; }*/
}
</style>


<script type="text/javascript">
	$('.submit').click(function() {
		$(this).button();
		$(this).button('loading');
	});
</script>
<script src="<?php echo base_url();?>js/validation.js"></script>