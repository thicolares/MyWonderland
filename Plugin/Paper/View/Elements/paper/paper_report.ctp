<?php
	$res = $this->requestAction('/admin/papers/paperReport');
	$resHtml = '<h2>' . __('Summarized Paper Report') . '</h2>';
	foreach($res as $entity){
		$resHtml .= '<h5>' . __('Payment Entity') . ' <em>' . $entity['name'] . '</em></h5>';
		$resStatuses = array();
		foreach($entity['status'] as $status){
			$resStatuses[] =  $status['status'] . ' <strong>(' . $status['registrations'] . ')</strong>';
		}
		$resHtml .= implode($resStatuses,', ');
	}
	print $this->Html->div('well',$resHtml);
?>