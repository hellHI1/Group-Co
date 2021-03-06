<?php

namespace Group\Cache\Tests;

use Test;

class ServiceClientTest extends Test
{
    public function unitservice()
    {   
        $user = (yield service('user')->call("User\User::getUser", ['id' => 1]));
        $this->assertArrayHasKey('id', $user);
    }

    public function unitexception()
    {
        try {
            throw new \Exception("Error", 1);
        } catch (\Exception $e) {
            $this->assertEquals('Error', $e->getMessage());
        }
    }
}
