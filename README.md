# Kohana StateMachine Module

This is a fairly simple state machine module for Kohana 3.

# Example Usage

## Standalone StateMachine

### Initialize a StateMachine
~~~
// Array of valid states, and the list of valid transitions from that state.
$transitions = array(
	'pending'  => array('active', 'rejected'),
	'active'   => array('deleted'),
	'rejected' => array('deleted'),
	'deleted'  => array(),
);

// Array of callbacks to be executed when transitioning to a state.
$transition_callbacks = array(
	'rejected' => array($this, 'transition_to_rejected_callback'),
);

// The intial state
$initial_state = 'pending';

// Initialize the StateMachine
$state_machine = StateMachine::factory($initial_state, array(
	'transitions'          => $transitions,
	'transition_callbacks' => $transition_callbacks,
));
~~~

### Get the current state
~~~
$state_machine->state();
~~~

### Check if we can transition to a supplied state, from the current state
~~~
$state_machine->can_transition('deleted');
~~~

### Check if we can transition to a supplied state, from a supplied state
~~~
$state_machine->can_transition('deleted', 'pending');
~~~

### Transition to the supplied state
~~~
$state_machine->can_transition('active');
~~~

### Generate and return a PNG image documenting the statemachine
~~~
$state_machine->generate_diagram('png');
~~~

## ORM_StateMachine Extension

### Initialize a StateMachine
~~~
class Model_User extends ORM_StateMachine {

	/**
	 * @var  string  Initial State
	 */
	protected $_initial_state = 'pending';

	/**
	 * @var  string  Column to use for state management
	 */
	protected $_state_column = 'state';

	/**
	 * Returns the transitions array
	 * 
	 * @return  array
	 */
	public function transitions()
	{
		return array(
			'pending'  => array('active', 'rejected'),
			'active'   => array('deleted'),
			'rejected' => array('deleted'),
			'deleted'  => array(),
		);
	}

	/**
	 * Returns the transition callbacks array
	 * 
	 * @return  array
	 */
	public function transition_callbacks()
	{
		return array(
			'rejected' => array($this, 'transition_to_rejected_callback'),
		);
	}

	/**
	 * Callback triggered when a user is rejected
	 * 
	 * @return  array
	 */
	public function transition_to_rejected_callback($state_to, $state_from)
	{
		// Do something... Maybe email a rejection letter?
		
		// Remember that this is called regardless of if the model has been
		// saved or not!
	}
}
~~~

### Get the current state
~~~
$model->state();
~~~

### Check if we can transition to a supplied state, from the current state
~~~
$model->can_transition('deleted');
~~~

### Check if we can transition to a supplied state, from a supplied state
~~~
$model->can_transition('deleted', 'pending');
~~~

### Transition to the supplied state and save.
~~~
$model->transition('active')->save();
~~~