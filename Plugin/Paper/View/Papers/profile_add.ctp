<div class="paper form">

    <h2><?php print __('Submit') . ' ' . $title; ?></h2>
     	
		<div class="alert-message block-message warning">
			<p><?php print __('<strong>Before you begin,</strong> make sure you are familiar with the following documents:') ?></p>
			<!-- <p><strong>Antes de começar</strong>, certifique-se que você está familiarizado com os documentos a seguir:</p> -->
			<ul>
				<li><?php echo $this->Html->link('Normas de Submissão e Linhas temáticas para mini-cursos e oficinas', '/files/instrucoes_submissao_oficinas_minicurso.doc'); ?></li>
				<li><?php echo $this->Html->link('Normas de submissão de apresentação dos Painéis', '/files/instrucoes_submissao_painel.doc'); ?></li>
			</ul>
		</div>
		
		<div class="alert-message block-message warning">			
				<p><?php echo __('<strong>To be evaluated,</strong> your work MUST follow the format below. It is mandatory.') ?></p>
			<!-- <p><strong>Para ser avaliado</strong>, seu trabalho deve obrigatoriamente seguir o modelo a seguir:</p> -->
			<ul>
				<li><?php echo $this->Html->link('Modelo Para Submissão de Trabalhos (Mini-Cursos e Oficinas)', '/files/modelo_resumo_vii_forum_ea.doc'); ?></li>
				<li><?php echo $this->Html->link('Modelo Para Submissão de Trabalhos (Painéis)', '/files/modelo_resumo_vii_forum_ea_painel.doc'); ?></li>
			</ul>
		</div>

    <?php
    echo $this->Form->create('Paper', array('type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false)));

	echo $this->Form->hidden('Paper.user_id', array('value' => $this->Session->read('Auth.User.id')));
	?>
	<fieldset>
		<legend><?php print __('Paper Information'); ?></legend>
		<?php
		// PAPER TYPE
		echo $this->Html->div('clearfix', 
			$this->Form->label('Paper.paper_type_id', __('Paper Type')) .
			$this->Form->select('Paper.paper_type_id', $paperTypes, array('empty' => false))
		);

		// PAPER NAME
	    print $this->Form->input(
	        'title', 
	        array(
	            'label' => __('Title'),
	            'div' => 'clearfix required',
				'class' => 'span12'
	        )
	    );

		// PAPER ABSTRACT
        $divClass = 'clearfix required';
        $after = '';
        if ($this->Form->isFieldError('Paper.abstract')){
            $divClass .= ' error';
            $after = $this->Form->error('Paper.abstract');
        }
		echo $this->Html->div($divClass, 
			$this->Form->label('Paper.abstract', __('Abstract')) .
			$this->Form->textarea('Paper.abstract', array('rows' => '5', 'cols' => '5', 'class' => 'span12')).$after
		);

		// REASEARCH LINES
		echo $this->Html->div('clearfix', 
			$this->Form->label('ResearchLine.id', __('Research Line')) .
			$this->Form->select('PaperResearchLine.0.research_line_id', $researchLines, array('empty' => false))
		);
		
		// Get max upload file
		$maxUpload = (int)(ini_get('upload_max_filesize'));
		$maxPost = (int)(ini_get('post_max_size'));
		$memoryLimit = (int)(ini_get('memory_limit'));
		$uploadMb = min($maxUpload, $maxPost, $memoryLimit);
		
		// PAPER FILE
		$mod = $this->Html->link('Modelo Para Submissão de Trabalhos (Mini-Cursos e Oficinas)', '/files/modelo_resumo_vii_forum_ea.doc') . ' e ' . $this->Html->link('Modelo Para Submissão de Trabalhos (Painéis)', '/files/modelo_resumo_vii_forum_ea_painel.doc');
		
		echo $this->Form->input('Paper.submittedfile', 
			array(
				'type' => 'file', 'label' => __('Paper File'), 'div' => 'clearfix required',
				'after' => '<span class="help-block">' . __('Only Word 2003 (.doc) format file is allowed. Maximum allowed file size: %d mb<br> Remember, it\'s mandatory to follow the %s.',$uploadMb, $mod ) . '</span>')
		);
		?>
	</fieldset>
	
		<fieldset>
			<legend><?php print __('Authors and Co-Authors'); ?></legend>
				<div class="alert-message block-message warning">			
						<p>A pessoal para a qual será solicitada isenção na inscrição <strong>deve fazer o cadastro no sistema com o CPF ou Passaporte informado</strong>.</p>
				</div>
				<?php
				
				// AUTHOR NAME
			    $name = $this->Form->input('Paper.author_name', array(
			        'label' => __('Author'),
			        'class' => "large",
			        'type' => 'text',
					'value' => $profile['Profile']['name'],
					'disabled' => true
			    ));

				$mainDoc = $this->Form->input('Paper.author_main_doc', array(
				    'class' => "small",
				    'type' => 'text',
					'value' => $profile['Profile']['main_doc'],
					'disabled' => true
				));
				
				$askForExemption = $this->Form->radio('Paper.ask_for_exemption',
					array(
				    	0 => null
					),
					array(
				    	'legend' => false,
						'label' => false,
						'value' => 0
					)
				);
                
			    print $this->Html->div('clearfix required',
					$this->Html->div('inline-inputs',
		                __('Name') . " $name &nbsp;&nbsp;&nbsp;&nbsp;" . __('CPF or Passport') . ' ' . $mainDoc . 
						" &nbsp;&nbsp;&nbsp;&nbsp; $askForExemption " . __('Ask for exemption') . 
		                '<span class="help-block">'.__('Do not use punctuation in CPF or Passport, such as hyphen, points etc.').'</span>'
					)
				);
				
				echo $this->Form->hidden('Paper.author_main_doc', array('value' => $profile['Profile']['main_doc']));
				
				$coAuthorHtml = '';
				for($i=1;$i<=4;$i++){
				    // --------------- Name --------------------
				    $name = $this->Form->input('Paper.co_author_name_' . $i, array(
				        'label' => __('Co-Author') . " $i",
				        'class' => "large",
				        'type' => 'text',
						'error' => false
				    ));

					$mainDoc = $this->Form->input('Paper.co_author_main_doc_' . $i, array(
					    'class' => "small",
					    'type' => 'text',
						'error' => false
					));
					
					$askForExemption = $this->Form->radio('Paper.ask_for_exemption',
						array(
					    	$i => null
						),
						array(
					    	'legend' => false,
							'label' => false,
							'value' => $i
						)
					);
					
					/**
					 * Custom error message position
					 *
					 */
					$errorClass = $errorMessage = '';
					
					if($this->Form->isFieldError('Paper.co_author_name_' . $i)){
						$errorClass = ' error';
						$errorMessage = '<span class="conditional-required-error">' . $this->Form->error('Paper.co_author_name_' . $i) . '</span>';
					}
					if($this->Form->isFieldError('Paper.co_author_main_doc_' . $i)){
						$errorClass = ' error';
						$errorMessage = '<span class="conditional-required-error">' . $this->Form->error('Paper.co_author_main_doc_' . $i) . '</span>';
					}

				    $coAuthorHtml .= $this->Html->div('clearfix' . $errorClass,
						$this->Html->div('inline-inputs',
				                __('Name') . " $name &nbsp;&nbsp;&nbsp;&nbsp;" .
								__('CPF or Passport') . ' ' . $mainDoc . ' ' .
								" &nbsp;&nbsp;&nbsp;&nbsp; $askForExemption " . __('Ask for exemption') . 
								'<span class="help-block">' . __('Do not use punctuation. You MUST tell us the CPF in order to ask for exemption.') . '</span>' . 
								$errorMessage
						)
					);
				}
				echo $coAuthorHtml;
				
				print $this->Form->label('', '&nbsp;') . $this->Form->radio('Paper.ask_for_exemption',
					array(
				    	-1 => ''
					),
					array(
				    	'legend' => false,
						'value' => -1
					)
				) . " <strong>" . __('Do not ask for exemption') . '</strong>';
				
				?>
				
		</fieldset>
	<?php
	echo $this->Html->div('actions',
		$this->Form->submit(__('Submit Paper'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
            'action' => 'index',
			'profile' => true
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();
	?>
</div>