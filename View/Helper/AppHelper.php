<?php
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Based on Croogo
 */
class AppHelper extends Helper {

/**
 * Url helper function
 *
 * @param string $url 
 * @param bool $full 
 * @return mixed
 * @access public
 */
    public function url($url = null, $full = false) {
        if (!isset($url['locale']) && isset($this->params['locale'])) {
            $url['locale'] = $this->params['locale'];
        }
        return parent::url($url, $full);
    }
}
?>