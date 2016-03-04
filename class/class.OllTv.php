<?php

/**
 * Oll.tv API class
 * @author Prakapas Andriy <prakapas@general-servers.com>
 * @copyright 2016 GeneralServers LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @version 2.1.0 - for same ispAPI version 2.1.0
 * @link  https://general-servers.com
 * @link  https://github.com/General-Servers/Oll.tv
 */
class OllTv
{

    const OTV_URL = 'http://oll.tv/'; // main production url
    const OTV_URL_DEV = 'http://dev.oll.tv/'; // development url for test
    const OTV_URL_API = 'ispAPI'; // main api url
    const OTV_URL_AUTH = 'auth2'; // auth url

    /**
     * client login
     * set in consctruct
     * @var string
     */
    private $login;
    /**
     * client password
     * set in consctruct
     * @var string
     */
    private $password;
    /**
     * client hash
     * hash return ispAPi; usign in all requests
     * @var string
     */
    private $hash;
    /**
     * result what return api
     * @var string
     */
    private $result;
    /**
     * api url
     * @var string
     */
    private $url;


    /* log vars */

    /**
     * path to log file
     * @var string
     */
    private $log;
    /**
     * log level variable
     * 0 - not show any messages
     * 1 - only Errors : DEFAULT
     * 2 - Errors, Warnings
     * 3 - Errors, Warnings and Infos
     * @var integer
     */
    private $logLevel = 1;
    /**
     * log message type\
     * related with $logLevel var
     * @var array
     */
    private $logType = array(
        0 => '',
        1 => 'Error',
        2 => 'Warning',
        3 => 'Info'
    );

    /* --end log vars */


    /* error vars */

    /**
     * api errors array
     * key - error status
     * @var array
     */
    private $errors = array(
        109 => array(
            'message' => 'Hash expired',
            'description' => 'Время действия хеша истекло или хеш не верен'
        ),
        110 => array(
            'message' => 'Authorization missed',
            'description' => 'Хеш не указан'
        ),
        111 => array(
            'message' => 'Auth failed',
            'description' => 'Неверный логин или пароль'
        ),
        112 => array(
            'message' => 'Login empty',
            'description' => 'Не указан логин'
        ),
        113 => array(
            'message' => 'Password empty',
            'description' => 'Не указан пароль'
        ),
        115 => array(
            'message' => 'Email already exists',
            'description' => 'Указанный имейл уже есть в БД'
        ),
        116 => array(
            'message' => 'Email validation failed',
            'description' => 'Формат указанного имейла неверен'
        ),
        117 => array(
            'message' => 'Result user account does not match provided',
            'description' => 'Не указан аккаунт или он не совпадает с аккаунтом на который подвязано устройство'
        ),
        119 => array(
            'message' => 'Device with provided mac or/and serial number already exist',
            'description' => 'Устройство с указанным мак-адресом и/или серийным номером уже присуствует в БД и за кем-то закреплено'
        ),
        120 => array(
            'message' => 'Wrong date format',
            'description' => "Неверный формат даты"
        ),
        200 => array(
            'message' => 'Required fields missed',
            'description' => "Остутствуют необходимые параметры"
        ),
        201 => array(
            'message' => 'Field email is required',
            'description' => "Отсутствует необходимый параметр email"
        ),
        203 => array(
            'message' => 'Neither mac nor serial_number was found in your request',
            'description' => "Отсутствуют параметры mac и serial_number"
        ),
        205 => array(
            'message' => 'Field new_email is required',
            'description' => "Отсутствует необходимый параметр new_email"
        ),
        301 => array(
            'message' => 'Registration failed. Contact technical support',
            'description' => "Ошибка добавления устройства пользователю или регистрации нового пользователя"
        ),
        302 => array(
            'message' => 'Wrong MAC address',
            'description' => "Неверный формат мак-адреса"
        ),
        303 => array(
            'message' => 'Wrong Serial number',
            'description' => "Неверный формат серийного номера"
        ),
        304 => array(
            'message' => 'Invalid binding code',
            'description' => "Неверный код привязки устройства"
        ),
        305 => array(
            'message' => 'No devices can be binded by this code',
            'description' => "Достигнут лимит кол-ва устройств, которые можно привязать по указанному коду привязки"
        ),
        404 => array(
            'message' => "Account not found",
            'description' => "Пользователь не найден в БД"
        ),
        405 => array(
            'message' => "Not eligible device_type",
            'description' => "Недопустимое значение в параметре device_type"
        ),
        406 => array(
            'message' => "Device not found in our DB",
            'description' => "Устройство не найдено в БД или оно отвязано от пользователя",
        ),
        407 => array(
            'message' => "Subscription not found",
            'description' => "Подписка, указанная в параметрах sub_id, new_sub_id или old_sub_id, не найдена"
        ),
        408 => array(
            'message' => "Subscription order violation",
            'description' => "Нарушение очерёдности отключения или включения услуги согласно подписке"
        ),
        501 => array(
            'message' => "Access denied",
            'description' => "Устройство привязано к пользователю другого провайдера"
        ),
        504 => array(
            'message' => "User already deactivated",
            'description' => "Услуга была уже выключена ранее"
        ),
        505 => array(
            'message' => "User is attached to another operator",
            'description' => "Пользователь привязан к другому провайдеру"
        ),
        506 => array(
            'message' => "Account is not active",
            'description' => "Аккаунт пользователя не активен"
        )
    );

