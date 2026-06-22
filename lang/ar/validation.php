<?php

declare(strict_types=1);

/**
 * Arabic (ar) – Validation Translation Strings
 *
 * All validation error messages mirror Laravel's built-in en/validation.php.
 * Add or override strings here as the LMS validation rules grow.
 */
return [
    'accepted'             => 'يجب قبول حقل :attribute.',
    'active_url'           => 'حقل :attribute يجب أن يكون رابطاً صالحاً.',
    'after'                => 'يجب أن يكون حقل :attribute تاريخاً بعد :date.',
    'alpha'                => 'يجب أن يحتوي حقل :attribute على أحرف فقط.',
    'alpha_num'            => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام فقط.',
    'array'                => 'يجب أن يكون حقل :attribute مصفوفة.',
    'before'               => 'يجب أن يكون حقل :attribute تاريخاً قبل :date.',
    'between'              => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون عدد حروف :attribute بين :min و :max.',
        'array'   => 'يجب أن يحتوي :attribute على عدد عناصر بين :min و :max.',
    ],
    'boolean'              => 'يجب أن تكون قيمة حقل :attribute صواباً أو خطأً.',
    'confirmed'            => 'تأكيد حقل :attribute غير مطابق.',
    'date'                 => 'حقل :attribute ليس تاريخاً صالحاً.',
    'date_format'          => 'لا يتوافق حقل :attribute مع الصيغة :format.',
    'different'            => 'يجب أن يكون حقل :attribute مختلفاً عن :other.',
    'digits'               => 'يجب أن يتكون حقل :attribute من :digits أرقام.',
    'digits_between'       => 'يجب أن يكون حقل :attribute بين :min و :max رقماً.',
    'email'                => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صالح.',
    'exists'               => 'القيمة المختارة لحقل :attribute غير صالحة.',
    'file'                 => 'يجب أن يكون حقل :attribute ملفاً.',
    'filled'               => 'يجب أن يكون لحقل :attribute قيمة.',
    'image'                => 'يجب أن يكون حقل :attribute صورة.',
    'in'                   => 'القيمة المختارة لحقل :attribute غير صالحة.',
    'integer'              => 'يجب أن يكون حقل :attribute عدداً صحيحاً.',
    'ip'                   => 'يجب أن يكون حقل :attribute عنوان IP صالحاً.',
    'json'                 => 'يجب أن يكون حقل :attribute نص JSON صالحاً.',
    'max'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute أقل من أو تساوي :max.',
        'file'    => 'يجب ألا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'string'  => 'يجب ألا يتجاوز عدد حروف :attribute :max حرفاً.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :max عنصراً.',
    ],
    'mimes'                => 'يجب أن يكون حقل :attribute ملفاً من نوع: :values.',
    'min'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :min.',
        'file'    => 'يجب أن لا يقل حجم الملف :attribute عن :min كيلوبايت.',
        'string'  => 'يجب أن لا يقل عدد حروف :attribute عن :min أحرف.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل :min عناصر.',
    ],
    'not_in'               => 'القيمة المختارة لحقل :attribute غير صالحة.',
    'numeric'              => 'يجب أن يكون حقل :attribute رقماً.',
    'regex'                => 'صيغة حقل :attribute غير صالحة.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'required_with'        => 'حقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all'    => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without'     => 'حقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون :values موجودة.',
    'same'                 => 'يجب أن يتطابق حقل :attribute مع :other.',
    'size'                 => [
        'numeric' => 'يجب أن تكون قيمة :attribute :size.',
        'file'    => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'string'  => 'يجب أن يكون عدد حروف :attribute :size أحرف.',
        'array'   => 'يجب أن يحتوي :attribute على :size عناصر.',
    ],
    'string'               => 'يجب أن يكون حقل :attribute نصاً.',
    'timezone'             => 'يجب أن يكون حقل :attribute منطقة زمنية صالحة.',
    'unique'               => 'قيمة حقل :attribute مُستخدمة مسبقاً.',
    'url'                  => 'صيغة حقل :attribute غير صالحة.',

    'attributes' => [],
];
