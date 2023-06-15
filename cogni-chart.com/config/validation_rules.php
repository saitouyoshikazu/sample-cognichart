<?php
return [
    'name'                      =>  [   'required',     'string',   'max:255'                                       ],
    'email'                     =>  [   'required',     'string',   'email',    'max:255',  'unique:users'          ],
    'password'                  =>  [   'required',     'string',   'min:6',    'confirmed'                         ],
    'superuser'                 =>  [   'sometimes',    'integer'                                                   ],
    'adminuser_id'              =>  [   'required',     'integer'                                                   ],
    'search_name'               =>  [   'sometimes',    'string',   'max:255',  'nullable'                          ],
    'sortcolumn'                =>  [   'sometimes',    'string'                                                    ],
    'sortdestination'           =>  [   'sometimes',    'string'                                                    ],
    'chart_phase'               =>  [   'required',     'string',   'regex:/^(released|provisioned)$/'              ],
    'chart_id'                  =>  [   'required',     'string',   'regex:/\A[0-9a-f]{32}\z/'                      ],
    'country_id'                =>  [   'required',     'string',   'regex:/\A[a-zA-Z]{2}\z/'                       ],
    'chart_name'                =>  [   'required',     'string',   'max:255',  'regex:/\A[!-\~ ]+\z/'              ],
    'scheme'                    =>  [   'required',     'string',   'max:255',  'regex:/\A[0-9a-zA-Z]+\z/'          ],
    'host'                      =>  [   'required',     'string',   'max:255',  'regex:/\A[!-\~]+\z/'               ],
    'uri'                       =>  [   'nullable',     'string',   'max:1000', 'regex:/\A[!-\~ ]+\z/'              ],
    'original_chart_name'       =>  [   'nullable',     'string',   'max:255',  'regex:/\A[!-\~ ]+\z/'              ],
    'page_title'                =>  [   'nullable',     'string',   'max:1000'                                      ],
    'chartterm_phase'           =>  [   'required',     'string',   'regex:/^(released|provisioned)$/'              ],
    'chartterm_id'              =>  [   'required',     'string',   'regex:/\A[0-9a-f]{32}\z/'                      ],
    'end_date'                  =>  [   'required',                 'date_format:Y-m-d'                             ],
    'artist_phase'              =>  [   'required',     'string',   'regex:/^(released|provisioned)$/'              ],
    'artist_id'                 =>  [   'required',     'string',   'regex:/\A[0-9a-f]{32}\z/'                      ],
    'artist_name'               =>  [   'required',     'string',   'max:255'                                       ],
    'itunes_artist_id'          =>  [   'required',     'string',   'regex:/\A[0-9a-f]{1,32}\z/'                    ],
    'music_phase'               =>  [   'required',     'string',   'regex:/^(released|provisioned)$/'              ],
    'music_id'                  =>  [   'required',     'string',   'regex:/\A[0-9a-f]{32}\z/'                      ],
    'music_title'               =>  [   'required',     'string',   'max:255'                                       ],
    'itunes_base_url'           =>  [   'nullable',     'string',   'regex:/\A[!-\~ ]+\z/'                          ],
    'promotion_video_url'       =>  [   'nullable',     'string',   'max:1000', 'regex:/\A[!-\~ ]+\z/'              ],
    'thumbnail_url'             =>  [   'nullable',     'string',   'max:1000', 'regex:/\A[!-\~ ]+\z/'              ],
    'chartrankingitem_id'       =>  [   'required',     'string',   'regex:/\A[0-9a-f]{32}\z/'                      ],
    'chart_artist'              =>  [   'required',     'string',   'max:255'                                       ],
    'chart_music'               =>  [   'required',     'string',   'max:255'                                       ],
    'search_artist_name'        =>  [   'nullable',     'string',   'max:255'                                       ],
    'search_itunes_artist_id'   =>  [   'nullable',     'string',   'regex:/\A[0-9a-f]{1,32}\z/'                    ],
    'search_music_title'        =>  [   'nullable',     'string',   'max:255'                                       ],
    'search_chart_artist'       =>  [   'nullable',     'string',   'max:255'                                       ],
    'search_chart_music'        =>  [   'nullable',     'string',   'max:255'                                       ],
    'music_ids'                 =>  [   'present',      'array'                                                     ],
    'music_ids.*'               =>  [   'required',     'string',   'regex:/\A[0-9a-f]{32}\z/'                      ],
];