    /**
     * last message array
     * can get last message by type without log
     * @var array
     */
    private $lastMessage = array(
        0 => '', // empty
        1 => '', // last error message
        2 => '', // last warning message
        3 => '' // last info message
    );

    /* --end error vars */


    /**
     * set last message
     * @param string $message message text
     * @param integer $type    message type
     * @return  boolean
     */
    private function _setLastMessage($message, $type)
    {
        // verify arguments
        if (!is_string($message) || !is_numeric($type)) return false;
        // verify type in last message array
        if (!isset($this->lastMessage[ $type ])) return false;
        // assign message
        $this->lastMessage[ $type ] = $message;
        return true;
    }

    /**
     * method write to log file
     * @param  string $message message text
     * @param  integer $type    type of message
     * @return boolean
     */
    private function _toLog($message, $type = 1)
    {
        // verify arguments
        if (empty($message) || !is_string($message)) {
            return false;
        }
        // set last message variable
        $this->_setLastMessage($message, $type);

        // verify log level
        if ($this->logLevel == 0 || $type == 0 || !isset($this->logType[ $type ])) return false;
        if ($type > $this->logLevel) {
            return false;
        }

        // verify path to log file and writable file access
        if (!is_writable($this->log)) {
            return false;
        }

        // prepare message type
        $type = $this->logType[ $type ].': ';
        // append write to file and get result
        $res = file_put_contents($this->log, date('Y-m-d H:i:s').' '.$type.$message."\n", FILE_APPEND | LOCK_EX);
        return (bool)$res;
    }

    /**
     * method prepare default account data
     * @param  array $params parametters array
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @return array
     */
    private function _prepareAccountDefaultData($params)
    {
        //init return array
        $args = array();

        // try find `account`
        if (!empty($params['account'])) {
            $args = array(
                'account' => $params['account']
            );
        }
        else {
            $this->_toLog('['.__FUNCTION__.'] - `account` not found in parametter array;', 3);
        }

        // try find `email`
        if (!empty($params['email']) && filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $args = array(
                'email' => $params['email']
            );
        }
        else {
            $this->_toLog('['.__FUNCTION__.'] - `email` not found in parametter array or `email` is not valid email;', 3);
        }

        // try find `id`
        if (!empty($params['id']) && is_numeric($params['id'])) {
            $args = array(
                'id' => $params['id']
            );
        }
        else {
            $this->_toLog('['.__FUNCTION__.'] - `id` not found in parametter array;', 3);
        }

        // try find `ds_account`
        if (!empty($params['ds_account'])) {
            $args = array(
                'ds_account' => $params['ds_account']
            );
        }
        else {
            $this->_toLog('['.__FUNCTION__.'] - `ds_account` not found in parametter array;', 3);
        }

        return $args;
    }

    /**
     * method prepare purchase type
     * @param  string $type
     * accept parametters:
     * subs_free_device — new contract - 24 months and equipment for 1 uah
     * subs_buy_device — new contract - buy equipment
     * subs_rent_device — new contract - rent equipment
     * subs_no_device — new contract - no equipment
     * subs_renew — restore the current contract
     *
     * @return mixed  string type or false
     */
    private function _preparePurchaseType($type)
    {
        // init types array
        $typeArray = array('subs_free_device', 'subs_buy_device', 'subs_rent_device', 'subs_no_device', 'subs_renew');
        // verify argument
        if (empty($type) || !in_array($type, $typeArray)) {
            $this->_toLog('['.__FUNCTION__.'] - purchase type is not correct');
            return false;
        }
        // return type
        return $type;
    }

