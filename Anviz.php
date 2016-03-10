<?php

class Anviz {

    const ACK_SUCCESS = '00'; //0x00 indcate success operation
    const ACK_FAIL = '01'; //0x01 indcate failed operation
    const getInfo = '30';   //Get firmware version, comunication password, sleep time, volume, language, date and time format, attendace state, language setting flag, command version
    const setInfo = '31';   //Set above
    const getInfo2 = '32';  //Get compare precission, Fixed wiegand Head code, Wiegand option, Work code permission, real-time mode setting ...
    const setInfo2 = '33'; //Set above
    const getDateTime = '38'; //Get the date and time of T&A
    const setDateTime = '39'; //Set the date and time of T&A

    /**
     * An instance of crc table
     * @var crc_table
     * @access protected
     */

    protected $crc_table = [
        0x0000, 0x1189, 0x2312, 0x329B, 0x4624, 0x57AD, 0x6536, 0x74BF, 0x8C48, 0x9DC1,
        0xAF5A, 0xBED3, 0xCA6C, 0xDBE5, 0xE97E, 0xF8F7, 0x1081, 0x0108, 0x3393, 0x221A,
        0x56A5, 0x472C, 0x75B7, 0x643E, 0x9CC9, 0x8D40, 0xBFDB, 0xAE52, 0xDAED, 0xCB64,
        0xF9FF, 0xE876, 0x2102, 0x308B, 0x0210, 0x1399, 0x6726, 0x76AF, 0x4434, 0x55BD,
        0xAD4A, 0xBCC3, 0x8E58, 0x9FD1, 0xEB6E, 0xFAE7, 0xC87C, 0xD9F5, 0x3183, 0x200A,
        0x1291, 0x0318, 0x77A7, 0x662E, 0x54B5, 0x453C, 0xBDCB, 0xAC42, 0x9ED9, 0x8F50,
        0xFBEF, 0xEA66, 0xD8FD, 0xC974, 0x4204, 0x538D, 0x6116, 0x709F, 0x0420, 0x15A9,
        0x2732, 0x36BB, 0xCE4C, 0xDFC5, 0xED5E, 0xFCD7, 0x8868, 0x99E1, 0xAB7A, 0xBAF3,
        0x5285, 0x430C, 0x7197, 0x601E, 0x14A1, 0x0528, 0x37B3, 0x263A, 0xDECD, 0xCF44,
        0xFDDF, 0xEC56, 0x98E9, 0x8960, 0xBBFB, 0xAA72, 0x6306, 0x728F, 0x4014, 0x519D,
        0x2522, 0x34AB, 0x0630, 0x17B9, 0xEF4E, 0xFEC7, 0xCC5C, 0xDDD5, 0xA96A, 0xB8E3,
        0x8A78, 0x9BF1, 0x7387, 0x620E, 0x5095, 0x411C, 0x35A3, 0x242A, 0x16B1, 0x0738,
        0xFFCF, 0xEE46, 0xDCDD, 0xCD54, 0xB9EB, 0xA862, 0x9AF9, 0x8B70, 0x8408, 0x9581,
        0xA71A, 0xB693, 0xC22C, 0xD3A5, 0xE13E, 0xF0B7, 0x0840, 0x19C9, 0x2B52, 0x3ADB,
        0x4E64, 0x5FED, 0x6D76, 0x7CFF, 0x9489, 0x8500, 0xB79B, 0xA612, 0xD2AD, 0xC324,
        0xF1BF, 0xE036, 0x18C1, 0x0948, 0x3BD3, 0x2A5A, 0x5EE5, 0x4F6C, 0x7DF7, 0x6C7E,
        0xA50A, 0xB483, 0x8618, 0x9791, 0xE32E, 0xF2A7, 0xC03C, 0xD1B5, 0x2942, 0x38CB,
        0x0A50, 0x1BD9, 0x6F66, 0x7EEF, 0x4C74, 0x5DFD, 0xB58B, 0xA402, 0x9699, 0x8710,
        0xF3AF, 0xE226, 0xD0BD, 0xC134, 0x39C3, 0x284A, 0x1AD1, 0x0B58, 0x7FE7, 0x6E6E,
        0x5CF5, 0x4D7C, 0xC60C, 0xD785, 0xE51E, 0xF497, 0x8028, 0x91A1, 0xA33A, 0xB2B3,
        0x4A44, 0x5BCD, 0x6956, 0x78DF, 0x0C60, 0x1DE9, 0x2F72, 0x3EFB, 0xD68D, 0xC704,
        0xF59F, 0xE416, 0x90A9, 0x8120, 0xB3BB, 0xA232, 0x5AC5, 0x4B4C, 0x79D7, 0x685E,
        0x1CE1, 0x0D68, 0x3FF3, 0x2E7A, 0xE70E, 0xF687, 0xC41C, 0xD595, 0xA12A, 0xB0A3,
        0x8238, 0x93B1, 0x6B46, 0x7ACF, 0x4854, 0x59DD, 0x2D62, 0x3CEB, 0x0E70, 0x1FF9,
        0xF78F, 0xE606, 0xD49D, 0xC514, 0xB1AB, 0xA022, 0x92B9, 0x8330, 0x7BC7, 0x6A4E,
        0x58D5, 0x495C, 0x3DE3, 0x2C6A, 0x1EF1, 0x0F78
    ];

