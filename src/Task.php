<?php

namespace TechDesign\TaskProcessor;

use TechDesign\TaskProcessor\Helper\Printer;

/**
 * Class Task
 * @package TechDesign\TaskProcessor
 * @method $this src(...$args)
 * @method $this less(...$args)
 * @method $this dest(...$args)
 * @method $this minify(...$args)
 * @method $this print(...$args)
 * @method $this rename(...$args)
 * @method $this concat(...$args)
 * @method $this log(...$args)
 * @method $this sass(...$args)
 */
class Task extends ThreadProxy implements TaskInterface
{
	protected $name;
	protected $started;
	protected $ended;

	/** @var Action[]|callable[] */
	protected $callableChain = [];
	/** @var \Composer\Autoload\ClassLoader */
	protected $classLoader;

	/**
	 * @param $action
	 * @return $this
	 */
	public function schedule($action)
	{
		$this->callableChain[] = $action;
		return $this;
	}

	public function __construct($name, $callable = null)
	{
		$this->name = $name;
		if (is_callable($callable)) {
			call_user_func($callable, $this);
		}
	}

	public function run()
	{
		$this->awake = true;
		$this->started = microtime(true);

		if (!class_exists('Printer')) {
			$this->classLoader->register();
		}

		Printer::prnt(sprintf('Task \'%s\' started.' ,  $this->name), Printer::FG_CYAN);

		foreach ($this->callableChain as $action) {
			if (is_callable($action)) {
				$result = $action(isset($result) ? $result : null);
			} elseif ($action instanceof Action) {
				$result = $action->run(isset($result) ? $result : null);
			} else {
				throw new \Exception('Task must be a function or instance of Action!');
			}
		}

		$this->ended = microtime(true);
		Printer::prnt(sprintf('Task \'%s\' ended, took: %s', $this->name,  $this->getTimeSpent()), Printer::FG_LIGHT_CYAN);

		$this->awake = false;
		return true;
	}
	
	public function __call($name, $arguments)
	{
		$class = 'TechDesign\TaskProcessor\Action\\' . ucfirst($name) . 'Action';
		if (class_exists($class)) {
			return $this->schedule(new $class(...$arguments));
		}
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	public function getTimeSpent()
	{
		$spent = ($this->ended - $this->started);
		if ($spent < 1) {
			return number_format($spent * 1000) . 'ms';
		} else {
			return number_format($spent, 3) . 's';
		}
	}

	/**
	 * If multithreading is enabled then we need to have composer autoloader class inside child threads
	 *
	 * @param $classLoader
	 */
	public function setClassLoader($classLoader)
	{
		$this->classLoader = $classLoader;
	}
}