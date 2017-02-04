<?php

namespace app\helpers;

class Constants
{
    //Roles of users
    const ROLE_ADMIN = 1;
    const ROLE_REDACTOR = 2;
    const ROLE_NEW = 3;

    //Statuses of work
    const STATUS_NEW = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_DONE = 3;
    const STATUS_ENABLED = 4;
    const STATUS_DISABLED = 5;

    //Email feedback types
    const EMAIL_TYPE_OFFER = 1;
    const EMAIL_TYPE_COMMENT = 2;
    const EMAIl_TYPE_QUESTION = 3;

    //Bot message types
    const BOT_SESSION_INIT = 1;
    const BOT_NEED_APPEND = 2;
    const BOT_NEED_SELECT_TICKET = 3;
    const BOT_NEED_ANSWER = 4;
    const BOT_NEED_CONFIRM_ANSWER = 5;
    const BOT_NEED_SELECT_TICKET_FA = 6;
}