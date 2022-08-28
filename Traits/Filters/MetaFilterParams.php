<?php

namespace Zadrog\LaravelModelQuery\Traits\Filters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait MetaFilterParams
{
    private $response = [];

    public function params(Model $model, array $fields)
    {
        $connection = DB::connection();

        foreach ($fields as $field) {

            $this->response[] = [

                'field' => $field,
                'type'  => $connection->getDoctrineColumn($model->getTable(), $field)->gettype()->getName(),

            ];
        }

        // dd($this->response);
        return $this->response;
    }

    public function respondentFilter()
    {
        $this->response = [
            [
                'field' => 'created_at',
                'title' => 'Дата регистрации',
                'type'  => 'datetime',
            ],
            [
                'field' => 'email',
                'title' => 'Email',
                'type'  => 'string',
            ],
            [
                'field' => 'birthday',
                'title' => 'Дата рождения',
                'type'  => 'datetime',
            ],
            [
                'field'   => 'gender',
                'title'   => 'Пол',
                'type'    => 'list',
                'options' => [
                    [
                        'name' => 'Мужской',
                        'stub' => 'male',
                    ],
                    [
                        'name' => 'Женский',
                        'stub' => 'female',
                    ],
                ],
            ],
            [
                'field'   => 'status',
                'title'   => 'Статус',
                'type'    => 'list',
                'options' => [
                    [
                        'name' => 'Активен',
                        'stub' => 'active',
                    ],
                    [
                        'name' => 'Ожидает',
                        'stub' => 'wait',
                    ],
                    [
                        'name' => 'Заблокирован',
                        'stub' => 'block',
                    ],
                ],
            ],
        ];

        return $this->response;
    }
}
