<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://hana-paena.de/
 * @since      1.0.0
 *
 * @package    pss-lfsmanager
 * @subpackage pss-lfsmanager/includes
 * @author     Jan Wagner (DUDEMICH Consulting) <info@hana-paena.de>
 * 
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 */

namespace hanapaena\pss_lsfmanager;

class Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $aActions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $aActions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $aFilters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $aFilters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return 	 VOID
	 */
	public function __construct() {

		$this->aActions = [];
		$this->aFilters = [];

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    string               $sHook             The name of the WordPress action that is being registered.
	 * @param    object               $oComponent        A reference to the instance of the object on which the action is defined.
	 * @param    string               $sCallback         The name of the function definition on the $component.
	 * @param    int                  $iPriority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $iAcceptedArgs    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 * @return 	 VOID
	 */
	public function addAction( $sHook, $oComponent, $sCallback, $iPriority = 10, $iAcceptedArgs = 1 ) {
		$this->aActions = $this->add( $this->aActions, $sHook, $oComponent, $sCallback, $iPriority, $iAcceptedArgs );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    string               $sHook             The name of the WordPress filter that is being registered.
	 * @param    object               $oComponent        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $sCallback         The name of the function definition on the $component.
	 * @param    int                  $iPriority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $iAcceptedArgs    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 * @return 	 VOID
	 */
	public function addFilter( $sHook, $oComponent, $sCallback, $iPriority = 10, $iAcceptedArgs = 1 ) {
		$this->aFilters = $this->add( $this->aFilters, $sHook, $oComponent, $sCallback, $iPriority, $iAcceptedArgs );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $aHooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $sHook             The name of the WordPress filter that is being registered.
	 * @param    object               $oComponent        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $sCallback         The name of the function definition on the $component.
	 * @param    int                  $iPriority         The priority at which the function should be fired.
	 * @param    int                  $iAcceptedArgs    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $aHooks, $sHook, $oComponent, $sCallback, $iPriority, $iAcceptedArgs ) {

		$aHooks[] = [
			'hook'          => $sHook,
			'component'     => $oComponent,
			'callback'      => $sCallback,
			'priority'      => $iPriority,
			'accepted_args' => $iAcceptedArgs
		];

		return $aHooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return 	 VOID
	 */
	public function run() {

		foreach ( $this->aFilters as $aHook ) {
			add_filter( $aHook['hook'], [ $aHook['component'], $aHook['callback'] ], $aHook['priority'], $aHook['accepted_args'] );
		}

		foreach ( $this->aActions as $aHook ) {
			add_action( $aHook['hook'], [ $aHook['component'], $aHook['callback'] ], $aHook['priority'], $aHook['accepted_args'] );
		}

	}
}