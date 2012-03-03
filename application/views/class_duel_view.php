<div class="row" id="class_duel">
	<div id="loser" class="offset2 span4">
		<h2>Der Verlierer:</h2>
		<div class="result">
			<h1 class="points"><?php echo $loser['class_name']; ?></h1>
			<div class="points_box">
				<div class="points_content_triangle"></div>
				<div class="points_content">
					<h3 class="loser class"><?php echo round($loser['points']); ?></h3>
				</div>
			</div>
		</div>	
	</div>
	<div class="span4 winner">
		<h2>Der Gewinner!</h2>
		<div class="result">
			<h1 class="points"><?php echo $winner['class_name']; ?></h1>
			<div class="points_box">
				<div class="points_content_triangle"></div>
				<div class="points_content">
					<h3 class="winner class"><?php echo round($winner['points']); ?></h3>
				</div>
			</div>
		</div>	
	</div>
</div>