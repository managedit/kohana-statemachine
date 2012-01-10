<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 
 * @package    StateMachine
 * @category   Libraries
 * @author     Managed I.T.
 * @copyright  (c) 2012 Managed I.T.
 * @license    http://labs.managedit.ie/license
 */
class StateMachine {

	public static function factory($initial_state = NULL, $options = array())
	{
		return new StateMachine($initial_state, $options);
	}

	protected $_state = NULL;

	protected $_transitions = array();

	protected $_transition_callbacks = array();

	protected function __construct($initial_state == NULL, $options = array())
	{
		$this->_state = $initial_state;

		foreach ($options as $key => $value)
		{
			$this->{'_'.$key} = $value;
		}
	}

	/**
	 * Returns the current state
	 * 
	 * @return  string
	 */
	public function state()
	{
		return $this->_state;
	}

	/**
	 * Returns the transitions array
	 * 
	 * @return  array
	 */
	public function transitions()
	{
		return $this->_transitions;
	}

	/**
	 * Returns the transition callbacks array
	 * 
	 * @return  array
	 */
	public function transition_callbacks()
	{
		return $this->_transition_callbacks;
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
		$transitions = $this->transitions();
		
		if ($state_from === NULL)
		{
			$state_from = $this->_state;
		}

		if ( ! array_key_exists($state_from, $transitions))
			return FALSE;
		
		$allowed_transitions = $transitions[$state_from];
		
		return in_array($state_to, $allowed_transitions);
	}

	/**
	 * Transitions to the supplied state, triggering any
	 * necessary callbacks.
	 * 
	 * @param   string  $state_to    Transition to state
	 * @throws  StateMachine_InvalidTransition_Exception
	 * @return  void
	 */
	public function transition($state_to)
	{
		$state_from = $this->state();

		if ($state_to == $state_from)
			throw new StateMachine_InvalidTransition_Exception('Unable to transition to \':state_to\' from \':state_from\'', array(
					'state_to'   => $state,
					'state_from' => $this->_state,
				));

		if ( ! $this->can_transition($state_to))
			throw new StateMachine_InvalidTransition_Exception('Unable to transition to \':state_to\' from \':state_from\'', array(
					'state_to'   => $state,
					'state_from' => $this->_state,
				));
		
		Kohana::$log->add(Log::DEBUG, "Transitioning from ':state_from' to ':state_to'", array(
			':state_from' => $state_from,
			':state_to'   => $state_to,
		));
		
		$this->state = $state_to;

		// Trigger the transition callback if needed
		$transition_callbacks = $this->transition_callbacks();

		if (array_key_exists($state_to, $transition_callbacks))
		{
			try
			{
				call_user_func($transition_callbacks[$state_to], $state_to, $state_from, $save);
			}
			catch (Exception $e)
			{
				$this->state = $state_from;
				
				Kohana::$log->add(Log::ERROR, "Failed to transition from :state_from to :state_to. Message: :message", array(
						':state_from' => $state_from,
						':state_to'   => $state_to,
						':message'    => $e->getMessage(),
					));
				
				throw $e;
			}
		}
	}

	/**
	 * Generates an dot graph of the state machine, returns the content of the image.
	 * 
	 * @param  string  $format     Image format - PNG, SVG, JPG
	 * @param  string  $direction  Direction of graph - LR, TB, RL, BT
	 * @param  string  $shape      Shape of states - circle, oval, square
	 */
	public function generate_diagram($format = NULL, $direction = NULL, $shape = NULL)
	{
		$format = is_null($format) ? 'png' : strtolower($format);
		$direction = is_null($direction) ? 'LR' : strtoupper($direction);
		$shape = is_null($shape) ? 'circle' : strtolower($shape);

		$transitions = $this->transitions();

		$dot = 'digraph finite_state_machine { node [shape = '.$shape.']; rankdir='.$direction.'; ';

		foreach ($transitions as $from => $to_array)
		{
			foreach ($to_array as $to)
			{
				$dot .= $from.' -> '.$to.' []; ';
			}
		}

		$dot .= '}';

		$dot_filename = tempnam(sys_get_temp_dir(), "kohana-statemachine-");

		file_put_contents($dot_filename, $dot);

		$output = `dot -T$format $dot_filename`;

		unlink($dot_filename);

		return $output;
	}
}