    /**
     * Socket resource
     * @var socket
     * @access protected
     * Default NULL 
     */
    protected $socket = null;

    /**
     * @var client
     * @access protected 
     */
    protected $client;

    /**
     * Device communication mode
     * Values client, server
     * @var mode
     * @access public
     */
    public $mode = 'client';

    /**
     * Device ID
     * Values 1-99999999 
     * @var id
     * @access public 
     */
    public $id;

    /**
     * Communication port
     * Default value 5010
     * @var port
     * @access public 
     */
    public $port = 5010;

    /**
     * IP address of either network adapter (client mode) or device (server mode)
     * @var ip
     * @access public 
     */
    public $ip;

    function __construct($mode = 'client', $id, $port = 5010, $ip) {
        $this->mode = $mode;
        $this->id = $id;
        $this->port = $port;
        $this->ip = $ip;

        $this->initSocket();
        $this->client = socket_accept($this->socket);
    }

    function __destruct() {
        socket_close($this->client);
        $this->socket = null;
    }

    /**
     * @param bin $b
     * @return hex (2 bytes)
     * Description: Function for generating last 2 bytes
     */
    function tc_crc16($b) {
        $crc = 0xFFFF;

        for ($l = 0; $l < strlen($b); $l++) {
            $crc ^= ord($b[$l]);
            $crc = ($crc >> 8) ^ $this->crc_table[$crc & 255];
        }

        $res = strtoupper(dechex($crc));

        //if crc has length less than 4 add leading zero
        $res = $this->padHex($res, 4);

        return($res[2] . $res[3] . $res[0] . $res[1]);
    }

    /**
     * 
     * @param string $hex
     * @param int $length
     * @return string
     * @throws AnvizException
     * Pad the input hex with leading zeroes
     */
    function padHex($hex, $length) {

        if ($length % 2 == 1) {
            throw new AnvizException("Hex should be even number");
        }

        while (strlen($hex) < $length) {
            $hex = '0' . $hex;
        }

        return $hex;
    }

    /**
     * 
     * @param string $string
     * @param int $length
     * @param string $pad_char
     * @return string
     * @throws AnvizException
     */
    function padString($string, $length, $pad_char = '0') {
        if (strlen($string) > $length) {
            throw new AnvizException("String length is already greater than requested length");
        }

        while (strlen($string) < $length) {
            $string = $pad_char . $string;
        }

        return $string;
    }

    /**
     * @access private
     * Create socket and start and start listening
     */
    private function initSocket() {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_bind($this->socket, $this->ip, $this->port);

        socket_listen($this->socket);
    }

    /**
     * 
     * @param string $command
     * @param string $data
     * @return binary
     * Build a request for given command
     */
    private function requestBuilder($command, $data = '') {
        //first byte should always be A5
        $request = 'A5';
        echo $request . PHP_EOL;
        //add device id
        $request .= $this->padHex($this->id, 8);
        echo $request . PHP_EOL;

        //add command
        $request .= $this->padHex($command, 2);
        echo $request . PHP_EOL;

        //add data (can be empty)
        $request .= $this->padHex(strlen($data) / 2, 4); //devide string length by 2 (1 byte 2 chars)
        echo $request . PHP_EOL;

        //if data is not empty add its length
        if ($data != '') {
            $request .= $data;
        }
        //at this point request variable should have even number of charactes
        if (strlen($request) % 2 == 1) {
            throw new AnvizException("Request builder failed. Request has odd number of chars");
        }

        $request .= $this->tc_crc16(hex2bin($request));

        return hex2bin($request);
    }

    private function hex2str($hex) {
        $str = '';
        for ($i = 0; $i < strlen($hex); $i+=2) {
            $str .= chr(hexdec(substr($hex, $i, 2)));
        }

        return $str;
    }

    /**
     * Function for getting date and time from devie
     * @return time or false on error
     * Bytes:
     * 9  - year
     * 10 - month
     * 11 - day
     * 12 - hour
     * 13 - minute
     * 14 - second
     */
    public function getDateTime() {
        //write command to socket
        socket_write($this->client, $this->requestBuilder(self::getDateTime));

        //read answer
        $response = bin2hex(socket_read($this->client, 2024));

        //split response into bytes
        $bytes = str_split($response, 2);

        //convert hex data to decimal values
        foreach ($bytes as $key => $value) {
            $bytesDec[$key] = hexdec($value);
        }

        if ($bytes[7] == self::ACK_SUCCESS) { //response ok, device returned data
            //convert data to human readable format
            $year = '20' . $bytesDec[9];
            $month = $this->padString($bytesDec[10], 2);
            $day = $this->padString($bytesDec[11], 2);
            $hour = $this->padString($bytesDec[12], 2);
            $minute = $this->padString($bytesDec[13], 2);
            $second = $this->padString($bytesDec[14], 2);

            $dateTime = sprintf('%d-%d-%d %d:%d:%d', $year, $month, $day, $hour, $minute, $second);

            return $dateTime;
        } else if ($bytes[7] == self::ACK_FAIL) { //response came to the device but device failed to read data
            return false;
        } else { //error happened
            throw new AnvizException("An error occured while reading date and time. Device possibly turned off or network is down");
        }
    }

