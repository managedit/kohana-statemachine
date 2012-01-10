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
	protected $_initial_state = NULL;

	/**
	 * @var  string  Column to use for state management
	 */
	protected $_state_column = 'state';

	public function __construct($id = NULL)
	{
		parent::__construct($id);

		$this->_statemachine = StateMachine::factory($this->_initial_state, array(
			'transitions'          => $this->transitions(),
			'transition_callbacks' => $this->transition_callbacks(),
		));
	}

	/**
	 * Returns the transitions array
	 * 
	 * @return  array
	 */
	public function transitions()
	{
		return array();
	}

	/**
	 * Returns the transition callbacks array.
	 * 
	 * [!!] Remember that these are called regardless of if the model has been saved or not!
	 * 
	 * @return  array
	 */
	public function transition_callbacks()
	{
		return array();
	}

	/**
	 * Returns the current state
	 * 
	 * @return  string
	 */
	public function state()
	{
		return $this->_statemachine->state();
	}

	/**
	 * Checks if a transition between two states is acceptable.
	 * 
	 * @param   string  $state_to    Transition to state
	 * @param   string  $state_from  Transition from state
	 * @return  bool
	 */
	public function can_transition($state_to, $state_from = NULL)
	{
		return $this->_statemachine->can_transition($state_to, $state_from);
	}

	/**
	 * Transitions to the supplied state, triggering any
	 * necessary callbacks.
	 * 
	 * @param   string  $state_to    Transition to state
	 * @throws  StateMachine_InvalidTransition_Exception
	 * @return  ORM_StateMachine
	 */
	public function transition($state_to)
	{
		// This will raise if there is a failure.
		$this->_statemachine->transition($state_to);

		$this->{$this->_state_column} = $state_to;

		return $this;
	}

	/**
	 * Intercepts attempts to set the `state` column manually.
	 * 
	 * @param  string $column  Column name
	 * @param  mixed  $value   Column value
	 * @return void
	 */
	public function set($column, $value)
	{
		switch ($column)
		{
			case $this->_state_column:
				throw new ORM_StateMachine_Exception('Unable to manually set the state column. Please use the ORM_StateMachine::transition() method.');
			default:
				return parent::set($column, $value);
		}
	}
}