<?php 
$res = $this->requestAction('/profile/papers/exemptNotification');
if(isset($res['message']) && !empty($res['message'])) {
    echo $this->Html->div('alert-message block-message '.$res['class'],
        $res['message']
    );
} 
?>