    /**
     * Set date and time
     */
    public function setDateTime() {

        //convert data from human readable format to hex values
        $year = $this->padHex(dechex(date('y')), 2);
        $month = $this->padHex(dechex(date('m')), 2);
        $day = $this->padHex(dechex(date('d')), 2);
        $hour = $this->padHex(dechex(date('H')), 2);
        $minute = $this->padHex(dechex(date('i')), 2);
        $second = $this->padHex(dechex(date('s')), 2);

        //build the request and then write it to socket
        socket_write($this->client, $this->requestBuilder(self::setDateTime, $year . $month . $day . $hour . $minute . $second));

        //get response
        $response = bin2hex(socket_read($this->client, 2024));

        //split response into bytes
        $bytes = str_split($response, 2);

        if ($bytes[7] == self::ACK_SUCCESS) { //date and time are set successfully
            return true;
        } else if ($bytes[7] == self::ACK_FAIL) { //request probably came to device but, device wasn't able to process request
            return false;
        } else { //error happened
            throw new AnvizException("An error occured while setting date and time. Device possibly turned off or network is down");
        }
    }

    /**
     * Get the basic device info 
     * @return array of data or false 
     * @throws AnvizException
     * Data bytes:
     * 1-8 Firmware
     * 9-11 Password
     * 12 Sleep time
     * 13 Volume
     * 14 Language
     * 15 Date / Time Format
     * 16 Attendance state
     * 17 Command Version
     */
    public function getDeviceInfo() {

        $request = $this->requestBuilder(self::getInfo);
        socket_write($this->client, $request);

        $response = bin2hex(socket_read($this->client, 2024));

        $bytes = str_split($response, 2);

        $return = array();

        if ($bytes[7] == self::ACK_SUCCESS) { //date and time are set successfully
            $data = array_slice($bytes, 9, -2);
            //get firmware
            $return['firmware'] = $this->hex2str(implode(array_slice($data, 0, 8)));
            //communication password and its length
            $return['password_length'] = bindec(substr(hex2bin($data[8]), 4));

            //password = first 4 bits of 9th byte + 10th byte + 11th byte
            $return['password'] = bindec(substr(hex2bin($data[8]), 0, 4) . hex2bin($data[9]) . hex2bin($data[10]));

            //sleep time
            $return['sleep_time'] = hexdec($data[11]);

            //volume
            $return['volume'] = hexdec($data[12]);

            //language
            $return['language'] = Parser::parseLanguage(hexdec($data[13]));

            //datetimeformat
            $return['date_format'] = Parser::parseDateFormat(bindec(substr(hex2bin($data[14]), 4)));
            $return['time_format'] = Parser::parseTimeFormat(bindec(substr(hex2bin($data[14]), 0, 4)));

            //attendance state
            $return['attendance_state'] = hexdec($data[15]);

            //language setting flag -- if true user can modify menu language
            $return['language_setting_flag'] = (bool) hexdec($data[16]);

            //command version -- whatever that means
            $return['command_version'] = hexdec($data[17]);

            return $return;
        } else if ($bytes[7] == self::ACK_FAIL) { //request probably came to device but, device wasn't able to process request
            return false;
        } else { //error happened
            throw new AnvizException("An error occured while setting date and time. Device possibly turned off or network is down");
        }
    }

}

/**
 * Anviz exception handler
 * @package Anviz
 */
class AnvizException extends Exception {

    public function errorMessage() {
        return $this->getMessage() . "<br>\n";
    }

}

/**
 * parse device codes to human readable format
 */
class Parser {

    /**
     * 
     * @param dec $code
     * @return string
     */
    public static function parseLanguage($code) {
        switch ($code) {
            case 0:
                return "Simplified Chinese";
            case 1:
                return "Traditional Chinese";
            case 2:
                return "English";
            case 3:
                return "French";
            case 4:
                return "Spanish";
            case 5:
                return "Portuguese";
            // ...
            case 15:
                return "Croatian";
        }
    }

    /**
     * 
     * @param dec $code
     * @return string
     */
    public static function parseDateFormat($code) {
        switch ($code) {
            case 0:
                return "Chinese yyyy-mm-dd";
            case 1:
                return "America mm-dd, yyyy";
            case 2:
                return "English d/m/yy";
        }
    }
    
    /**
     * 
     * @param dec $code
     * @return string
     */
    public static function parseTimeFormat($code) {
        switch ($code) {
            case 0:
                return "24 hours";
            case 1:
                return "12 hours (AM/PM)";
        }
    }

}
