<?php
App::uses('QuizAppController', 'Quiz.Controller');

/**
 * Registration Period Controller
 *
 * PHP version 5
 */
class PlacesController extends QuizAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Places';
    
	public $auth;
	
	public $uses = array('Quiz.LastFMUser', 'Quiz.Chart', 'Quiz.Artist');
	
	public $limit = 20;


    public function beforeFilter() {
        parent::beforeFilter();

//		parent::beforeFilter();
	    // $this->Cookie->name = 'baker_id';
	    // $this->Cookie->time =  3600;  // or '1 hour'
	    // $this->Cookie->path = '/bakers/preferences/';
	    // //$this->Cookie->domain = 'apimenti.com.br.com';
	    // // $this->Cookie->secure = true;  // i.e. only sent if using secure HTTPS
	    // $this->Cookie->key = 'ewcw41e2dqyxkxr90509358re!';
	    // $this->Cookie->httpOnly = true;
	}
	
	
	private function _countArtistEventsPerLocation($pastEvents){
		$countVenue = array();
		
		// apenas se tiver evento!
		if(isset($pastEvents['events']['event'])){
			foreach($pastEvents['events']['event'] as $event){
				
				if(isset($event['startDate']) && !empty($event['startDate'])){
					// date limite!
					if(date("Y", strtotime($event['startDate'])) >= date('Y', strtotime('-2 years'))){

						if(is_array($event['venue'])){
							$venue[$event['id']] = array(
								'city' => empty(
								$event['venue']['location']['city'])
								 || !isset($event['venue']['location']['city']) ? 
								$event['venue']['location']['city'] : null,
								'country' => $event['venue']['location']['country']
							);

							if(!isset($countVenue[$event['venue']['location']['country']])){
								$countVenue[$event['venue']['location']['country']]['count'] = 1;
							} else {
								$countVenue[$event['venue']['location']['country']]['count']++;				
							}

							if(isset($event['venue']['location']['city']) && !empty($event['venue']['location']['city'])){
								if(!isset($countVenue[ $event['venue']['location']['country']] [$event['venue']['location']['city'] ])){
									$countVenue[$event['venue']['location']['country']] [$event['venue']['location']['city']] ['count'] = 1; 
								} else {
									$countVenue[$event['venue']['location']['country']] [$event['venue']['location']['city']] ['count'] ++;
								}
							}
						} else {
							$countVanue = array();						
						}

					} else {
						$countVanue = array();
					}
				} else {
					$countVanue = array();
				}
			} // for
			
		} else {
			$countVanue = array();
		}
		return $countVenue;

	}

	
	private function _getNextEventsPerArtists($artists){
		$eventsPerArtists = array();
		$countVenue = array();
		foreach($artists as $artist){
			$this->Artist->artist = $artist['name'];
			$options = array('limit' => $this->limit);
			$pastEvents = $this->Artist->get('getevents', $options);
			$countVenue[$artist['name']][] = $this->_countArtistEventsPerLocation($pastEvents);
			$eventsPerArtists[$artist['name']] = $countVenue;
		}
		return $countVenue;
	}

	
	private function _getEventsPerArtists($artists){
		$eventsPerArtists = array();
		$countVenue = array();
		foreach($artists as $artist){
			$this->Artist->artist = $artist['name'];
			$options = array('limit' => $this->limit);
			$pastEvents = $this->Artist->get('getpastevents', $options);
			$countVenue[$artist['name']][] = $this->_countArtistEventsPerLocation($pastEvents);
			$eventsPerArtists[$artist['name']] = $countVenue;
		}
		return $countVenue;
	}
	
	public function index($username = null){

		// terrible!!!!
		if($this->request->is('post')){

			$this->redirect('/p/' . $this->request->data['Quiz']['lastfm_username']);
		}
				
		$this->LastFMUser->username = $this->Chart->username = $username;
		
		// LastFMUserr data
		$userInfo = $this->LastFMUser->get('getinfo');
		if($userInfo === false){
	        $this->Session->setFlash(__('Ops! Are You sure the user <strong>%s</strong> exist at LastFM?', $username), 'default', array('class' => 'alert alert-error'));
			$this->redirect('/');
		}
		
		$options = array('limit' => $this->limit);
		$userTopArtists = $this->LastFMUser->get('gettopartists', $options);
		
		if(!isset($userTopArtists['topartists']['artist']) && empty($userTopArtists['topartists']['artist'])){
			$this->Session->setFlash(__('The user '. $username . ' has no related artists at Last.FM :(', $username), 'default', array('class' => 'alert alert-error'));
			$this->redirect('/');
		}
		
		$pastRes = $this->_getEventsPerArtists($userTopArtists['topartists']['artist']);
		
		$nextRes = $this->_getNextEventsPerArtists($userTopArtists['topartists']['artist']);

				
		$this->set(compact('userInfo', 'pastRes', 'nextRes'));
		$this->set('artists', $userTopArtists['topartists']['artist']);
		
		// foreach($userTopArtists['topartists']['artist'] as $artist){
		// 	$userArtists[] = $artist['url'];
		// }
		// 
		// $occurrences = 0;
		// 
		// $options = array('limit' => $this->limit);
		// $topArtists = $this->Chart->get('gettopartists');
		// debug($topArtists);
		// 
		// foreach($topArtists['artists']['artist'] as $artist){
		// 	if(in_array($artist['url'], $userArtists)){
		// 		$occurrences++;
		// 	}
		// }
		// 
		// if($occurrences/$this->limit >= $this->popIndex){
		// 	$pop = true;
		// } else {
		// 	$pop = false;
		// }
		// 
		// $oPer = $occurrences/$this->limit*100;
		
		$this->set(compact(
			'username', 'userInfo', 'occurrences', 'pop', 'oPer', 'userTopArtists', 'topArtists'
		));
	}
	
}