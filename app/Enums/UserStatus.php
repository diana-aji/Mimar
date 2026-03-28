<?php

namespace App\Enums;

enum UserStatus: string
{
     case ACTIVE = 'active';
     case INACTIVE = 'inactive';
     case SUPER_ADMIN = 'super-admin';
     case ADMIN = 'admin';
     case USER = 'user';
}