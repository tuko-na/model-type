<?php

namespace App\Services\IncidentFormSchema;

/**
 * フォームフィールドの型を定義するenum
 */
enum FieldType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case SELECT = 'select';
    case BOOLEAN = 'boolean';
    case SLIDER = 'slider';
    case BUTTON_GROUP = 'button_group';
    case ICON_SELECT = 'icon_select';
    case TEXTAREA = 'textarea';
}