    /**
     * method prepare device type
     * @param  string $type
     * accept parametters:
     * device_free — new contract - 24 months and equipment for 1 uah
     * device_buy — new contract - buy equipment
     * device_rent — new contract - rent equipment
     * device_change — service replace the current equipment
     *
     * @return mixed   string type or false
     */
    private function _prepareDeviceType($type)
    {
        // init types array
        $typeArray = array('device_free', 'device_buy', 'device_rent', 'device_change');
        // verify argument
        if (empty($type) || !in_array($type, $typeArray)) {
            $this->_toLog('['.__FUNCTION__.'] - device type is not correct');
            return false;
        }
        // return type
        return $type;
    }

    /**
     * method read result from API
     * @param  string $result result string
     * @return boolean
     */
    private function _readResult($result)
    {
        // verify result var
        if (empty($result) || !is_string($result)) {
            $this->_toLog('['.__FUNCTION__.'] - cannot read result; maybe empty or not string value: '.var_export($result, true).';');
            return false;
        }

        // log info
        $this->_toLog('['.__FUNCTION__.'] - read result: '.var_export($result, true).';', 3);

        // decode from string
        $json = json_decode($result);
        // check json errors
        $error = json_last_error_msg();
        if (!$json || strtolower($error) !== 'no error') {
            $this->_toLog('['.__FUNCTION__.'] - has json error: '.$error.';');
            return false;
        }
        // all looks good
        $this->result = $json;
        return true;
    }

    /**
     * method return API result
     * @return mixed object or false
     */
    private function _return()
    {
        // verify result
        if (empty($this->result) || !is_object($this->result) || !isset($this->result->status)) {
            $this->_toLog('['.__FUNCTION__.'] - API result is bad type: '.var_export($this->result, true).';');
            return false;
        }

        // log warnings if is
        if (!empty($this->result->warnings)) {
            $this->_toLog('['.__FUNCTION__.'] - API warnngs: '.(string)$this->result->warnings.';');
        }

        // verify result status
        if ($this->result->status !== 0) {
            // prepare error
            $error = '';

            if (!isset($this->errors[ $this->result->status ])) {
                if (isset($this->result->message)) {
                    $error = $this->result->message;
                }
                else {
                    $error = '['.__FUNCTION__.'] - API return false status';
                }
            }
            else {
                // prepare error string
                $error = 'Code #'.$this->result->status;
                $error .= ' '.$this->errors[ $this->result->status ]['message'];
                $error .= ' - '.$this->errors[ $this->result->status ]['description'];
            }

            // log error
            $this->_toLog($error);
            // return flase
            return false;
        }
        // all looks OK
        else {
            // log action
            $this->_toLog('['.__FUNCTION__.'] - return API result', 3);
            // return result
            return $this->result;
        }
    }

    /**
     * method create API url
     * @param  boolean $testMode testing mode flag
     * @return boolean
     */
    private function _createUrl($testMode = false)
    {
        // verify argument
        if (!is_bool($testMode)) {
            $this->_toLog('['.__FUNCTION__.'] - $testMode not is boolean type; set to default `false`;', 2);
            $testMode = false;
        }

        // assign url
        if ($testMode) {
            $this->_toLog('['.__FUNCTION__.'] - $testMode is `true`; set url to: '.self::OTV_URL_DEV, 3);
            $this->url = self::OTV_URL_DEV;
        }
        else {
            $this->_toLog('['.__FUNCTION__.'] - $testMode is `false`; set url to: '.self::OTV_URL, 3);
            $this->url = self::OTV_URL;
        }
        // add API link to url
        $this->url .= self::OTV_URL_API;

        return true;
    }

    /**
     * method authenticate to API and set hash
     * @return boolean
     */
    private function _auth()
    {
        // verify login adn pass
        if (empty($this->login)) {
            $this->_toLog('['.__FUNCTION__.'] - login is empty;');
            return false;
        }
        if (empty($this->password)) {
            $this->_toLog('['.__FUNCTION__.'] - password is empty;');
            return false;
        }

        // try to connect
        $res = $this->_connect(self::OTV_URL_AUTH, array(
            'login' => $this->login,
            'password' => $this->password
        ));
        // verify result and hash
        if (!$res || empty($this->result->hash)) {
            $this->_toLog('['.__FUNCTION__.'] - false authenticate;');
            return false;
        }

        // assign hash
        $this->hash = $this->result->hash;
        return true;
    }

