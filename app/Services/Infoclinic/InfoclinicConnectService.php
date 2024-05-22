<?php

namespace App\Services\Infoclinic;
class InfoclinicConnectService
{
//    const HOST = '192.168.88.14:C:/database/SIST_RESHENIA_19072022.FDB';
//    const HOST = '192.168.88.14:C:/database/SIST_RESHENIA.FDB'; current
    const HOST = 'infobase.local:C:/database/SIST_RESHENIA.FDB';
    const LOGIN = 'CHEA';
    const PASS = 'DJhVPfM';
    const LOCALE = 'utf-8';
    private $connect;
    /**
     * @var bool|mixed for manual transtion control
     */
    private bool $manual;

    public function __construct($manual = false) {
        $this->manual = $manual;
        if(!$this->manual) {

            $this->connect = \ibase_connect(self::HOST, self::LOGIN, self::PASS, self::LOCALE);
        }

    }

    public function __destruct() {
        if(!$this->manual) {
            ibase_close($this->connect);
//            usleep(200000);
        }
    }
    public function connect() {
        $this->connect = \ibase_connect(self::HOST, self::LOGIN, self::PASS, self::LOCALE);
    }
    public function close() {
        \ibase_close($this->connect);
    }
    /**
     * Делает запрос в БД Инфоклиники
     * @param string $query
     * @return array
     */
    public function query(string $query):array {

        $sth = \ibase_query($this->connect, $query);
        $data = [];
        while ($row = \ibase_fetch_assoc ($sth)) {
            $data[] = $row;
        }
        \ibase_commit();
        ibase_free_result($sth);
        return $data;
    }
}
