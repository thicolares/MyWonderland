<div id="header">
    <div class="container_16">
        <div class="grid_8 header-left">
        <?php
            echo $this->Html->link(__('Dashboard'), array('controller' => 'systems', 'action' => 'dashboard', 'prefix' => 'admin', 'admin' => true, 'plugin' => false));
            echo ' <span>|</span> ';
            echo $this->Html->link(__('Visit website'), DS);
        ?>
        </div>

        <div class="grid_8 header-right">
        <?php
            echo __("You are logged in as: <strong>%s</strong>", $this->Session->read('Auth.User.email'));
            echo ' <span>|</span> ';
            echo $this->Html->link(__("Log out"), array('plugin' => null, 'profile' => false, 'controller' => 'users', 'action' => 'logout'));
        ?>
        </div>

        <div class="clear">&nbsp;</div>
    </div>
</div>