# Kohana StateMachine Module

This is a fairly simple state machine module for Kohana 3.

# Example Usage

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

// Get the current state.
$state_machine->state();

// Check if we can transition to a give state, from the current state.
$state_machine->can_transition('deleted');

// Check if we can transition to a give state, from a supplied state.
$state_machine->can_transition('deleted', 'pending');

// Transition to the supplied state
$state_machine->can_transition('active');

// Generate and return a PNG image documenting the statemachine
$state_machine->generate_diagram('png');
~~~