    /**
     * method run curl to API
     * @param  string $method API method
     * @param  array $args   POST arguments
     * @return mixed    false or object
     */
    private function _connect($method, $args)
    {
        // verify arguments
        if (!is_string($method)) {
            $this->_toLog('['.__FUNCTION__.'] - `$method` must to be string;');
            return false;
        }
        if (!is_array($args)) {
            $this->_toLog('['.__FUNCTION__.'] - `$args` is not array;');
            return false;
        }

        // add hash to arguments
        if ($method !== self::OTV_URL_AUTH) {
            $args['hash'] = $this->hash;
        }

        // create curl link
        $curlLink = $this->url.'/'.$method;

        // prepare data for log
        $logArgs = $args;
        // disable password - pass not show in logs!
        if (!empty($logArgs['password'])) {
            $logArgs['password'] = '*************';
        }
        // lof info
        $this->_toLog('['.__FUNCTION__.'] - send request to url: '.$curlLink.';', 3);
        $this->_toLog('['.__FUNCTION__.'] - send request data: '.var_export($logArgs, true).';', 3);

        // create and send curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curlLink);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/html;charset=utf-8'));

        // send post if need
        if (!empty($args)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // read result
        $this->_readResult($response);
        // run return
        return $this->_return();
    }


    /**
     * __construct
     * @depends _createUrl, _auth
     * @param string  $login    client login
     * @param string  $pass     client password
     * @param boolean $testMode test mode flag
     * @param string  $log      path to log file
     */
    public function __construct($login, $pass, $testMode = false, $log = '', $logLevel = 1)
    {
        // assign log file
        $this->log = $log;
        // assign log level
        $this->logLevel = ($logLevel < 0 || $logLevel > 3) ? 1 : (int)$logLevel;

        // assign login and password
        $this->login = $login;
        $this->password = $pass;
        // create api url
        $this->_createUrl($testMode);
        // try connect
        $this->_auth();
    }

    /**
     * magic method __call
     * commented for better times
     *
     * @depends _connect
     * @param  string $method class method
     * @param  array $args   function arguments
     * @return mixed
     */
    // public function __call($method, $args = array())
    // {
    //     return $this->_connect($method, $args);
    // }

    /**
     * method return last message
     * @param  integer $type message type
     * @return mixed        false | string
     */
    public function getLastMessage($type = 1)
    {
        // verify arguments
        if (!is_numeric($type) || !isset($this->lastMessage[ $type ])) return false;
        // return message
        return $this->lastMessage[ $type ];
    }


    /* API functions */

    /* users functions */

    /**
     * method verify email
     * @param  string $email user email
     * @return mixed    false - smth is wrong; 0 - not exist; 1 - exist
     */
    public function emailExists($email)
    {
        // verify argument
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_toLog('['.__FUNCTION__.'] - `$email` is not valid email address;');
            return false;
        }
        // prepare arguments array
        $args = array(
            'email' => $email
        );
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /**
     * method return account object
     * @param  string $account user account in provider base
     * @return mixed          return object - user account or 0
     */
    public function accountExists($account)
    {
        // verify argument
        if (empty($account)) {
            $this->_toLog('['.__FUNCTION__.'] - `$account` is empty;');
            return 0;
        }
        // prepare arguments array
        $args = array(
            'account' => $account
        );
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return 0;
        }
    }

    /**
     * method add new user
     * @param string $email     user email
     * @param string $account   user account in provider base
     * @param array  $addParams additional params
     * birth_date (YYYY-MM-DD or DD.MM.YYYY)
     * gender (M or F, default: M)
     * firstname (default: «Гость»/«Гостья»)
     * password (will generate automatic - if not set, must be longer 8 chars)
     * lastname
     * phone (example: 0501234567)
     * region
     * receive_news (value 1 or 0, default: 1)
     * send_registration_email (whether send the user a registration message, value 1 or 0, default: 1)
     * index (zip code or other identifier binding regional subscriber)
     *
     * @return  mixed  false - smth is wrong; string - user ID
     */
    public function addUser($email, $account, $addParams = array())
    {
        // verify arguments
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_toLog('['.__FUNCTION__.'] - `$email` is not valid email address;');
            return false;
        }
        // verify argument
        if (empty($account)) {
            $this->_toLog('['.__FUNCTION__.'] - `$account` is empty;');
            return false;
        }

