<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 
 * @package    StateMachine
 * @category   Libraries
 * @author     Managed I.T.
 * @copyright  (c) 2012 Managed I.T.
 * @license    http://labs.managedit.ie/license
 */
class ORM_StateMachine extends ORM {

	/**
	 * @var  StateMachine  StateMachine Instance
	 */
	protected $_statemachine = NULL;

	/**
	 * @var  string  Initial State
	 */
	protected $_initial_state = 'true';

	public function __construct($id = NULL)
	{
		parent::__construct($id);

		$this->_statemachine = StateMachine::factory($this->_initial_state, array(
			'transitions'          => $this->transitions(),
			'transition_callbacks' => $this->transition_callbacks(),
		));
	}

	public function transitions()
	{
		return array();
	}

	public function transition_callbacks()
	{
		return array();
	}

	public function state()
	{
		return $this->_statemachine->state();
	}

	public function can_transition($state_to, $state_from = NULL)
	{
		return $this->_statemachine->can_transition($state_to, $state_from);
	}

	public function transition($state_to)
	{
		return $this->_statemachine->transition($state_to);
	}
}