<?php

declare(strict_types=1);

use Mailing\Entity\EmailMessage;
use Mailing\Entity\Mailer;
use Mailing\Entity\Sender;
use Mailing\Entity\Template;
use Mailing\Entity\Transactional;
use Mailing\Navigation\Invokable\EmailMessageLabel;
use Mailing\Navigation\Invokable\MailerLabel;
use Mailing\Navigation\Invokable\SenderLabel;
use Mailing\Navigation\Invokable\TemplateLabel;
use Mailing\Navigation\Invokable\TransactionalLabel;

return [
    'navigation' => [
        'default' => [
            'mailing' => [
                'label' => _('txt-emails'),
                'resource' => 'route/zfcadmin/mailing/email/list',
                'privilege' => 'list',
                'uri' => '#',
                'pages' => [
                    // And finally, here is where we define our page hierarchy
                    'email' => [
                        'label' => _('txt-nav-email-list'),
                        'route' => 'zfcadmin/mailing/email/list',
                        'pages' => [
                            'email-view' => [
                                'route' => 'zfcadmin/mailing/email/view',
                                'params' => [
                                    'entities' => [
                                        'id' => EmailMessage::class,
                                    ],
                                    'invokables' => [
                                        EmailMessageLabel::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'senders' => [
                        'label' => _('txt-nav-sender-list'),
                        'route' => 'zfcadmin/mailing/sender/list',
                        'pages' => [
                            'view' => [
                                'route' => 'zfcadmin/mailing/sender/view',
                                'params' => [
                                    'entities' => [
                                        'id' => Sender::class,
                                    ],
                                    'invokables' => [
                                        SenderLabel::class,
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-nav-edit'),
                                        'route' => 'zfcadmin/mailing/sender/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Sender::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'route' => 'zfcadmin/mailing/sender/new',
                                'label' => _('txt-new-sender'),
                            ],
                        ],
                    ],
                    'mailers' => [
                        'label' => _('txt-nav-mailer-list'),
                        'route' => 'zfcadmin/mailing/mailer/list',
                        'pages' => [
                            'view' => [
                                'route' => 'zfcadmin/mailing/mailer/view',
                                'params' => [
                                    'entities' => [
                                        'id' => Mailer::class,
                                    ],
                                    'invokables' => [
                                        MailerLabel::class
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-nav-edit'),
                                        'route' => 'zfcadmin/mailing/mailer/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Mailer::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'route' => 'zfcadmin/mailing/mailer/new',
                                'label' => _('txt-new-mailer'),
                            ],
                        ],
                    ],
                    'templates' => [
                        'label' => _('txt-nav-mailing-template-list'),
                        'route' => 'zfcadmin/mailing/template/list',
                        'pages' => [
                            'view' => [
                                'route' => 'zfcadmin/mailing/template/view',
                                'params' => [
                                    'entities' => [
                                        'id' => Template::class,
                                    ],
                                    'invokables' => [
                                        TemplateLabel::class,
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-nav-edit'),
                                        'route' => 'zfcadmin/mailing/template/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Template::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'route' => 'zfcadmin/mailing/template/new',
                                'label' => _('txt-new-email-template'),
                            ],
                        ],
                    ],
                    'transactional' => [
                        'label' => _('txt-nav-transactional-email-list'),
                        'route' => 'zfcadmin/mailing/transactional/list',
                        'pages' => [
                            'view' => [
                                'route' => 'zfcadmin/mailing/transactional/view',
                                'params' => [
                                    'entities' => [
                                        'id' => Transactional::class,
                                    ],
                                    'invokables' => [
                                        TransactionalLabel::class,
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-nav-edit'),
                                        'route' => 'zfcadmin/mailing/transactional/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Transactional::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'route' => 'zfcadmin/mailing/transactional/new',
                                'label' => _('txt-new-transactional-email'),
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