        // prepare arguments array
        $args = array(
            'email' => $email,
            'account' => $account,
        );
        // prepare birth date
        if (!empty($addParams['birth_date']) && strtotime($addParams['birth_date'])) {
            $args['birth_date'] = date('Y-m-d', strtotime($addParams['birth_date']));
        }
        // prepare gender
        if (!empty($addParams['gender']) && in_array($addParams['gender'], array('M', 'F'))) {
            $args['gender'] = $addParams['gender'];
        }
        // prepare firstname
        if (!empty($addParams['firstname'])) {
            $args['firstname'] = $addParams['firstname'];
        }
        else {
            $args['firstname'] = (isset($args['gender']) && $args['gender'] === 'F') ? 'Гостья' : 'Гость';
        }
        // prepare lastname
        if (!empty($addParams['lastname'])) {
            $args['lastname'] = $addParams['lastname'];
        }

        // prepare password
        if (!empty($addParams['password']) && strlen($addParams['password']) >= 8) {
            $args['password'] = $addParams['password'];
        }
        else {
            $this->_toLog('['.__FUNCTION__.'] - user password is empty or shorter than 8 chars; generate automatic;', 2);
        }

        // prepare phone
        if (!empty($addParams['phone']) && is_string($addParams['phone']) &&
            preg_match('/^\d{10,}$/', $addParams['phone']) && $addParams['phone']{0} === '0'
        ) {
            $args['phone'] = $addParams['phone'];
        }
        // prepare region
        if (!empty($addParams['region'])) {
            $args['region'] = $addParams['region'];
        }
        // prepare receive_news
        if (!empty($addParams['receive_news']) && is_numeric($addParams['receive_news'])) {
            $args['receive_news'] = (int)(bool)$addParams['receive_news'];
        }
        // prepare send_registration_email
        if (!empty($addParams['send_registration_email']) && is_numeric($addParams['send_registration_email'])) {
            $args['send_registration_email'] = (int)(bool)$addParams['send_registration_email'];
        }
        // prepare index
        if (!empty($addParams['index'])) {
            $args['index'] = $addParams['index'];
        }

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return 0;
        }
    }

    /**
     * method return user list bound to provider
     * @param  integer $offset offset
     * @param  integer $limit  limit
     * @return array
     */
    public function getUserList($offset = 0, $limit = 1000)
    {
        // verify arguments
        if (!is_numeric($offset) || $offset < 0) {
            // log info
            $this->_toLog('['.__FUNCTION__.'] - `$offset` is incorrect, set to 0;', 2);
            $offset = 0;
        }
        if (!is_numeric($limit) || $limit <= 0 || $limit > 1000) {
            // log info
            $this->_toLog('['.__FUNCTION__.'] - `$limit` is incorrect, set to default 1000;', 2);
            $limit = 1000;
        }
        // prepare arguments array
        $args = array(
            'offset' => $offset,
            'limit' => $limit
        );
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return array();
        }
    }

    /**
     * method set provider account and bind user to account
     * @param  string $email   user email
     * @param  string $account account
     * @return mixed          false or integer - status
     */
    public function changeAccount($email, $account)
    {
        // verify arguments
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_toLog('['.__FUNCTION__.'] - `$email` is not valid email address;');
            return false;
        }
        // verify argument
        if (empty($account)) {
            $this->_toLog('['.__FUNCTION__.'] - `$account` is empty;');
            return false;
        }
        // prepare arguments array
        $args = array(
            'email' => $email,
            'account' => $account,
        );
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->status)) {
            return $res->status;
        }
        else {
            return false;
        }
    }

    /**
     * method unbind user from provider by params
     * @param  array $params
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @return mixed          false or integer - status
     */
    public function deleteAccount($params)
    {
        // verify parametters
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }

        // prepare arguments array
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->status)) {
            return $res->status;
        }
        else {
            return false;
        }
    }

    /**
     * change user email
     * @param  string $email    current user email
     * @param  string $newEmail new user email
     * @return mixed           false or integer - status
     */
    public function changeEmail($email, $newEmail)
    {
        // verify arguments
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_toLog('['.__FUNCTION__.'] - `$email` is not valid email address;');
            return false;
        }
        if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $this->_toLog('['.__FUNCTION__.'] - `$newEmail` is not valid email address;');
            return false;
        }
        // prepare arguments array
        $args = array(
            'email' => $email,
            'new_email' => $newEmail
        );
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->status)) {
            return $res->status;
        }
        else {
            return false;
        }
    }

    /**
     * method return user information
     * @param array $params
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @return mixed     false or object
     */
    public function getUserInfo($params)
    {
        // verify parametters
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }

        // prepare arguments array
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /**
     * method return user information
     * @param array $params parametters array
     * accept parametters:
     *
     * one from following is required:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * additionals:
     * birth_date (YYYY-MM-DD or DD.MM.YYYY)
     * gender (M or F, default: M)
     * password (must be longer than 8 chars)
     * firstname
     * lastname
     * phone
     * region
     * index (zip code or other identifier binding regional subscriber)
     *
     * @return mixed   false or integer - status
     */
    public function changeUserInfo($params)
    {
        // verify parametters
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }

        // prepare arguments array
        // require parametters
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }

        // additional parametters
        // prepare birth date
        if (!empty($addParams['birth_date']) && strtotime($addParams['birth_date'])) {
            $args['birth_date'] = date('Y-m-d', strtotime($addParams['birth_date']));
        }
        // prepare gender
        if (!empty($addParams['gender']) && in_array($addParams['gender'], array('M', 'F'))) {
            $args['gender'] = $addParams['gender'];
        }
        // prepare firstname
        if (!empty($addParams['firstname'])) {
            $args['firstname'] = $addParams['firstname'];
        }
        else {
            $args['firstname'] = (isset($args['gender']) && $args['gender'] === 'F') ? 'Гостья' : 'Гость';
        }
        // prepare lastname
        if (!empty($addParams['lastname'])) {
            $args['lastname'] = $addParams['lastname'];
        }

        // prepare password
        if (!empty($addParams['password']) && strlen($addParams['password']) >= 8) {
            $args['password'] = $addParams['password'];
        }

        // prepare phone
        if (!empty($addParams['phone']) && is_string($addParams['phone']) &&
            preg_match('/^\d{10,}$/', $addParams['phone']) && $addParams['phone']{0} === '0'
        ) {
            $args['phone'] = $addParams['phone'];
        }
        // prepare region
        if (!empty($addParams['region'])) {
            $args['region'] = $addParams['region'];
        }
        // prepare index
        if (!empty($addParams['index'])) {
            $args['index'] = $addParams['index'];
        }

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /**
     * method reset parent control
     * @param array $params
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @return mixed         false or integer - status
     */
    public function resetParentControl($params)
    {
        // verify parametters
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }

        // prepare arguments array
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->status)) {
            return $res->status;
        }
        else {
            return false;
        }
    }

    /* -end users functions */


    /* purchases functions */

    /**
     * method enable bundle
     * @param array $params
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @param  string $subId purchase identificator
     * @param  string $type
     * accept parametters:
     * subs_free_device — new contract - 24 months and equipment for 1 uah
     * subs_buy_device — new contract -  - buy equipment
     * subs_rent_device — new contract - rent equipment
     * subs_no_device — new contract - no equipment
     * subs_renew — restore the current contract
     *
     * @return mixed       false or integer
     */
    public function enableBundle($params, $subId, $type)
    {
        // verify arguments
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }
        if (empty($subId)) {
            $this->_toLog('['.__FUNCTION__.'] - `$subId` is empty');
            return false;
        }
        if (empty($type)) {
            $this->_toLog('['.__FUNCTION__.'] - `$type` is empty');
            return false;
        }

        // prepare arguments array
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }

        // assign subId
        $args['sub_id'] = $subId;

        // prepare type
        $type = $this->_preparePurchaseType($type);
        // verify type
        if (!$type) {
            $this->_toLog('['.__FUNCTION__.'] - `$type` is not correct;');
            return false;
        }
        // assign type
        $args['type'] = $type;

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /**
     * method disable bundle
     * @param array $params
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @param  string $subId description
     * @param  string $type
     * accept parametters:
     * subs_free_device — new contract - 24 months and equipment for 1 uah
     * subs_buy_device — new contract -  - buy equipment
     * subs_rent_device — new contract - rent equipment
     * subs_no_device — new contract - no equipment
     * subs_renew — restore the current contract
     *
     * @return mixed      false or integer
     */
    public function disableBundle($params, $subId, $type)
    {
        // verify parametters
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }
        if (empty($subId)) {
            $this->_toLog('['.__FUNCTION__.'] - `$subId` is empty');
            return false;
        }
        if (empty($type)) {
            $this->_toLog('['.__FUNCTION__.'] - `$type` is empty');
            return false;
        }

        // prepare arguments array
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }

        // assign subId
        $args['sub_id'] = $subId;

        // prepare type
        $type = $this->_preparePurchaseType($type);
        // verify type
        if (!$type) {
            $this->_toLog('['.__FUNCTION__.'] - `$type` is not correct;');
            return false;
        }
        // assign type
        $args['type'] = $type;

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /**
     * method check user bundle
     * @param array $params
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @param string $subId description
     *
     * @return mixed     false or integer
     */
    public function checkBundle($params, $subId)
    {
        // verify parametters
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }
        if (empty($subId)) {
            $this->_toLog('['.__FUNCTION__.'] - `$subId` is empty');
            return false;
        }

        // prepare arguments array
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }

        // assign subId
        $args['sub_id'] = $subId;

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /**
     * method change use bundle-subscription
     * @param  array $params   account params array
     * accept parametters:
     * account OR email OR id OR ds_account
     * examples:
     * array('account' => 'test')
     * array('email' => 'test@test.com')
     * array('id' => 42)
     * array('ds_account' => 'test')
     *
     * @param  string $oldSubId old subscription
     * @param  string $newSubId new subscription
     * @return mixed         false or integer - status
     */
    public function changeBundle($params, $oldSubId, $newSubId)
    {
        // verify parametters
        if (empty($params)) {
            $this->_toLog('['.__FUNCTION__.'] - `$params` is empty;');
            return false;
        }
        if (empty($oldSubId)) {
            $this->_toLog('['.__FUNCTION__.'] - `$oldSubId` is empty');
            return false;
        }
        if (empty($newSubId)) {
            $this->_toLog('['.__FUNCTION__.'] - `$newSubId` is empty');
            return false;
        }

        // prepare arguments array
        $args = $this->_prepareAccountDefaultData($params);
        // verify arguments
        if (empty($args)) {
            $this->_toLog('['.__FUNCTION__.'] - account parametter not found in `$params`;');
            return false;
        }

        // assign subscriptions
        $args['old_sub_id'] = $oldSubId;
        $args['new_sub_id'] = $newSubId;

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->status)) {
            return $res->status;
        }
        else {
            return false;
        }
    }

    /**
     * get active provider's purchases
     * @param  string $startDate start date of reporting period
     * @param  integer $page      page number
     * @return mixed             false or object - data
     */
    public function getAllPurchases($startDate, $page = 1)
    {
        // verify arguments
        if (empty($startDate) || !strtotime($startDate)) {
            $this->_toLog('['.__FUNCTION__.'] - parametter `$startDate` is not valida date type;');
            return false;
        }
        if (!is_numeric($page) || $page <= 0) {
            $this->_toLog('['.__FUNCTION__.'] - `$page` is not numeric os less than 0;');
            return false;
        }
        // prepare arguments
        $args = array(
            'start_date' => $startDate,
            'page' => $page
        );
        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /* -end purchases functions */


    /* devices functions */

    /**
     * method add device and bind to user
     * $account is required
     * $serialNumber or $mac is required, but advisable required TWO
     * $binding_code is required for providers that work with purchases for access to additional devices
     * $addParams may assigns 'device_type', 'device_model', 'type'
     *
     * @param string $account       user account
     * @param string $serialNumber  device serial number
     * @param string $mac           mac address
     * @param string $binding_code  code for binding
     * @param array  $addParams     additional params
     * @return  mixed     false or integer - status
     */
    public function addDevice($account, $serialNumber = null, $mac = null, $binding_code = null, $addParams = array())
    {
        // verify arguments
        if (empty($account)) {
            $this->_toLog('['.__FUNCTION__.'] - `$account` not set;');
            return false;
        }
        if (empty($serialNumber) && empty($mac)) {
            $this->_toLog('['.__FUNCTION__.'] - must set `$serialNumber` or `$mac` or both;');
            return false;
        }

        // prepare arguments
        $args = array(
            'account' => $account
        );

        if (!empty($serialNumber)) {
            $args['serial_number'] = $serialNumber; // assign serial number
        }
        if (!empty($mac)) {
            $args['mac'] = $mac; // assign mac
        }
        if (!empty($binding_code)) {
            $args['binding_code'] = $binding_code; // assign binding code
        }

        // assign additional params
        if (!empty($addParams['device_type'])) {
            $args['device_type'] = $addParams['device_type']; // assign device type
        }
        if (!empty($addParams['device_model'])) {
            $args['device_model'] = $addParams['device_model']; // assign device model
        }
        // assign device type
        if (!empty($addParams['type'])) {
            $args['type'] = $this->_prepareDeviceType($addParams['type']);
        }

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->status)) {
            return $res->status;
        }
        else {
            return false;
        }
    }

    /**
     * method unbind device from user
     * $serialNumber or $mac is required, but advisable required TWO
     * $account is NOT required
     * $type may assigns:
     * device_break_contract - end of contract
     * device_change - equipment problem
     *
     * @param  string $serialNumber device serial number
     * @param  string $mac          device mac address
     * @param  string $account      user account
     * @param  string $type         device type
     * @return mixed          false or result
     */
    public function delDevice($serialNumber = null, $mac = null, $account = null, $type = null)
    {
        // verify arguments
        if (empty($serialNumber) && empty($mac)) {
            $this->_toLog('['.__FUNCTION__.'] - must set `$serialNumber` or `$mac` or both;');
            return false;
        }

        // init arguments array
        $args = array();

        if (!empty($serialNumber)) {
            $args['serial_number'] = $serialNumber; // assign serial number
        }
        if (!empty($mac)) {
            $args['mac'] = $mac; // assign mac
        }
        if (!empty($account)) {
            $args['account'] = $account; // assign account
        }
        if (!empty($type) && in_array($type, array('device_break_contract', 'device_change'))) {
            $args['type'] = $type;
        }

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->status)) {
            return $res->status;
        }
        else {
            return false;
        }
    }

    /**
     * method check device
     * $serialNumber or $mac is required, but advisable required TWO
     *
     * @param  string $serialNumber device serial number
     * @param  string $mac          device mac address
     * @return mixed           false or 0 or object
     */
    public function deviceExists($serialNumber = null, $mac = null)
    {
        // verify arguments
        if (empty($serialNumber) && empty($mac)) {
            $this->_toLog('['.__FUNCTION__.'] - must set `$serialNumber` or `$mac` or both;');
            return false;
        }

        // init arguments array
        $args = array();

        if (!empty($serialNumber)) {
            $args['serial_number'] = $serialNumber; // assign serial number
        }
        if (!empty($mac)) {
            $args['mac'] = $mac; // assign mac
        }

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /**
     * method return device list
     * @param  string  $account user account
     * @param  string  $email   user email
     * @param  integer $offset  offset parametter
     * @param  integer $limit   limit parametter no more than 1000
     * @return mixed         false or array
     */
    public function getDeviceList($account = null, $email = null, $offset = 0, $limit = 1000)
    {
        // verify arguments
        if (!is_numeric($offset) || $offset < 0) {
            // log info
            $this->_toLog('['.__FUNCTION__.'] - `$offset` is incorrect, set to 0;', 2);
            $offset = 0;
        }
        if (!is_numeric($limit) || $limit <= 0 || $limit > 1000) {
            // log info
            $this->_toLog('['.__FUNCTION__.'] - `$limit` is incorrect, set to default 1000;', 2);
            $limit = 1000;
        }

        // init arguments array
        $args = array(
            'offset' => $offset,
            'limit' => $limit
        );

        if (!empty($account)) {
            $args['account'] = $account; // assign account
        }
        if (empty($args['account']) && !empty($email)) {
            $args['email'] = $email; // assign email
        }

        // log info
        $this->_toLog('['.__FUNCTION__.'] - send params: '.var_export($args, true).';', 3);
        // run request to API and get result
        $res = $this->_connect(__FUNCTION__, $args);
        // verify and return
        if (isset($res->data)) {
            return $res->data;
        }
        else {
            return false;
        }
    }

    /* -end devices functions */

    /* -end API functions */

} // end class
