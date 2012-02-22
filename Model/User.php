<?php

App::uses('AuthComponent', 'Controller/Component');

/**
 * A user is a software agent, who uses the computer or network service.
 *
 * @author Thiago Colares
 */
class User extends AppModel {
    public $name = 'User';
	public $belongsTo = array('Role');
//	public $actsAs = array('Acl' => array('type' => 'requester'));
	
	/**
	 * Used to determine a Parent -> Child relationship
	 *
	 * @return mixed
	 * @author Thiago Colares
	 */
    function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['role_id'])) {
            $roleId = $this->data['User']['role_id'];
        } else {
            $roleId = $this->field('role_id');
        }
        if (!$roleId) {
            return null;
        } else {
            return array('Role' => array('id' => $roleId));
        }
    }

    public $hasOne = array(
        'Profile' => array(
            'className'    => 'Profile',
            'foreignKey' => 'id',
            'dependent'    => true
        )
    );
    
	public $hasMany = array(
		'Issue' => array(
			'className'    => 'Issue.Issue',
			'dependent'    => true,
			'foreignKey'	=> 'user_id'
		)
	);

	/**
	 * Set of code that is trigget before save
	 *
	 * @return boolean
	 * @author Thiago Colares
	 */
	public function beforeSave() {		
		// Password hashing is no longer automatic since CakePHP 2.0.0
	    if (isset($this->data[$this->alias]['password'])) {
	        $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
	    }
        
        unset($this->data[$this->alias]['confirm_password']);
        
	    return true;
	}

	/**
	 * Array with validation rules
	 *
	 * @var string
	 */
    public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'Por favor, utilize o formato contato@exemplo.com.br'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
		        'message' => 'This email is already in use'
			)
		),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'author')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        ),
        'password' => array(
            'identicalFieldValues' => array(
                'rule' => array('identicalFieldValues', 'confirm_password'),
                'message' => 'As senhas devem ser idênticas.'
            ),
            'notempty' => array(
                'rule' => array('notempty'),//, array('on' => 'create')),
                'message' => 'Este campo deve ser preenchido.',
                // 'on' => 'create'
            ),
        ),
        'confirm_password' => array(
            'notempty' => array(
                'rule' => array('notempty', array('on' => 'create')),
                'message' => 'Este campo deve ser preenchido.',
                'on' => 'create'
            ),
        )
    );
    
    /**
     *
     * @param array $field 
     * @param string $compare_field field that will be compared
     * @return boolean 
     * @author Rafael Ávila
     */
    function identicalFieldValues($field=array(), $compare_field=null) {
        foreach ($field as $key => $value) {
            $v1 = $value;
            $v2 = $this->data[$this->name][$compare_field];
            if ($v1 !== $v2) {
                return false;
            } else {
                continue;
            }
        }
        return true;
    }
}

?>