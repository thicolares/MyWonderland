

<br><div class="row">
	<div class="span4">
		<?php echo $this->Html->image(
			(!is_array($userInfo['user']['image'][3]))
				?
				$userInfo['user']['image'][3]
				:
				'http://cdn.last.fm/flatness/catalogue/noimage/2/default_user_large.png'
			,
			array('escape' => false, 'class' => 'avatar', 'alt' => $userInfo['user']['realname']
		)); ?>
	</div>


<?php
$countryArr = array();

$i = 20;
foreach($pastRes as &$art){
	$art['w'] = $i;

	if(count($art[0]) > 0){
		//debug($art);
		foreach($art[0] as $country => $city){
			if(isset($countryArr[$country])){
				$countryArr[$country] = $countryArr[$country] + $city['count'] * $i;
			} else {
				$countryArr[$country] = $city['count'] * $i;
			}
		}
	}
		$i--;
		
}



$i = 20;
foreach($nextRes as &$art){
	$art['w'] = $i;
	if(count($art[0]) > 0){
		//debug($art);
		foreach($art[0] as $country => $city){
			if(isset($countryArr[$country])){
				$countryArr[$country] = $countryArr[$country] + $city['count'] * $i;
			} else {
				$countryArr[$country] = $city['count'] * $i;
			}
		}
	}
	$i--;

}
arsort($countryArr);
//debug($countryArr);
$tmp = array();
foreach($countryArr as $key => $value){
	$tmp[$value] = $key;
}

//debug($tmp);

?>

	<div class="span8">
		<div class="row">
			<div class="span4">
					<h2>Wonderlands</h2>
					<table class="table table-bordered">
					  <table class="table">
				        <thead>
				          <tr>
				            <th><?php print __('Rank'); ?></th>
				            <th><?php print __('Wonderland'); ?></th>
				          </tr>
				        </thead>
				        <tbody>
						<?php
							foreach($tmp as $key => $row){
								print "<tr><td>$key</td><td>$row</td></tr>";

							}

						?>
				        </tbody>
				      </table>
				</div>
				
				
									<h2>Artists</h2>
				<div class="span4">
						<table class="table table-bordered">
						  <table class="table">
					        <thead>
					          <tr>
					            <th><?php print __('Rank'); ?></th>
					            <th><?php print __('Wonderland'); ?></th>
					          </tr>
					        </thead>
					        <tbody>
							<?php
								foreach($artists as $key => $row){

									print "<tr><td>$key</td><td>" . $row['name'] . "</td></tr>";

								}

							?>
					        </tbody>
					      </table>
					</div>
				
				
		</div>
	</div>
</div>
