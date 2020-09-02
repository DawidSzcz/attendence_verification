<?php


namespace app\components;

use yii\httpclient\Client;
use yii\base\BaseObject;

class StudentBase extends BaseObject
{
    public $url;
    public $auth_user;
    public $auth_password;

    private $http_client;

    const CARD_UID = 'card_uid';
    const ALBUM_NO = 'album_no';
    const PARAMETRIZED_URL = '%s/%s/%s';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->http_client = new Client();
    }

    public function retrieveStudentsByCardUids(array $card_uids)
    {
        return $this->retrieveStudentsByParam(static::CARD_UID, $card_uids);
    }

    public function retrieveStudentsByAlbumNos(array $album_nos)
    {
        return $this->retrieveStudentsByParam(static::ALBUM_NO, $album_nos);
    }
    public function retrieveAllStudents()
    {
        return $this->retrieveStudents($this->url);
    }

    private function retrieveStudentsByParam(string $param, array $values)
    {
        $result = [];

        if(!empty($values)) {
            $result = $this->retrieveStudents(sprintf(static::PARAMETRIZED_URL, $this->url, $param, base64_encode(join(',', $values))));
        }
        return $result;
    }

    private function retrieveStudents(string $url)
    {
        $response = $this->http_client->createRequest()
            ->setUrl($url)
            ->addHeaders([
                'Authorization' => 'Basic ' . base64_encode(sprintf('%s:%s', $this->auth_user, $this->auth_password))
            ])
            ->send();

        return $response->getData();
    }
}