<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/../class/class.OllTv.php';

/**
 * Oll.tv API test class
 */
class OllTvTest extends PHPUnit_Framework_TestCase
{
    /**
     * OllTv class var
     * @var OllTv
     */
    private $_class;

    /**
     * construct alias function
     * @return void
     */
    public function setUp()
    {
        require __DIR__.'/config.php';
        // assign ollTv class to var
        $this->_class = new OllTv($config['login'], $config['pass'], $config['test'], $config['log'], $config['log_level']);
    }

    public function tearDown()
    {
        // unset class var
        $this->_class = null;
    }

    /* test functions */

    public function testEmailExists()
    {
        // test some random email
        $result = $this->_class->emailExists('test'.rand(555, 666).'@test.com');
        $this->assertEquals($result, 0);

        // get first user info
        $result = $this->_class->getUserList(0, 1);
        if (!empty($result) && isset($result[0]->email)) {
            $this->assertEquals( $this->_class->emailExists($result[0]->email), 1 );
        }
    }

    public function testAccountExists()
    {
        // test some random account
        $result = $this->_class->accountExists('test'.rand(555, 666));
        $this->assertEquals($result, 0);

        // get first user info
        $result = $this->_class->getUserList(0, 1);
        if (!empty($result) && isset($result[0]->account)) {
            $this->assertInternalType('object', $this->_class->accountExists($result[0]->account) );
        }
    }

