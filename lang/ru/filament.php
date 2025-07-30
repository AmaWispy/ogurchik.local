<?php

return [
    'pages' => [
        'title' => 'Страницы',
    ],
    'navigation' => [
        'title' => 'Навигация',
    ],
    'forms' => [
        'actions' => [
            'save' => 'Сохранить',
            'cancel' => 'Отмена',
        ],
        'modal' => [
            'heading' => 'Уведомление',
        ],
    ],
    'login' => [
        'title' => 'Вход',
        'heading' => 'Войти в систему',
        'buttons' => [
            'submit' => [
                'label' => 'Войти',
            ],
        ],
        'fields' => [
            'email' => [
                'label' => 'Email',
                'placeholder' => 'Введите email',
            ],
            'password' => [
                'label' => 'Пароль',
                'placeholder' => 'Введите пароль',
            ],
            'remember' => [
                'label' => 'Запомнить меня',
            ],
        ],
    ],
    'dashboard' => [
        'title' => 'Панель управления',
    ],
    'resources' => [
        'title' => 'Ресурсы',
        'actions' => [
            'create' => 'Создать',
            'edit' => 'Редактировать',
            'view' => 'Просмотр',
            'delete' => 'Удалить',
        ],
        'modal' => [
            'delete' => [
                'heading' => 'Удалить :label',
                'subheading' => 'Вы уверены, что хотите выполнить это действие?',
                'buttons' => [
                    'delete' => [
                        'label' => 'Удалить',
                    ],
                    'cancel' => [
                        'label' => 'Отмена',
                    ],
                ],
            ],
        ],
    ],
]; 