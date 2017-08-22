<?php
//debugging file
class antiCall
{
  public function test()
  {
    return $this->get_calling_class();
    return debug_backtrace();
  }

  private function get_calling_class(): string
  {
// tnx https://stackoverflow.com/a/6927569/7126351
    $trace = debug_backtrace();
    $class = $trace[1]['class'];
    for($i = 1; $i < count($trace); $i++){
      if(isset($trace[$i])) if($class != $trace[$i]['class']) return $trace[$i]['class'];
    }
    return "null";
  }
}

class Tester
{
  public function test()
  {
    $test = new class
    {
      public function test(antiCall $anticall) { return $anticall->test(); }
    };
    print_r($test->test(new anticall));
  }
}
$temp_file = tempnam(sys_get_temp_dir(),'');
echo $temp_file;
$class = '<?php return new class
{
  public function test()
  {
    $test = new class
    {
      public function test(antiCall $anticall) { return $anticall->test(); }
    };
    print_r($test->test(new anticall));
  }
};';
file_put_contents($temp_file,$class);
$class = include($temp_file);
//var_dump($class);
$class->test();