    public function testAddUser()
    {
        // add new user with false data
        $user = array(
            'email' => '',
            'account' => '',
            'birth_date' => date('Y-m-d'),
            'gender' => 'M',
            'firstname' => 'Tester',
            'lastname' => 'Test',
            'password' => 'tester',
            'phone' => '0509009090',
            'region' => 'test',
            'receive_news' => 1,
            'send_registration_email' => 1,
            'index' => '35600'
        );
        $this->assertFalse( $this->_class->addUser($user['email'], $user['account'], $user) );

        // add user with true data
        // prepare tester account
        $account = 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6);
        $email = $account.'@test.com';
        $user = array(
            'birth_date' => date('Y-m-d'),
            'gender' => 'M',
            'firstname' => 'Tester',
            'lastname' => 'Test',
            'password' => 'tester',
            'phone' => '0509009090',
            'region' => 'test',
            'receive_news' => 1,
            'send_registration_email' => 1,
            'index' => '35600'
        );
        $this->assertInternalType('string', $this->_class->addUser($email, $account, $user) );
    }

    public function testGetUserList()
    {
        // get user list
        $this->assertInternalType('array', $this->_class->getUserList(0, 2) );
    }

    public function testChangeAccount()
    {
        // try change some random account
        $account = 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6);
        $email = $account.'@test.com';
        $this->assertEquals($this->_class->changeAccount($email, $account), 0);
    }

    public function testDeleteAccount()
    {
        // try delete some random account
        $account = 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6);
        $email = $account.'@test.com';
        $data = array(
            'account' => $account,
            'email' => $email
        );
        $this->assertEquals($this->_class->deleteAccount($data), 0);
    }

    public function testChangeEmail()
    {
        // try change some random email
        $email = 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com';
        $newEmail = 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com';
        $this->assertEquals($this->_class->changeEmail($email, $newEmail), 0);
    }

    public function testGetUserInfo()
    {
        // try get some random user info
        $account = array(
            'email' => 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com'
        );
        $this->assertEquals($this->_class->getUserInfo($account), 0);

        // get first user info
        $result = $this->_class->getUserList(0, 1);
        if (!empty($result) && isset($result[0]->account)) {
            $account = array(
                'account' => $result[0]->account
            );
            $this->assertInternalType('object', $this->_class->getUserInfo($account) );
        }
    }

    public function testChangeUserInfo()
    {
        // try change random user info
        $account = 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6);
        $email = $account.'@test.com';
        $user = array(
            'account' => $account,
            'email' => $email,
            'birth_date' => date('Y-m-d'),
            'gender' => 'M',
            'firstname' => 'Tester',
            'lastname' => 'Test',
            'password' => 'tester',
            'phone' => '0508888888',
            'region' => 'test',
            'receive_news' => 1,
            'send_registration_email' => 1,
            'index' => '35600'
        );
        $this->assertEquals($this->_class->changeUserInfo($user), 0);

        // try change existing user info
        $result = $this->_class->getUserList(0, 1);
        if (!empty($result) && isset($result[0]->account) && isset($result[0]->email)) {
            $user = array(
                'account' => $result[0]->account,
                'email' => $result[0]->email,
                'firstname' => 'Tester'
            );
            $this->assertEquals($this->_class->changeUserInfo($user), 1);
        }
    }

    public function testResetParentControl()
    {
        // try reset parent control for random user
        $account = array(
            'email' => 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com'
        );
        $this->assertEquals($this->_class->resetParentControl($account), 0);
    }

    public function testEnableBundle()
    {
        // try enable bundle to random user
        $data = array(
            'email' => 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com',
            'sub_id' => 'test-bundle',
            'type' => 'subs_free_device'
        );
        $res = $this->_class->enableBundle($data, $data['sub_id'], $data['type']);
        $this->assertEquals($res, 0);
    }

    public function testDisableBundle()
    {
        // try disable bundle to random user
        $data = array(
            'email' => 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com',
            'sub_id' => 'test-bundle',
            'type' => 'subs_free_device'
        );
        $res = $this->_class->disableBundle($data, $data['sub_id'], $data['type']);
        $this->assertEquals($res, 0);
    }

    public function testCheckBundle()
    {
        // try check bundle to random user
        $data = array(
            'email' => 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com',
            'sub_id' => 'test-bundle',
        );
        $res = $this->_class->checkBundle($data, $data['sub_id']);
        $this->assertEquals($res, 0);
    }

    public function testChangeBundle()
    {
        // try change byndle to random user
        $data = array(
            'email' => 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6).'@test.com',
            'sub_id' => 'test-bundle',
            'new_sub_id' => 'new-test-bundle',
        );
        $res = $this->_class->changeBundle($data, $data['sub_id'], $data['new_sub_id']);
        $this->assertEquals($res, 0);
    }

    public function testGetAllPurchases()
    {
        // get purchases
        $startDate = date('Y-m-d');
        $page = 1;
        $res = $this->_class->getAllPurchases($startDate, $page);
        $this->assertInternalType('object', $res);
    }

    public function testAddDevice()
    {
        // try add random device
        $data = array(
            'account' => 'testers'.substr(md5(date('Y-m-d H:i:s')), 0, 6),
            'serial_number' => 'test-serial-number',
            'mac' => 'test-mac',
            'binding_code' => 'test-code',
            'adds' => array(
                'device_type' => 'ipad'
            )
        );
        $res = $this->_class->addDevice($data['account'], $data['serial_number'], $data['mac'], $data['binding_code'], $data['adds']);
        $this->assertEquals($res, 0);
    }

    public function testDelDevice()
    {
        // try delete device with incorrect data
        $data = array(
            'serial_number' => 'test-serial-number',
            'mac' => 'test-mac',
            'account' => 'tester'.rand(3, 20),
            'type' => 'some-type'
        );
        $res = $this->_class->delDevice($data['serial_number'], $data['mac'], $data['account'], $data['type']);
        $this->assertFalse($res);
    }

    public function testDeviceExists()
    {
        // try check random device
        $data = array(
            'serial_number' => 'test-serial-number',
            'mac' => 'test-mac'
        );
        $res = $this->_class->deviceExists($data['serial_number'], $data['mac']);
        $this->assertEquals($res, 0);
    }

    public function testGetDeviceList()
    {
        // get devices list
        $res = $this->_class->getDeviceList();
        $this->assertInternalType('array', $res);
    }

    /* --end test functions */

} // end class
