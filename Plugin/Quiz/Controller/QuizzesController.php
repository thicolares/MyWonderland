<?php
App::uses('QuizAppController', 'Quiz.Controller');

/**
 * Registration Period Controller
 *
 * PHP version 5
 */
class QuizzesController extends QuizAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'LastFMs';
    
	public $auth;
	
	public $uses = array('Quiz.LastFMUser', 'Quiz.Chart');
	
	public $limit = 100;
	
	public $popIndex = 0.05;

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
	

	private function _getVotes($username){
		$res = $this->LastFMUser->findByLastfmUsername($username);
		$cemPor = $res['LastFMUser']['pop'] + $res['LastFMUser']['hipster'];
		if($cemPor != 0){
			$popPor = round($res['LastFMUser']['pop']/$cemPor*100, 2);
			$hipPor = round($res['LastFMUser']['hipster']/$cemPor*100, 2);
		} else {
			$hipPor = $popPor = 0;
		}

		if($this->Cookie->read('LastFM.' . $username))
			$hasVoted = true;
		else
			$hasVoted = false;
		$this->set(compact('popPor', 'hipPor', 'hasVoted'));
		
	}
	
	
	public function index($username = null){

		// terrible!!!!
		if($this->request->is('post')){
			
			$this->redirect('/q/' . $this->request->data['Quiz']['username']);
		}
		
		$this->_getVotes($username);
		
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
		
		foreach($userTopArtists['topartists']['artist'] as $artist){
			$userArtists[] = $artist['url'];
		}
		
		$occurrences = 0;
		
		$options = array('limit' => $this->limit);
		$topArtists = $this->Chart->get('gettopartists');

		foreach($topArtists['artists']['artist'] as $artist){
			if(in_array($artist['url'], $userArtists)){
				$occurrences++;
			}
		}
		
		if($occurrences/$this->limit >= $this->popIndex){
			$pop = true;
		} else {
			$pop = false;
		}
		
		$oPer = $occurrences/$this->limit*100;
		
		$this->set(compact(
			'username', 'userInfo', 'occurrences', 'pop', 'oPer', 'userTopArtists', 'topArtists'
		));
	}
	
	public function vote(){
		$resCookie = $this->Cookie->read('LastFM.' . $this->request->data['Quiz']['username']);
		if(empty($resCookie))
		{
			if($this->request->is('post')){
				$res = $this->LastFMUser->findByLastfmUsername($this->request->data['Quiz']['username']);
				if($res){
					$res['LastFMUser'][$this->request->data['action']]++;
					$this->request->data = $res;
//					debug( $this->request->data['LastFMUser']['lastfm_username']); die();
					$this->Cookie->write('LastFM.' . $this->request->data['LastFMUser']['lastfm_username'], 'true');
					$this->_save($this->LastFMUser);
					$this->Session->setFlash(__('U Thanks to help him or her to learn more about him or herself'), 'default', array('class' => 'alert alert-success'));
					$this->redirect('/q/' . $this->request->data['LastFMUser']['lastfm_username']);
				} else {
					$this->LastFMUser->create();
					$this->request->data['LastFMUser']['lastfm_username'] = $this->request->data['Quiz']['username'];
					$this->request->data['LastFMUser'][$this->request->data['action']] = 1;
					$this->Cookie->write('LastFM.' . $this->request->data['LastFMUser']['lastfm_username'], 'true');
					$this->_save($this->LastFMUser);
					$this->Session->setFlash(__('Thanks to help him or her to learn more about him or herself'), 'default', array('class' => 'alert alert-success'));
					$this->redirect('/q/' . $this->request->data['LastFMUser']['lastfm_username']);
				}
			}
		} else {
			$this->Session->setFlash(__('You have already voted for this user!'), 'default', array('class' => 'alert alert-error'));
			$this->redirect('/q/' . $this->request->data['Quiz']['username']);
		}
	}
}