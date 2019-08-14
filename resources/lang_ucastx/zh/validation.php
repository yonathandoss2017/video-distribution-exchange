<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ' :attribute 必须被接受.',
    'active_url' => ' :attribute 不是一个有效链接.',
    'after' => ':attribute 必须是 :date 之后的日期',
    'alpha' => ' :attribute 仅能包含字母',
    'alpha_dash' => ' :attribute 仅能包含字母，数字和破折号',
    'alpha_num' => ' :attribute 仅能包含字母和数字',
    'array' => ' :attribute 必须是数字串',
    'before' => ' :attribute 必须是 :date 之前的日期',
    'between' => [
        'numeric' => ' :attribute 是 :min 和 :max 之间的数字',
        'file' => ' :attribute 必须是大小 :min 和 :max kb之间的文件',
        'string' => ' :attribute 必须是 :min 和 :max 之间的字符',
        'array' => ' :attribute 必须是 :min 和 :max 之间的数字串',
    ],
    'boolean' => ' :attribute 必须是 true 或者 false.',
    'confirmed' => ' :attribute 不符合重新输入',
    'date' => ' :attribute 不是一个有效日期',
    'date_format' => ' :attribute 不符合 :format 格式',
    'different' => ' :attribute 和 :other 必须不同',
    'digits' => ' :attribute 必须是 :digits',
    'digits_between' => ' :attribute 必须是 :min 和 :max 之间的数字',
    'dimensions' => ' :attribute 的图片分辨率不适用',
    'distinct' => ' :attribute 必须是一个复制的值',
    'email' => ' :attribute 必须是一个有效的电子邮箱',
    'exists' => '被选择的 :attribute 无效',
    'file' => ' :attribute 必须是一个文件',
    'filled' => ' :attribute 必填',
    'image' => ' :attribute 必须是一张图片',
    'in' => '被选择的 :attribute 无效',
    'in_array' => ' :attribute 在 :other 中不存在',
    'integer' => ' :attribute 必须是整数',
    'ip' => ' :attribute 必须是一个有效的 IP 地址',
    'json' => ' :attribute 必须是一个有效的 JSON 字符串.',
    'max' => [
        'numeric' => ' :attribute 不能大于 :max.',
        'file' => ' :attribute 文件尺寸不能打过 :max kb',
        'string' => ' :attribute 不能超过 :max 字母',
        'array' => ' :attribute 不能超过 :max 数组',
    ],
    'mimes' => ' :attribute 必须是 : :values 文件',
    'mimetypes' => ' :attribute 必须是 : :values 文件',
    'min' => [
        'numeric' => ' :attribute 必须大于 :min.',
        'file' => ' :attribute 文件尺寸至少要有 :min kb',
        'string' => ' :attribute 必须是 :min 字母',
        'array' => ' :attribute 至少要有 :min 数组 ',
    ],
    'not_in' => ' 被选的 :attribute 无效',
    'numeric' => ' :attribute 必须是一个数字',
    'present' => ' :attribute 必须存在',
    'regex' => ' :attribute 格式无效',
    'required' => ' :attribute 是必须的',
    'required_if' => ' 当 :other 是 :value ，:attribute 是必须的',
    'required_unless' => '除非 :other 是 :values 否则 :attribute 是必须的',
    'required_with' => '当  :values 存在，则 :attribute 是必须的',
    'required_with_all' => '当 :values 存在，则 :attribute 是必须的',
    'required_without' => ' 当 :values 不存在，则 :attribute 是必须的',
    'required_without_all' => '当任何 :values 都不存在，则 :attribute 是必须的 .',
    'same' => ' :attribute 和 :other 必须匹配',
    'size' => [
        'numeric' => ' :attribute 大小必须是 :size.',
        'file' => ' :attribute 文件必须是 :size kb.',
        'string' => ' :attribute 必须是 :size 这些字母',
        'array' => ' :attribute 必须包含 :size 这些数组',
    ],
    'string' => ' :attribute 必须是字符',
    'timezone' => ' :attribute 必须是一个有效的时区',
    'unique' => ' :attribute 已存在',
    'uploaded' => ' :attribute 上传失败',
    'url' => ' :attribute 格式无效',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
];
