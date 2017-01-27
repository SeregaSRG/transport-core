<?php
class Action_Location extends Action 
{

    function __construct() 
    {
        $this->domain = new Domain_Location();
        $this->responder = new Responder();
    }

    public function add()
    {
        $result = $this->domain->add();

        if ($result['isAdded']) {
            Responder::send([
               'code' => $result['code']
            ]);
        } else {
            Responder::error(
                $result['code']
            );
        }
        
        exit();
    }

    public function get()
    {
        $result = $this->domain->get();
        
        if ($result['isGot']) {
            Responder::send([
                'array' => json_encode($result['array'])
            ]);
        } else {
            Responder::error(
                $result['code']
            );
        }
    }
}