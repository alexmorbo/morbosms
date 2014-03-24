<?php

namespace Morbosms;

/**
 * API V4
 *
 * Отправка смсок через шлюз
 */
class Sms
{
    /**
     * Метод
     *
     * @var string
     */
    protected $_action;


    /**
     * Параметры запроса
     *
     * @var array
     */
    protected $_sendData = [];


    /**
     * Логин
     *
     * @var string
     */
    private $_login;


    /**
     * Пароль доступа
     *
     * @var
     */
    private $_passkey;


    /**
     * Конструктор
     *
     * @param string $login   Логин
     * @param string $passkey Ключ авторизации
     */
    public function __construct($login, $passkey)
    {
        $this->_login = $login;
        $this->_passkey = $passkey;
    }


    /**
     * Стоимость
     *
     * @param string       $message Текст сообщения
     * @param string|array $numbers Номера телефонов
     *
     * @return mixed
     */
    public function getSmsCost($message, $numbers)
    {
        $this->_action = 'send_message';
        $this->_sendData['phones'] = $numbers;
        $this->_sendData['message'] = $message;
        $this->_sendData['cost'] = 1;

        return json_decode($this->_sendRequest(), true);
    }


    /**
     * Отправка сообщения
     *
     * @param string       $message    Текст сообщения
     * @param string|array $numbers    Номера телефонов
     * @param string       $senderName SenderID
     *
     * @return array
     */
    public function sendMessage($message, $numbers, $senderName = '')
    {
        $this->_action = 'send_message';
        unset($this->_sendData['phones']);
        if (is_array($numbers)) {
            $this->_sendData['phones'] = $numbers;
        } else {
            $this->_sendData['phones'][] = $numbers;
        }
        $this->_sendData['message'] = $message;
        $this->_sendData['sendfrom'] = $senderName;

        return json_decode($this->_sendRequest(), true);
    }


    /**
     * Возвращает баланс
     *
     * @return mixed
     */
    public function getBalance()
    {
        $this->_action = 'balance_info';

        return json_decode($this->_sendRequest(), true);
    }


    /**
     * Возвращает список доступных отправителей
     *
     * @return mixed
     */
    public function getSenders()
    {
        $this->_action = 'senders';

        return json_decode($this->_sendRequest(), true);
    }


    /**
     * Возвращает статус сообщения
     *
     * @param int $messageId Номер сообщения
     *
     * @return mixed
     */
    public function getMessageStatus($messageId)
    {
        $this->_action = 'status';
        $this->_sendData['message_id'] = $messageId;

        return json_decode($this->_sendRequest(), true);
    }


    /**
     * Возвращает стоимость по направлениям
     *
     * @return mixed
     */
    public function getMessageRates()
    {
        $this->_action = 'rates';

        return json_decode($this->_sendRequest(), true);
    }


    /**
     * Отправляет запрос на шлюз
     *
     * @return mixed
     */
    protected function _sendRequest()
    {
        $url = 'http://send.morbo.ru/1/' . $this->_action;
        $fields['USER'] = urlencode($this->_login);
        $fields['KEY'] = urlencode($this->_passkey);
        $fields['SEND'] = urlencode(json_encode($this->_sendData));

        $fields = (is_array($fields)) ? http_build_query($fields) : $fields;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields))
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
