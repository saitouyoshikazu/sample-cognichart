<?php
return [
    'country_id'            =>  [   'nullable',     'string',   'regex:/\A[a-zA-Z]{2}\z/'                       ],
    'chart_name'            =>  [   'nullable',     'string',   'max:255',  'regex:/\A[!-\~ ]+\z/'              ],
    'end_date'              =>  [   'nullable',                 'date_format:Y-m-d'                             ],
    'your_email_address'    =>  [   'required',     'string',   'email',    'max:255'                           ],
    'subject'               =>  [   'required',     'string',   'max:255'                                       ],
    'body_of_email'         =>  [   'required',     'string'                                                    ]
];
