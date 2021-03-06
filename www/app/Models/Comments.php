<?php

namespace Songfolio\Models;

use Songfolio\Core\BaseSQL;
use Songfolio\Models\Users;

class Comments extends BaseSQL
{
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function prepareComments(string $type, string $id): array
    {
        $comments = $this->getAllBy(['type' => $type, 'type_id' => $id, 'confirm' => '1'], ['orderBy' => 'date_created', 'orderTo' => 'DESC']);
        return self::match($comments);
    }

    public function prepareConfirmComments()
    {
        $comments = $this->getAllBy(['confirm' => '0'], ['orderBy' => 'date_created', 'orderTo' => 'DESC']);
        return self::match($comments);
    }

    private function match($comments): array
    {
        foreach ($comments as $key => $value) {
            $user = new Users($value['user_id']);
            $comments[$key]['user_name'] = $user->getShowName();
        }
        return $comments;
    }
}
