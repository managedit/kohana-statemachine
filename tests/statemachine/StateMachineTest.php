<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');

/**
 * 
 *
 * @group statemachine
 *
 * @package    StateMachine
 * @category   Tests
 * @author     Managed I.T.
 * @copyright  (c) 2012 Managed I.T.
 * @license    http://labs.managedit.ie/license
 */
class StateMachine_StateMachineTest extends Unittest_TestCase
{
	protected function get_statemachine_instance()
	{
		$transitions = array(
			'pending'  => array('active', 'rejected'),
			'active'   => array('deleted'),
			'rejected' => array('deleted'),
			'deleted'  => array(),
		);

		$transition_callbacks = array(

		);

		$initial_state = 'pending';

		return StateMachine::factory($initial_state, array(
			'transitions'          => $transitions,
			'transition_callbacks' => $transition_callbacks,
		));
	}

	/**
	 * Tests the StateMachine::factory() method
	 *
	 * @test
	 */
	public function test_factory()
	{
		$transitions = array(
			'pending'  => array('active', 'rejected'),
			'active'   => array('deleted'),
			'rejected' => array('deleted'),
			'deleted'  => array(),
		);

		$transition_callbacks = array(

		);

		$initial_state = 'pending';

		return StateMachine::factory($initial_state, array(
			'transitions'          => $transitions,
			'transition_callbacks' => $transition_callbacks,
		));

		$this->assertSame($initial_state, $sm->state());
		$this->assertSame($transitions, $sm->transitions());
		$this->assertSame($transition_callbacks, $sm->transition_callbacks());
	}

	/**
	 * Tests the StateMachine::can_transition() method
	 *
	 * @test
	 */
	public function test_can_transition()
	{
		$sm = $this->get_statemachine_instance();

		$this->assertTrue($sm->can_transition('active'));
		$this->assertTrue($sm->can_transition('rejected'));
		$this->assertFalse($sm->can_transition('deleted'));
	}

	/**
	 * Tests the StateMachine::transition() method
	 *
	 * @test
	 */
	public function test_transition_valid()
	{
		$sm = $this->get_statemachine_instance();

		$this->assertSame('pending', $sm->state());

		$sm->transition('active');

		$this->assertSame('active', $sm->state());

		$sm->transition('deleted');

		$this->assertSame('deleted', $sm->state());
	}

	/**
	 * Tests the StateMachine::transition() method fails when asked to
	 * perform an invalid transition.
	 *
	 * @test
	 * @expectedException StateMachine_InvalidTransition_Exception
	 */
	public function test_transition_invalid()
	{
		$sm = $this->get_statemachine_instance();

		$this->assertSame('pending', $sm->state());

		$sm->transition('deleted');
	}

	/**
	 * Tests the StateMachine::transition() method does not change the state
	 * when a transition fails.
	 *
	 * @test
	 */
	public function test_transition_invalid2()
	{
		$sm = $this->get_statemachine_instance();

		$this->assertSame('pending', $sm->state());

		$exception_caught = FALSE;

		try
		{
			$sm->transition('deleted');
		}
		catch (StateMachine_InvalidTransition_Exception $e)
		{
			$exception_caught = TRUE;
		}

		$this->assertSame('pending', $sm->state());
	}

	/**
	 * Tests the StateMachine::transition() method fails when asked to
	 * perform an transition to the current state.
	 *
	 * @test
	 * @expectedException StateMachine_InvalidTransition_Exception
	 */
	public function test_transition_same()
	{
		$sm = $this->get_statemachine_instance();

		$sm->transition($sm->state());
	}
}