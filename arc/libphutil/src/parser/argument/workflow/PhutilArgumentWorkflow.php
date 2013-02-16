<?php

/**
 * Used with @{class:PhutilArgumentParser} to build command line tools which
 * operate in several modes, called "workflows", like `git`, `svn`, `arc`,
 * `apt-get`. For example, you might build a simple calculator like this:
 *
 *   name=scripts/example/calculator.php
 *   $args = new PhutilArgumentParser($argv);
 *   $args->setTagline('simple calculator example');
 *   $args->setSynopsis(<<<EOHELP
 *   **calculator.php** __op__ __n__ ...
 *   Perform a calculation.
 *   EOHELP
 *   );
 *
 *   $add_workflow = id(new PhutilArgumentWorkflow())
 *     ->setName('add')
 *     ->setExamples('**add** __n__ ...')
 *     ->setSynopsis('Compute the sum of a list of numbers.')
 *     ->setArguments(
 *       array(
 *         array(
 *           'name'       => 'numbers',
 *           'wildcard'   => true,
 *         ),
 *       ));
 *
 *   $mul_workflow = id(new PhutilArgumentWorkflow())
 *     ->setName('mul')
 *     ->setExamples('**mul** __n__ ...')
 *     ->setSynopsis('Compute the product of a list of numbers.')
 *     ->setArguments(
 *       array(
 *         array(
 *           'name'       => 'numbers',
 *           'wildcard'   => true,
 *         ),
 *       ));
 *
 *   $flow = $args->parseWorkflows(
 *     array(
 *       $add_workflow,
 *       $mul_workflow,
 *       new PhutilHelpArgumentWorkflow(),
 *     ));
 *
 *   $nums = $args->getArg('numbers');
 *   if (empty($nums)) {
 *     echo "You must provide one or more numbers!\n";
 *     exit(1);
 *   }
 *
 *   foreach ($nums as $num) {
 *     if (!is_numeric($num)) {
 *       echo "Number '{$num}' is not numeric!\n";
 *       exit(1);
 *     }
 *   }
 *
 *   switch ($flow->getName()) {
 *     case 'add':
 *       echo array_sum($nums)."\n";
 *       break;
 *     case 'mul':
 *       echo array_product($nums)."\n";
 *       break;
 *   }
 *
 * You can also subclass this class and return `true` from
 * @{method:isExecutable}. In this case, the parser will automatically select
 * your workflow when the user invokes it.
 *
 * @stable
 * @concrete-extensible
 * @group console
 */
class PhutilArgumentWorkflow {

  private $name;
  private $synopsis;
  private $specs = array();
  private $examples;

  final public function __construct() {
    $this->didConstruct();
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function getName() {
    return $this->name;
  }

  final public function setExamples($examples) {
    $this->examples = $examples;
    return $this;
  }

  final public function getExamples() {
    if (!$this->examples) {
      return "**".$this->name."**";
    }
    return $this->examples;
  }

  final public function setSynopsis($synopsis) {
    $this->synopsis = $synopsis;
    return $this;
  }

  final public function getSynopsis() {
    return $this->synopsis;
  }

  final public function setArguments(array $specs) {
    $specs = PhutilArgumentSpecification::newSpecsFromList($specs);
    $this->specs = $specs;
    return $this;
  }

  final public function getArguments() {
    return $this->specs;
  }

  protected function didConstruct() {
    return null;
  }

  public function isExecutable() {
    return false;
  }

  public function execute(PhutilArgumentParser $args) {
    throw new Exception("This workflow isn't executable!");
  }

  /**
   * Normally, workflow arguments are parsed fully, so unexpected arguments will
   * raise an error. You can return `true` from this method to parse workflow
   * arguments only partially. This will allow you to manually parse remaining
   * arguments or delegate to a second level of workflows.
   *
   * @return bool True to partially parse workflow arguments (default false).
   */
  public function shouldParsePartial() {
    return false;
  }

}
