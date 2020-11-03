<?php
/*
    Необходимо доработать класс рассылки Newsletter, что бы он отправлял письма 
    и пуш нотификации для юзеров из UserRepository. 
    
    За отправку имейла мы считаем вывод в консоль строки: "Email {email} has been sent to user {name}"
    За отправку пуш нотификации: "Push notification has been sent to user {name} with device_id {device_id}"
    
    Так же необходимо реализовать функциональность для валидации имейлов/пушей:
    1) Нельзя отправлять письма юзерам с невалидными имейлами - done
    2) Нельзя отправлять пуши юзерам с невалидными device_id. Правила валидации можете придумать сами.
    3) Ничего не отправляем юзерам у которых нет имен - done
    4) На одно и то же мыло/device_id - можно отправить письмо/пуш только один раз
    
    Для обеспечения возможности масштабирования системы (добавление новых типов отправок и новых валидаторов), 
    можно добавлять и использовать новые классы и другие языковые конструкции php в любом количестве. 
    Реализация должна соответствовать принципам ООП
*/

class Validator {
    public function email(string $email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function deviceId(string $deviceId) {
        return $deviceId !== '' && preg_match("/[0-9]/",$deviceId) == 0;
    }
}
class Newsletter
{
    private $validator;
    public function __construct()
    {
        $this->validator = new Validator();
    }

    public function send($testUser): void
    {
        $pushedEmails = [];
        $pushedDeviceIds = [];
        foreach ($testUser as $key => $value) {

            if(isset($value['name']) && $value['name'] !== '') {
                if($this->validator->email((string)($value['email'] ?? ''))
                    && !in_array($value['email'], $pushedEmails)) {
                    $pushedEmails[] = $value['email'];
                    echo sprintf(
                        '<p>Email %s has been sent to user %s</p>',
                        $value['email'],
                        $value['name']
                    );
                }

                if($this->validator->deviceId((string)($value['device_id'] ?? ''))
                    && !in_array($value['device_id'], $pushedDeviceIds)) {
                    $pushedDeviceIds[] = $value['device_id'];
                    echo sprintf(
                        '<p>Push notification has been sent to user %s with device_id %s</p>',
                        $value['name'],
                        $value['device_id']
                    );
                }
            }
        }
    }
}

class UserRepository
{
    public function getUsers(): array
    {
        return [
            [
                'name' => 'abc',
                'email' => 'ivan@test.com',
                'device_id' => 'Ks[2dqweer'
            ],
            [
                'name' => 'Ivan',
                'email' => 'ivan@test.com',
                'device_id' => 'Ks[dqweer'
            ],
            [
                'name' => 'Peter',
                'email' => 'peter@test.com'
            ],
            [
                'name' => 'Mark',
                'device_id' => 'Ks[dqweer'
            ],
            [
                'name' => 'Nina',
                'email' => '...'
            ],
            [
                'name' => 'Luke',
                'device_id' => 'vfehlfgg'
            ],
            [
               'name' => 'Zerg',
               'device_id' => ''
            ],
            [
               'email' => '...',
               'device_id' => ''
            ]
        ];    
    }
}
// Тут релизовать получение объекта(ов) рассылки Newsletter и вызов(ы) метода send()
$newsLetter = new Newsletter();
$userRepository = new UserRepository();
$testUser = $userRepository->getUsers();
$newsLetter->send($testUser);