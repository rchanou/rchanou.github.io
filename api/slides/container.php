<?php

error_reporting(E_ALL ^ E_NOTICE);

$json_laptime = <<<EOD
{
  "race": {
    "id": 9128,
    "track_id": 1,
    "track": "Rental",
    "starts_at": "2012-06-15 21:12:00.000",
    "heat_type_id": 1,
    "heat_status_id": 2,
    "speed_level_id": 6,
    "speed_level": "Adult Kart - Rental CW",
    "win_by": "laptime",
    "racers": [
      {
        "id": 1016233,
        "start_position": 5,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 2,
        "nickname": "CASEY COSTELLA"
      },
      {
        "id": 1016235,
        "start_position": 2,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 6,
        "nickname": "Bachelor"
      },
      {
        "id": 1016236,
        "start_position": 4,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 4,
        "nickname": "ethan"
      },
      {
        "id": 1016237,
        "start_position": 3,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 1,
        "nickname": "LIL-C"
      },
      {
        "id": 1016238,
        "start_position": 6,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 7,
        "nickname": "swat15"
      },
      {
        "id": 1016239,
        "start_position": 1,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 5,
        "nickname": "Rock"
      },
      {
        "id": 1016244,
        "start_position": 7,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 3,
        "nickname": "cas"
      },
      {
        "id": 1016249,
        "start_position": 8,
        "rpm": 1200,
        "is_first_time": 1,
        "finish_position": 8,
        "nickname": "William Stevens"
      }
    ],
    "laps": [
      {
        "id": 523719,
        "kart_number": 10,
        "lap_time": 0,
        "amb_time": 725303.195,
        "lap_number": 0,
        "racer_id": 1016237
      },
      {
        "id": 523720,
        "kart_number": 13,
        "lap_time": 0,
        "amb_time": 725303.905,
        "lap_number": 0,
        "racer_id": 1016239
      },
      {
        "id": 523721,
        "kart_number": 28,
        "lap_time": 0,
        "amb_time": 725304.145,
        "lap_number": 0,
        "racer_id": 1016233
      },
      {
        "id": 523722,
        "kart_number": 4,
        "lap_time": 0,
        "amb_time": 725305.356,
        "lap_number": 0,
        "racer_id": 1016236
      },
      {
        "id": 523723,
        "kart_number": 6,
        "lap_time": 0,
        "amb_time": 725306.254,
        "lap_number": 0,
        "racer_id": 1016235
      },
      {
        "id": 523724,
        "kart_number": 11,
        "lap_time": 0,
        "amb_time": 725309.216,
        "lap_number": 0,
        "racer_id": 1016238
      },
      {
        "id": 523725,
        "kart_number": 25,
        "lap_time": 0,
        "amb_time": 725309.341,
        "lap_number": 0,
        "racer_id": 1016249
      },
      {
        "id": 523726,
        "kart_number": 29,
        "lap_time": 0,
        "amb_time": 725309.564,
        "lap_number": 0,
        "racer_id": 1016244
      },
      {
        "id": 523727,
        "kart_number": 10,
        "lap_time": 34.266,
        "amb_time": 725337.461,
        "lap_number": 1,
        "racer_id": 1016237
      },
      {
        "id": 523728,
        "kart_number": 28,
        "lap_time": 34.741,
        "amb_time": 725338.886,
        "lap_number": 1,
        "racer_id": 1016233
      },
      {
        "id": 523729,
        "kart_number": 4,
        "lap_time": 34.505,
        "amb_time": 725339.861,
        "lap_number": 1,
        "racer_id": 1016236
      },
      {
        "id": 523730,
        "kart_number": 13,
        "lap_time": 36.935,
        "amb_time": 725340.84,
        "lap_number": 1,
        "racer_id": 1016239
      },
      {
        "id": 523731,
        "kart_number": 6,
        "lap_time": 37.402,
        "amb_time": 725343.656,
        "lap_number": 1,
        "racer_id": 1016235
      },
      {
        "id": 523732,
        "kart_number": 25,
        "lap_time": 35.362,
        "amb_time": 725344.703,
        "lap_number": 1,
        "racer_id": 1016249
      },
      {
        "id": 523733,
        "kart_number": 29,
        "lap_time": 36.822,
        "amb_time": 725346.386,
        "lap_number": 1,
        "racer_id": 1016244
      },
      {
        "id": 523734,
        "kart_number": 11,
        "lap_time": 38.929,
        "amb_time": 725348.145,
        "lap_number": 1,
        "racer_id": 1016238
      },
      {
        "id": 523735,
        "kart_number": 10,
        "lap_time": 35.339,
        "amb_time": 725372.8,
        "lap_number": 2,
        "racer_id": 1016237
      },
      {
        "id": 523736,
        "kart_number": 4,
        "lap_time": 33.261,
        "amb_time": 725373.122,
        "lap_number": 2,
        "racer_id": 1016236
      },
      {
        "id": 523737,
        "kart_number": 28,
        "lap_time": 34.353,
        "amb_time": 725373.239,
        "lap_number": 2,
        "racer_id": 1016233
      },
      {
        "id": 523738,
        "kart_number": 13,
        "lap_time": 35.122,
        "amb_time": 725375.962,
        "lap_number": 2,
        "racer_id": 1016239
      },
      {
        "id": 523739,
        "kart_number": 6,
        "lap_time": 38.142,
        "amb_time": 725381.798,
        "lap_number": 2,
        "racer_id": 1016235
      },
      {
        "id": 523740,
        "kart_number": 29,
        "lap_time": 35.719,
        "amb_time": 725382.105,
        "lap_number": 2,
        "racer_id": 1016244
      },
      {
        "id": 523741,
        "kart_number": 11,
        "lap_time": 38.468,
        "amb_time": 725386.613,
        "lap_number": 2,
        "racer_id": 1016238
      },
      {
        "id": 523742,
        "kart_number": 25,
        "lap_time": 46.207,
        "amb_time": 725390.91,
        "lap_number": 2,
        "racer_id": 1016249
      },
      {
        "id": 523743,
        "kart_number": 10,
        "lap_time": 35.653,
        "amb_time": 725408.453,
        "lap_number": 3,
        "racer_id": 1016237
      },
      {
        "id": 523744,
        "kart_number": 28,
        "lap_time": 36.33,
        "amb_time": 725409.569,
        "lap_number": 3,
        "racer_id": 1016233
      },
      {
        "id": 523745,
        "kart_number": 13,
        "lap_time": 35.157,
        "amb_time": 725411.119,
        "lap_number": 3,
        "racer_id": 1016239
      },
      {
        "id": 523746,
        "kart_number": 4,
        "lap_time": 40.342,
        "amb_time": 725413.464,
        "lap_number": 3,
        "racer_id": 1016236
      },
      {
        "id": 523747,
        "kart_number": 29,
        "lap_time": 33.586,
        "amb_time": 725415.691,
        "lap_number": 3,
        "racer_id": 1016244
      },
      {
        "id": 523748,
        "kart_number": 6,
        "lap_time": 35.638,
        "amb_time": 725417.436,
        "lap_number": 3,
        "racer_id": 1016235
      },
      {
        "id": 523749,
        "kart_number": 11,
        "lap_time": 36.718,
        "amb_time": 725423.331,
        "lap_number": 3,
        "racer_id": 1016238
      },
      {
        "id": 523750,
        "kart_number": 25,
        "lap_time": 41.819,
        "amb_time": 725432.729,
        "lap_number": 3,
        "racer_id": 1016249
      },
      {
        "id": 523751,
        "kart_number": 28,
        "lap_time": 35.415,
        "amb_time": 725444.984,
        "lap_number": 4,
        "racer_id": 1016233
      },
      {
        "id": 523752,
        "kart_number": 10,
        "lap_time": 37.184,
        "amb_time": 725445.637,
        "lap_number": 4,
        "racer_id": 1016237
      },
      {
        "id": 523753,
        "kart_number": 13,
        "lap_time": 35.162,
        "amb_time": 725446.281,
        "lap_number": 4,
        "racer_id": 1016239
      },
      {
        "id": 523754,
        "kart_number": 4,
        "lap_time": 33.309,
        "amb_time": 725446.773,
        "lap_number": 4,
        "racer_id": 1016236
      },
      {
        "id": 523755,
        "kart_number": 29,
        "lap_time": 33.729,
        "amb_time": 725449.42,
        "lap_number": 4,
        "racer_id": 1016244
      },
      {
        "id": 523756,
        "kart_number": 6,
        "lap_time": 34.939,
        "amb_time": 725452.375,
        "lap_number": 4,
        "racer_id": 1016235
      },
      {
        "id": 523757,
        "kart_number": 11,
        "lap_time": 35.763,
        "amb_time": 725459.094,
        "lap_number": 4,
        "racer_id": 1016238
      },
      {
        "id": 523758,
        "kart_number": 25,
        "lap_time": 35.842,
        "amb_time": 725468.571,
        "lap_number": 4,
        "racer_id": 1016249
      },
      {
        "id": 523759,
        "kart_number": 28,
        "lap_time": 33.697,
        "amb_time": 725478.681,
        "lap_number": 5,
        "racer_id": 1016233
      },
      {
        "id": 523760,
        "kart_number": 10,
        "lap_time": 33.566,
        "amb_time": 725479.203,
        "lap_number": 5,
        "racer_id": 1016237
      },
      {
        "id": 523761,
        "kart_number": 13,
        "lap_time": 33.793,
        "amb_time": 725480.074,
        "lap_number": 5,
        "racer_id": 1016239
      },
      {
        "id": 523762,
        "kart_number": 4,
        "lap_time": 33.777,
        "amb_time": 725480.55,
        "lap_number": 5,
        "racer_id": 1016236
      },
      {
        "id": 523763,
        "kart_number": 29,
        "lap_time": 32.903,
        "amb_time": 725482.323,
        "lap_number": 5,
        "racer_id": 1016244
      },
      {
        "id": 523764,
        "kart_number": 6,
        "lap_time": 42.068,
        "amb_time": 725494.443,
        "lap_number": 5,
        "racer_id": 1016235
      },
      {
        "id": 523765,
        "kart_number": 11,
        "lap_time": 35.698,
        "amb_time": 725494.792,
        "lap_number": 5,
        "racer_id": 1016238
      },
      {
        "id": 523766,
        "kart_number": 25,
        "lap_time": 36.122,
        "amb_time": 725504.693,
        "lap_number": 5,
        "racer_id": 1016249
      },
      {
        "id": 523767,
        "kart_number": 28,
        "lap_time": 33.656,
        "amb_time": 725512.337,
        "lap_number": 6,
        "racer_id": 1016233
      },
      {
        "id": 523768,
        "kart_number": 10,
        "lap_time": 33.411,
        "amb_time": 725512.614,
        "lap_number": 6,
        "racer_id": 1016237
      },
      {
        "id": 523769,
        "kart_number": 13,
        "lap_time": 34.031,
        "amb_time": 725514.105,
        "lap_number": 6,
        "racer_id": 1016239
      },
      {
        "id": 523770,
        "kart_number": 4,
        "lap_time": 34.015,
        "amb_time": 725514.565,
        "lap_number": 6,
        "racer_id": 1016236
      },
      {
        "id": 523771,
        "kart_number": 29,
        "lap_time": 32.827,
        "amb_time": 725515.15,
        "lap_number": 6,
        "racer_id": 1016244
      },
      {
        "id": 523772,
        "kart_number": 6,
        "lap_time": 34.78,
        "amb_time": 725529.223,
        "lap_number": 6,
        "racer_id": 1016235
      },
      {
        "id": 523773,
        "kart_number": 11,
        "lap_time": 35.209,
        "amb_time": 725530.001,
        "lap_number": 6,
        "racer_id": 1016238
      },
      {
        "id": 523774,
        "kart_number": 25,
        "lap_time": 35.667,
        "amb_time": 725540.36,
        "lap_number": 6,
        "racer_id": 1016249
      },
      {
        "id": 523775,
        "kart_number": 10,
        "lap_time": 32.817,
        "amb_time": 725545.431,
        "lap_number": 7,
        "racer_id": 1016237
      },
      {
        "id": 523776,
        "kart_number": 28,
        "lap_time": 33.692,
        "amb_time": 725546.029,
        "lap_number": 7,
        "racer_id": 1016233
      },
      {
        "id": 523777,
        "kart_number": 13,
        "lap_time": 33.552,
        "amb_time": 725547.657,
        "lap_number": 7,
        "racer_id": 1016239
      },
      {
        "id": 523778,
        "kart_number": 29,
        "lap_time": 33.219,
        "amb_time": 725548.369,
        "lap_number": 7,
        "racer_id": 1016244
      },
      {
        "id": 523779,
        "kart_number": 4,
        "lap_time": 34.278,
        "amb_time": 725548.843,
        "lap_number": 7,
        "racer_id": 1016236
      },
      {
        "id": 523780,
        "kart_number": 6,
        "lap_time": 35.242,
        "amb_time": 725564.465,
        "lap_number": 7,
        "racer_id": 1016235
      },
      {
        "id": 523781,
        "kart_number": 11,
        "lap_time": 35.32,
        "amb_time": 725565.321,
        "lap_number": 7,
        "racer_id": 1016238
      },
      {
        "id": 523782,
        "kart_number": 25,
        "lap_time": 34.845,
        "amb_time": 725575.205,
        "lap_number": 7,
        "racer_id": 1016249
      },
      {
        "id": 523783,
        "kart_number": 10,
        "lap_time": 31.765,
        "amb_time": 725577.196,
        "lap_number": 8,
        "racer_id": 1016237
      },
      {
        "id": 523784,
        "kart_number": 28,
        "lap_time": 33.777,
        "amb_time": 725579.806,
        "lap_number": 8,
        "racer_id": 1016233
      },
      {
        "id": 523785,
        "kart_number": 29,
        "lap_time": 32.879,
        "amb_time": 725581.248,
        "lap_number": 8,
        "racer_id": 1016244
      },
      {
        "id": 523786,
        "kart_number": 13,
        "lap_time": 33.664,
        "amb_time": 725581.321,
        "lap_number": 8,
        "racer_id": 1016239
      },
      {
        "id": 523787,
        "kart_number": 4,
        "lap_time": 33.04,
        "amb_time": 725581.883,
        "lap_number": 8,
        "racer_id": 1016236
      },
      {
        "id": 523788,
        "kart_number": 6,
        "lap_time": 34.915,
        "amb_time": 725599.38,
        "lap_number": 8,
        "racer_id": 1016235
      },
      {
        "id": 523789,
        "kart_number": 11,
        "lap_time": 34.557,
        "amb_time": 725599.878,
        "lap_number": 8,
        "racer_id": 1016238
      },
      {
        "id": 523790,
        "kart_number": 10,
        "lap_time": 31.852,
        "amb_time": 725609.048,
        "lap_number": 9,
        "racer_id": 1016237
      },
      {
        "id": 523791,
        "kart_number": 25,
        "lap_time": 36.508,
        "amb_time": 725611.713,
        "lap_number": 8,
        "racer_id": 1016249
      },
      {
        "id": 523792,
        "kart_number": 28,
        "lap_time": 32.305,
        "amb_time": 725612.111,
        "lap_number": 9,
        "racer_id": 1016233
      },
      {
        "id": 523793,
        "kart_number": 29,
        "lap_time": 32.242,
        "amb_time": 725613.49,
        "lap_number": 9,
        "racer_id": 1016244
      },
      {
        "id": 523794,
        "kart_number": 13,
        "lap_time": 33.337,
        "amb_time": 725614.658,
        "lap_number": 9,
        "racer_id": 1016239
      },
      {
        "id": 523795,
        "kart_number": 4,
        "lap_time": 33.23,
        "amb_time": 725615.113,
        "lap_number": 9,
        "racer_id": 1016236
      },
      {
        "id": 523796,
        "kart_number": 6,
        "lap_time": 34.602,
        "amb_time": 725633.982,
        "lap_number": 9,
        "racer_id": 1016235
      },
      {
        "id": 523797,
        "kart_number": 11,
        "lap_time": 34.271,
        "amb_time": 725634.149,
        "lap_number": 9,
        "racer_id": 1016238
      },
      {
        "id": 523798,
        "kart_number": 10,
        "lap_time": 31.554,
        "amb_time": 725640.602,
        "lap_number": 10,
        "racer_id": 1016237
      },
      {
        "id": 523799,
        "kart_number": 28,
        "lap_time": 32.856,
        "amb_time": 725644.967,
        "lap_number": 10,
        "racer_id": 1016233
      },
      {
        "id": 523800,
        "kart_number": 29,
        "lap_time": 32.462,
        "amb_time": 725645.952,
        "lap_number": 10,
        "racer_id": 1016244
      },
      {
        "id": 523801,
        "kart_number": 25,
        "lap_time": 35.804,
        "amb_time": 725647.517,
        "lap_number": 9,
        "racer_id": 1016249
      },
      {
        "id": 523802,
        "kart_number": 13,
        "lap_time": 33.332,
        "amb_time": 725647.99,
        "lap_number": 10,
        "racer_id": 1016239
      },
      {
        "id": 523803,
        "kart_number": 4,
        "lap_time": 34.327,
        "amb_time": 725649.44,
        "lap_number": 10,
        "racer_id": 1016236
      },
      {
        "id": 523804,
        "kart_number": 6,
        "lap_time": 34.865,
        "amb_time": 725668.847,
        "lap_number": 10,
        "racer_id": 1016235
      },
      {
        "id": 523805,
        "kart_number": 11,
        "lap_time": 34.95,
        "amb_time": 725669.099,
        "lap_number": 10,
        "racer_id": 1016238
      },
      {
        "id": 523806,
        "kart_number": 10,
        "lap_time": 32.065,
        "amb_time": 725672.667,
        "lap_number": 11,
        "racer_id": 1016237
      },
      {
        "id": 523807,
        "kart_number": 28,
        "lap_time": 32.792,
        "amb_time": 725677.759,
        "lap_number": 11,
        "racer_id": 1016233
      },
      {
        "id": 523808,
        "kart_number": 29,
        "lap_time": 32.227,
        "amb_time": 725678.179,
        "lap_number": 11,
        "racer_id": 1016244
      },
      {
        "id": 523809,
        "kart_number": 13,
        "lap_time": 34.011,
        "amb_time": 725682.001,
        "lap_number": 11,
        "racer_id": 1016239
      },
      {
        "id": 523810,
        "kart_number": 25,
        "lap_time": 35.378,
        "amb_time": 725682.895,
        "lap_number": 10,
        "racer_id": 1016249
      },
      {
        "id": 523811,
        "kart_number": 4,
        "lap_time": 33.973,
        "amb_time": 725683.413,
        "lap_number": 11,
        "racer_id": 1016236
      },
      {
        "id": 523812,
        "kart_number": 6,
        "lap_time": 33.416,
        "amb_time": 725702.263,
        "lap_number": 11,
        "racer_id": 1016235
      },
      {
        "id": 523813,
        "kart_number": 11,
        "lap_time": 34.033,
        "amb_time": 725703.132,
        "lap_number": 11,
        "racer_id": 1016238
      },
      {
        "id": 523814,
        "kart_number": 10,
        "lap_time": 31.325,
        "amb_time": 725703.992,
        "lap_number": 12,
        "racer_id": 1016237
      },
      {
        "id": 523815,
        "kart_number": 28,
        "lap_time": 32.232,
        "amb_time": 725709.991,
        "lap_number": 12,
        "racer_id": 1016233
      },
      {
        "id": 523816,
        "kart_number": 29,
        "lap_time": 32.507,
        "amb_time": 725710.686,
        "lap_number": 12,
        "racer_id": 1016244
      },
      {
        "id": 523817,
        "kart_number": 13,
        "lap_time": 32.971,
        "amb_time": 725714.972,
        "lap_number": 12,
        "racer_id": 1016239
      },
      {
        "id": 523818,
        "kart_number": 4,
        "lap_time": 33.51,
        "amb_time": 725716.923,
        "lap_number": 12,
        "racer_id": 1016236
      },
      {
        "id": 523819,
        "kart_number": 25,
        "lap_time": 35.205,
        "amb_time": 725718.1,
        "lap_number": 11,
        "racer_id": 1016249
      },
      {
        "id": 523820,
        "kart_number": 6,
        "lap_time": 34.257,
        "amb_time": 725736.52,
        "lap_number": 12,
        "racer_id": 1016235
      },
      {
        "id": 523821,
        "kart_number": 10,
        "lap_time": 32.653,
        "amb_time": 725736.645,
        "lap_number": 13,
        "racer_id": 1016237
      },
      {
        "id": 523822,
        "kart_number": 11,
        "lap_time": 34.11,
        "amb_time": 725737.242,
        "lap_number": 12,
        "racer_id": 1016238
      },
      {
        "id": 523823,
        "kart_number": 28,
        "lap_time": 31.867,
        "amb_time": 725741.858,
        "lap_number": 13,
        "racer_id": 1016233
      },
      {
        "id": 523824,
        "kart_number": 29,
        "lap_time": 32.002,
        "amb_time": 725742.688,
        "lap_number": 13,
        "racer_id": 1016244
      },
      {
        "id": 523825,
        "kart_number": 13,
        "lap_time": 33.409,
        "amb_time": 725748.381,
        "lap_number": 13,
        "racer_id": 1016239
      },
      {
        "id": 523826,
        "kart_number": 4,
        "lap_time": 32.39,
        "amb_time": 725749.313,
        "lap_number": 13,
        "racer_id": 1016236
      },
      {
        "id": 523827,
        "kart_number": 25,
        "lap_time": 34.947,
        "amb_time": 725753.047,
        "lap_number": 12,
        "racer_id": 1016249
      },
      {
        "id": 523828,
        "kart_number": 10,
        "lap_time": 32.341,
        "amb_time": 725768.986,
        "lap_number": 14,
        "racer_id": 1016237
      },
      {
        "id": 523829,
        "kart_number": 6,
        "lap_time": 33.813,
        "amb_time": 725770.333,
        "lap_number": 13,
        "racer_id": 1016235
      },
      {
        "id": 523830,
        "kart_number": 11,
        "lap_time": 33.744,
        "amb_time": 725770.986,
        "lap_number": 13,
        "racer_id": 1016238
      },
      {
        "id": 523831,
        "kart_number": 28,
        "lap_time": 32.468,
        "amb_time": 725774.326,
        "lap_number": 14,
        "racer_id": 1016233
      },
      {
        "id": 523832,
        "kart_number": 29,
        "lap_time": 32.51,
        "amb_time": 725775.198,
        "lap_number": 14,
        "racer_id": 1016244
      },
      {
        "id": 523833,
        "kart_number": 13,
        "lap_time": 33.887,
        "amb_time": 725782.268,
        "lap_number": 14,
        "racer_id": 1016239
      },
      {
        "id": 523834,
        "kart_number": 4,
        "lap_time": 33.356,
        "amb_time": 725782.669,
        "lap_number": 14,
        "racer_id": 1016236
      },
      {
        "id": 523835,
        "kart_number": 25,
        "lap_time": 35.665,
        "amb_time": 725788.712,
        "lap_number": 13,
        "racer_id": 1016249
      },
      {
        "id": 523836,
        "kart_number": 10,
        "lap_time": 31.369,
        "amb_time": 725800.355,
        "lap_number": 15,
        "racer_id": 1016237
      },
      {
        "id": 523837,
        "kart_number": 6,
        "lap_time": 34.409,
        "amb_time": 725804.742,
        "lap_number": 14,
        "racer_id": 1016235
      },
      {
        "id": 523838,
        "kart_number": 11,
        "lap_time": 34.505,
        "amb_time": 725805.491,
        "lap_number": 14,
        "racer_id": 1016238
      },
      {
        "id": 523839,
        "kart_number": 28,
        "lap_time": 31.818,
        "amb_time": 725806.144,
        "lap_number": 15,
        "racer_id": 1016233
      },
      {
        "id": 523840,
        "kart_number": 29,
        "lap_time": 31.821,
        "amb_time": 725807.019,
        "lap_number": 15,
        "racer_id": 1016244
      }
    ]
  }
}
EOD;

$json_position = <<<EOD
{
  "race": {
    "id": 10968,
    "track_id": 1,
    "track": "Rental",
    "starts_at": "2012-07-11 20:17:00.000",
    "heat_type_id": 16,
    "heat_status_id": 3,
    "speed_level_id": 1,
    "speed_level": "Adult Kart - Rental CCW",
    "win_by": "position",
    "racers": [
      {
        "id": 1000212,
        "start_position": 17,
        "rpm": 1071,
        "is_first_time": 0,
        "finish_position": 13,
        "nickname": "Hernaldo"
      },
      {
        "id": 1000219,
        "start_position": 14,
        "rpm": 1369,
        "is_first_time": 0,
        "finish_position": 11,
        "nickname": "Anthony Jensen"
      },
      {
        "id": 1001325,
        "start_position": 12,
        "rpm": 1460,
        "is_first_time": 0,
        "finish_position": 8,
        "nickname": "vetteX"
      },
      {
        "id": 1001575,
        "start_position": 2,
        "rpm": 2068,
        "is_first_time": 0,
        "finish_position": 5,
        "nickname": "KH504"
      },
      {
        "id": 1002154,
        "start_position": 4,
        "rpm": 2164,
        "is_first_time": 0,
        "finish_position": 2,
        "nickname": "RobbieV"
      },
      {
        "id": 1002210,
        "start_position": 3,
        "rpm": 1777,
        "is_first_time": 0,
        "finish_position": 4,
        "nickname": "Letitgokaboom"
      },
      {
        "id": 1003345,
        "start_position": 5,
        "rpm": 1896,
        "is_first_time": 0,
        "finish_position": 6,
        "nickname": "ZTR"
      },
      {
        "id": 1003475,
        "start_position": 1,
        "rpm": 2001,
        "is_first_time": 0,
        "finish_position": 1,
        "nickname": "Chuletas"
      },
      {
        "id": 1005722,
        "start_position": 10,
        "rpm": 2248,
        "is_first_time": 0,
        "finish_position": 3,
        "nickname": "504maxx"
      },
      {
        "id": 1008518,
        "start_position": 8,
        "rpm": 1822,
        "is_first_time": 0,
        "finish_position": 10,
        "nickname": "SEV0"
      },
      {
        "id": 1012365,
        "start_position": 11,
        "rpm": 1477,
        "is_first_time": 0,
        "finish_position": 17,
        "nickname": "Duna Lafountain"
      },
      {
        "id": 1015388,
        "start_position": 7,
        "rpm": 1542,
        "is_first_time": 0,
        "finish_position": 7,
        "nickname": "racingfreak225"
      },
      {
        "id": 1016137,
        "start_position": 19,
        "rpm": 741,
        "is_first_time": 0,
        "finish_position": 19,
        "nickname": "David"
      },
      {
        "id": 1018145,
        "start_position": 15,
        "rpm": 1226,
        "is_first_time": 0,
        "finish_position": 12,
        "nickname": "speedy"
      },
      {
        "id": 1018870,
        "start_position": 9,
        "rpm": 1527,
        "is_first_time": 0,
        "finish_position": 9,
        "nickname": "Mallets"
      },
      {
        "id": 1019833,
        "start_position": 18,
        "rpm": 1032,
        "is_first_time": 0,
        "finish_position": 18,
        "nickname": "Florida Gator"
      },
      {
        "id": 1019834,
        "start_position": 13,
        "rpm": 1075,
        "is_first_time": 0,
        "finish_position": 16,
        "nickname": "tnarg14"
      },
      {
        "id": 1020606,
        "start_position": 16,
        "rpm": 1121,
        "is_first_time": 0,
        "finish_position": 15,
        "nickname": "Crutch"
      },
      {
        "id": 1020645,
        "start_position": 6,
        "rpm": 1429,
        "is_first_time": 0,
        "finish_position": 14,
        "nickname": "Brian Edmonds"
      }
    ],
    "laps": [
      {
        "id": 659549,
        "kart_number": 14,
        "lap_time": 0,
        "amb_time": 1423485.944,
        "lap_number": 0,
        "racer_id": 1001575
      },
      {
        "id": 659550,
        "kart_number": 4,
        "lap_time": 0,
        "amb_time": 1423486.091,
        "lap_number": 0,
        "racer_id": 1003475
      },
      {
        "id": 659551,
        "kart_number": 1,
        "lap_time": 0,
        "amb_time": 1423486.582,
        "lap_number": 0,
        "racer_id": 1002154
      },
      {
        "id": 659552,
        "kart_number": 2,
        "lap_time": 0,
        "amb_time": 1423486.549,
        "lap_number": 0,
        "racer_id": 1002210
      },
      {
        "id": 659553,
        "kart_number": 24,
        "lap_time": 0,
        "amb_time": 1423486.852,
        "lap_number": 0,
        "racer_id": 1003345
      },
      {
        "id": 659554,
        "kart_number": 8,
        "lap_time": 0,
        "amb_time": 1423486.982,
        "lap_number": 0,
        "racer_id": 1020645
      },
      {
        "id": 659555,
        "kart_number": 5,
        "lap_time": 0,
        "amb_time": 1423487.23,
        "lap_number": 0,
        "racer_id": 1015388
      },
      {
        "id": 659556,
        "kart_number": 30,
        "lap_time": 0,
        "amb_time": 1423487.389,
        "lap_number": 0,
        "racer_id": 1008518
      },
      {
        "id": 659557,
        "kart_number": 16,
        "lap_time": 0,
        "amb_time": 1423487.675,
        "lap_number": 0,
        "racer_id": 1018870
      },
      {
        "id": 659558,
        "kart_number": 20,
        "lap_time": 0,
        "amb_time": 1423487.77,
        "lap_number": 0,
        "racer_id": 1005722
      },
      {
        "id": 659559,
        "kart_number": 13,
        "lap_time": 0,
        "amb_time": 1423488.252,
        "lap_number": 0,
        "racer_id": 1001325
      },
      {
        "id": 659560,
        "kart_number": 19,
        "lap_time": 0,
        "amb_time": 1423488.383,
        "lap_number": 0,
        "racer_id": 1012365
      },
      {
        "id": 659561,
        "kart_number": 3,
        "lap_time": 0,
        "amb_time": 1423488.627,
        "lap_number": 0,
        "racer_id": 1000219
      },
      {
        "id": 659562,
        "kart_number": 23,
        "lap_time": 0,
        "amb_time": 1423489.168,
        "lap_number": 0,
        "racer_id": 1019834
      },
      {
        "id": 659563,
        "kart_number": 21,
        "lap_time": 0,
        "amb_time": 1423489.208,
        "lap_number": 0,
        "racer_id": 1020606
      },
      {
        "id": 659564,
        "kart_number": 18,
        "lap_time": 0,
        "amb_time": 1423489.571,
        "lap_number": 0,
        "racer_id": 1018145
      },
      {
        "id": 659565,
        "kart_number": 11,
        "lap_time": 0,
        "amb_time": 1423490.082,
        "lap_number": 0,
        "racer_id": 1000212
      },
      {
        "id": 659566,
        "kart_number": 17,
        "lap_time": 0,
        "amb_time": 1423490.245,
        "lap_number": 0,
        "racer_id": 1019833
      },
      {
        "id": 659567,
        "kart_number": 27,
        "lap_time": 0,
        "amb_time": 1423490.76,
        "lap_number": 0,
        "racer_id": 1016137
      },
      {
        "id": 659568,
        "kart_number": 4,
        "lap_time": 38.508,
        "amb_time": 1423524.599,
        "lap_number": 1,
        "racer_id": 1003475
      },
      {
        "id": 659569,
        "kart_number": 1,
        "lap_time": 38.621,
        "amb_time": 1423525.203,
        "lap_number": 1,
        "racer_id": 1002154
      },
      {
        "id": 659570,
        "kart_number": 14,
        "lap_time": 39.288,
        "amb_time": 1423525.232,
        "lap_number": 1,
        "racer_id": 1001575
      },
      {
        "id": 659571,
        "kart_number": 2,
        "lap_time": 39.116,
        "amb_time": 1423525.665,
        "lap_number": 1,
        "racer_id": 1002210
      },
      {
        "id": 659572,
        "kart_number": 24,
        "lap_time": 39.476,
        "amb_time": 1423526.328,
        "lap_number": 1,
        "racer_id": 1003345
      },
      {
        "id": 659573,
        "kart_number": 5,
        "lap_time": 39.629,
        "amb_time": 1423526.859,
        "lap_number": 1,
        "racer_id": 1015388
      },
      {
        "id": 659574,
        "kart_number": 20,
        "lap_time": 39.294,
        "amb_time": 1423527.064,
        "lap_number": 1,
        "racer_id": 1005722
      },
      {
        "id": 659575,
        "kart_number": 16,
        "lap_time": 39.657,
        "amb_time": 1423527.332,
        "lap_number": 1,
        "racer_id": 1018870
      },
      {
        "id": 659576,
        "kart_number": 8,
        "lap_time": 40.833,
        "amb_time": 1423527.815,
        "lap_number": 1,
        "racer_id": 1020645
      },
      {
        "id": 659577,
        "kart_number": 13,
        "lap_time": 41.555,
        "amb_time": 1423529.807,
        "lap_number": 1,
        "racer_id": 1001325
      },
      {
        "id": 659578,
        "kart_number": 23,
        "lap_time": 42.137,
        "amb_time": 1423531.305,
        "lap_number": 1,
        "racer_id": 1019834
      },
      {
        "id": 659579,
        "kart_number": 18,
        "lap_time": 42.108,
        "amb_time": 1423531.679,
        "lap_number": 1,
        "racer_id": 1018145
      },
      {
        "id": 659580,
        "kart_number": 17,
        "lap_time": 41.94,
        "amb_time": 1423532.185,
        "lap_number": 1,
        "racer_id": 1019833
      },
      {
        "id": 659581,
        "kart_number": 11,
        "lap_time": 42.471,
        "amb_time": 1423532.553,
        "lap_number": 1,
        "racer_id": 1000212
      },
      {
        "id": 659582,
        "kart_number": 3,
        "lap_time": 44.13,
        "amb_time": 1423532.757,
        "lap_number": 1,
        "racer_id": 1000219
      },
      {
        "id": 659583,
        "kart_number": 30,
        "lap_time": 51.234,
        "amb_time": 1423538.623,
        "lap_number": 1,
        "racer_id": 1008518
      },
      {
        "id": 659584,
        "kart_number": 21,
        "lap_time": 59.012,
        "amb_time": 1423548.22,
        "lap_number": 1,
        "racer_id": 1020606
      },
      {
        "id": 659585,
        "kart_number": 4,
        "lap_time": 34.991,
        "amb_time": 1423559.59,
        "lap_number": 2,
        "racer_id": 1003475
      },
      {
        "id": 659586,
        "kart_number": 1,
        "lap_time": 35.406,
        "amb_time": 1423560.609,
        "lap_number": 2,
        "racer_id": 1002154
      },
      {
        "id": 659587,
        "kart_number": 14,
        "lap_time": 36.017,
        "amb_time": 1423561.249,
        "lap_number": 2,
        "racer_id": 1001575
      },
      {
        "id": 659588,
        "kart_number": 24,
        "lap_time": 35.298,
        "amb_time": 1423561.626,
        "lap_number": 2,
        "racer_id": 1003345
      },
      {
        "id": 659589,
        "kart_number": 2,
        "lap_time": 36.303,
        "amb_time": 1423561.968,
        "lap_number": 2,
        "racer_id": 1002210
      },
      {
        "id": 659590,
        "kart_number": 20,
        "lap_time": 35.076,
        "amb_time": 1423562.14,
        "lap_number": 2,
        "racer_id": 1005722
      },
      {
        "id": 659591,
        "kart_number": 5,
        "lap_time": 35.502,
        "amb_time": 1423562.361,
        "lap_number": 2,
        "racer_id": 1015388
      },
      {
        "id": 659592,
        "kart_number": 16,
        "lap_time": 37.445,
        "amb_time": 1423564.777,
        "lap_number": 2,
        "racer_id": 1018870
      },
      {
        "id": 659593,
        "kart_number": 8,
        "lap_time": 37.323,
        "amb_time": 1423565.138,
        "lap_number": 2,
        "racer_id": 1020645
      },
      {
        "id": 659594,
        "kart_number": 13,
        "lap_time": 35.958,
        "amb_time": 1423565.765,
        "lap_number": 2,
        "racer_id": 1001325
      },
      {
        "id": 659595,
        "kart_number": 23,
        "lap_time": 38.921,
        "amb_time": 1423570.226,
        "lap_number": 2,
        "racer_id": 1019834
      },
      {
        "id": 659596,
        "kart_number": 18,
        "lap_time": 38.912,
        "amb_time": 1423570.591,
        "lap_number": 2,
        "racer_id": 1018145
      },
      {
        "id": 659597,
        "kart_number": 17,
        "lap_time": 38.851,
        "amb_time": 1423571.036,
        "lap_number": 2,
        "racer_id": 1019833
      },
      {
        "id": 659598,
        "kart_number": 11,
        "lap_time": 38.732,
        "amb_time": 1423571.285,
        "lap_number": 2,
        "racer_id": 1000212
      },
      {
        "id": 659599,
        "kart_number": 3,
        "lap_time": 38.719,
        "amb_time": 1423571.476,
        "lap_number": 2,
        "racer_id": 1000219
      },
      {
        "id": 659600,
        "kart_number": 27,
        "lap_time": 81.571,
        "amb_time": 1423572.331,
        "lap_number": 1,
        "racer_id": 1016137
      },
      {
        "id": 659601,
        "kart_number": 30,
        "lap_time": 35.696,
        "amb_time": 1423574.319,
        "lap_number": 2,
        "racer_id": 1008518
      },
      {
        "id": 659602,
        "kart_number": 21,
        "lap_time": 37.938,
        "amb_time": 1423586.158,
        "lap_number": 2,
        "racer_id": 1020606
      },
      {
        "id": 659603,
        "kart_number": 4,
        "lap_time": 35.247,
        "amb_time": 1423594.837,
        "lap_number": 3,
        "racer_id": 1003475
      },
      {
        "id": 659604,
        "kart_number": 1,
        "lap_time": 34.461,
        "amb_time": 1423595.07,
        "lap_number": 3,
        "racer_id": 1002154
      },
      {
        "id": 659605,
        "kart_number": 14,
        "lap_time": 35.407,
        "amb_time": 1423596.656,
        "lap_number": 3,
        "racer_id": 1001575
      },
      {
        "id": 659606,
        "kart_number": 24,
        "lap_time": 35.094,
        "amb_time": 1423596.72,
        "lap_number": 3,
        "racer_id": 1003345
      },
      {
        "id": 659607,
        "kart_number": 2,
        "lap_time": 35.358,
        "amb_time": 1423597.326,
        "lap_number": 3,
        "racer_id": 1002210
      },
      {
        "id": 659608,
        "kart_number": 20,
        "lap_time": 35.41,
        "amb_time": 1423597.55,
        "lap_number": 3,
        "racer_id": 1005722
      },
      {
        "id": 659609,
        "kart_number": 5,
        "lap_time": 36.345,
        "amb_time": 1423598.706,
        "lap_number": 3,
        "racer_id": 1015388
      },
      {
        "id": 659610,
        "kart_number": 16,
        "lap_time": 36.978,
        "amb_time": 1423601.755,
        "lap_number": 3,
        "racer_id": 1018870
      },
      {
        "id": 659611,
        "kart_number": 13,
        "lap_time": 36.665,
        "amb_time": 1423602.43,
        "lap_number": 3,
        "racer_id": 1001325
      },
      {
        "id": 659612,
        "kart_number": 8,
        "lap_time": 37.437,
        "amb_time": 1423602.575,
        "lap_number": 3,
        "racer_id": 1020645
      },
      {
        "id": 659613,
        "kart_number": 18,
        "lap_time": 38.533,
        "amb_time": 1423609.124,
        "lap_number": 3,
        "racer_id": 1018145
      },
      {
        "id": 659614,
        "kart_number": 23,
        "lap_time": 39.029,
        "amb_time": 1423609.255,
        "lap_number": 3,
        "racer_id": 1019834
      },
      {
        "id": 659615,
        "kart_number": 11,
        "lap_time": 38.487,
        "amb_time": 1423609.772,
        "lap_number": 3,
        "racer_id": 1000212
      },
      {
        "id": 659616,
        "kart_number": 17,
        "lap_time": 38.836,
        "amb_time": 1423609.872,
        "lap_number": 3,
        "racer_id": 1019833
      },
      {
        "id": 659617,
        "kart_number": 3,
        "lap_time": 38.578,
        "amb_time": 1423610.054,
        "lap_number": 3,
        "racer_id": 1000219
      },
      {
        "id": 659618,
        "kart_number": 19,
        "lap_time": 121.715,
        "amb_time": 1423610.098,
        "lap_number": 1,
        "racer_id": 1012365
      },
      {
        "id": 659619,
        "kart_number": 30,
        "lap_time": 36.171,
        "amb_time": 1423610.49,
        "lap_number": 3,
        "racer_id": 1008518
      },
      {
        "id": 659620,
        "kart_number": 27,
        "lap_time": 39.339,
        "amb_time": 1423611.67,
        "lap_number": 2,
        "racer_id": 1016137
      },
      {
        "id": 659621,
        "kart_number": 21,
        "lap_time": 37.391,
        "amb_time": 1423623.549,
        "lap_number": 3,
        "racer_id": 1020606
      },
      {
        "id": 659622,
        "kart_number": 4,
        "lap_time": 34.674,
        "amb_time": 1423629.511,
        "lap_number": 4,
        "racer_id": 1003475
      },
      {
        "id": 659623,
        "kart_number": 1,
        "lap_time": 35.016,
        "amb_time": 1423630.086,
        "lap_number": 4,
        "racer_id": 1002154
      },
      {
        "id": 659624,
        "kart_number": 24,
        "lap_time": 34.739,
        "amb_time": 1423631.459,
        "lap_number": 4,
        "racer_id": 1003345
      },
      {
        "id": 659625,
        "kart_number": 14,
        "lap_time": 35.402,
        "amb_time": 1423632.058,
        "lap_number": 4,
        "racer_id": 1001575
      },
      {
        "id": 659626,
        "kart_number": 20,
        "lap_time": 35.166,
        "amb_time": 1423632.716,
        "lap_number": 4,
        "racer_id": 1005722
      },
      {
        "id": 659627,
        "kart_number": 2,
        "lap_time": 35.575,
        "amb_time": 1423632.901,
        "lap_number": 4,
        "racer_id": 1002210
      },
      {
        "id": 659628,
        "kart_number": 5,
        "lap_time": 35.061,
        "amb_time": 1423633.767,
        "lap_number": 4,
        "racer_id": 1015388
      },
      {
        "id": 659629,
        "kart_number": 16,
        "lap_time": 35.389,
        "amb_time": 1423637.144,
        "lap_number": 4,
        "racer_id": 1018870
      },
      {
        "id": 659630,
        "kart_number": 13,
        "lap_time": 35.496,
        "amb_time": 1423637.926,
        "lap_number": 4,
        "racer_id": 1001325
      },
      {
        "id": 659631,
        "kart_number": 8,
        "lap_time": 36.408,
        "amb_time": 1423638.983,
        "lap_number": 4,
        "racer_id": 1020645
      },
      {
        "id": 659632,
        "kart_number": 18,
        "lap_time": 37.061,
        "amb_time": 1423646.185,
        "lap_number": 4,
        "racer_id": 1018145
      },
      {
        "id": 659633,
        "kart_number": 11,
        "lap_time": 37.94,
        "amb_time": 1423647.712,
        "lap_number": 4,
        "racer_id": 1000212
      },
      {
        "id": 659634,
        "kart_number": 23,
        "lap_time": 38.529,
        "amb_time": 1423647.784,
        "lap_number": 4,
        "racer_id": 1019834
      },
      {
        "id": 659635,
        "kart_number": 30,
        "lap_time": 37.448,
        "amb_time": 1423647.938,
        "lap_number": 4,
        "racer_id": 1008518
      },
      {
        "id": 659636,
        "kart_number": 17,
        "lap_time": 38.251,
        "amb_time": 1423648.123,
        "lap_number": 4,
        "racer_id": 1019833
      },
      {
        "id": 659637,
        "kart_number": 19,
        "lap_time": 38.219,
        "amb_time": 1423648.317,
        "lap_number": 2,
        "racer_id": 1012365
      },
      {
        "id": 659638,
        "kart_number": 3,
        "lap_time": 38.306,
        "amb_time": 1423648.36,
        "lap_number": 4,
        "racer_id": 1000219
      },
      {
        "id": 659639,
        "kart_number": 27,
        "lap_time": 37.943,
        "amb_time": 1423649.613,
        "lap_number": 3,
        "racer_id": 1016137
      },
      {
        "id": 659640,
        "kart_number": 21,
        "lap_time": 36.616,
        "amb_time": 1423660.165,
        "lap_number": 4,
        "racer_id": 1020606
      },
      {
        "id": 659641,
        "kart_number": 4,
        "lap_time": 34.492,
        "amb_time": 1423664.003,
        "lap_number": 5,
        "racer_id": 1003475
      },
      {
        "id": 659642,
        "kart_number": 1,
        "lap_time": 35.217,
        "amb_time": 1423665.303,
        "lap_number": 5,
        "racer_id": 1002154
      },
      {
        "id": 659643,
        "kart_number": 24,
        "lap_time": 34.891,
        "amb_time": 1423666.35,
        "lap_number": 5,
        "racer_id": 1003345
      },
      {
        "id": 659644,
        "kart_number": 14,
        "lap_time": 35.067,
        "amb_time": 1423667.125,
        "lap_number": 5,
        "racer_id": 1001575
      },
      {
        "id": 659645,
        "kart_number": 20,
        "lap_time": 35.053,
        "amb_time": 1423667.769,
        "lap_number": 5,
        "racer_id": 1005722
      },
      {
        "id": 659646,
        "kart_number": 2,
        "lap_time": 35.134,
        "amb_time": 1423668.035,
        "lap_number": 5,
        "racer_id": 1002210
      },
      {
        "id": 659647,
        "kart_number": 5,
        "lap_time": 35.077,
        "amb_time": 1423668.844,
        "lap_number": 5,
        "racer_id": 1015388
      },
      {
        "id": 659648,
        "kart_number": 16,
        "lap_time": 35.973,
        "amb_time": 1423673.117,
        "lap_number": 5,
        "racer_id": 1018870
      },
      {
        "id": 659649,
        "kart_number": 13,
        "lap_time": 35.56,
        "amb_time": 1423673.486,
        "lap_number": 5,
        "racer_id": 1001325
      },
      {
        "id": 659650,
        "kart_number": 8,
        "lap_time": 36.597,
        "amb_time": 1423675.58,
        "lap_number": 5,
        "racer_id": 1020645
      },
      {
        "id": 659651,
        "kart_number": 18,
        "lap_time": 37.112,
        "amb_time": 1423683.297,
        "lap_number": 5,
        "racer_id": 1018145
      },
      {
        "id": 659652,
        "kart_number": 30,
        "lap_time": 37.105,
        "amb_time": 1423685.043,
        "lap_number": 5,
        "racer_id": 1008518
      },
      {
        "id": 659653,
        "kart_number": 3,
        "lap_time": 37.336,
        "amb_time": 1423685.696,
        "lap_number": 5,
        "racer_id": 1000219
      },
      {
        "id": 659654,
        "kart_number": 11,
        "lap_time": 38.149,
        "amb_time": 1423685.861,
        "lap_number": 5,
        "racer_id": 1000212
      },
      {
        "id": 659655,
        "kart_number": 19,
        "lap_time": 38.301,
        "amb_time": 1423686.618,
        "lap_number": 3,
        "racer_id": 1012365
      },
      {
        "id": 659656,
        "kart_number": 23,
        "lap_time": 39.453,
        "amb_time": 1423687.237,
        "lap_number": 5,
        "racer_id": 1019834
      },
      {
        "id": 659657,
        "kart_number": 17,
        "lap_time": 39.367,
        "amb_time": 1423687.49,
        "lap_number": 5,
        "racer_id": 1019833
      },
      {
        "id": 659658,
        "kart_number": 27,
        "lap_time": 38.416,
        "amb_time": 1423688.029,
        "lap_number": 4,
        "racer_id": 1016137
      },
      {
        "id": 659659,
        "kart_number": 21,
        "lap_time": 37.018,
        "amb_time": 1423697.183,
        "lap_number": 5,
        "racer_id": 1020606
      },
      {
        "id": 659660,
        "kart_number": 4,
        "lap_time": 34.62,
        "amb_time": 1423698.623,
        "lap_number": 6,
        "racer_id": 1003475
      },
      {
        "id": 659661,
        "kart_number": 1,
        "lap_time": 34.855,
        "amb_time": 1423700.158,
        "lap_number": 6,
        "racer_id": 1002154
      },
      {
        "id": 659662,
        "kart_number": 24,
        "lap_time": 34.578,
        "amb_time": 1423700.928,
        "lap_number": 6,
        "racer_id": 1003345
      },
      {
        "id": 659663,
        "kart_number": 14,
        "lap_time": 35.069,
        "amb_time": 1423702.194,
        "lap_number": 6,
        "racer_id": 1001575
      },
      {
        "id": 659664,
        "kart_number": 20,
        "lap_time": 34.943,
        "amb_time": 1423702.712,
        "lap_number": 6,
        "racer_id": 1005722
      },
      {
        "id": 659665,
        "kart_number": 2,
        "lap_time": 35.08,
        "amb_time": 1423703.115,
        "lap_number": 6,
        "racer_id": 1002210
      },
      {
        "id": 659666,
        "kart_number": 5,
        "lap_time": 34.709,
        "amb_time": 1423703.553,
        "lap_number": 6,
        "racer_id": 1015388
      },
      {
        "id": 659667,
        "kart_number": 16,
        "lap_time": 35.399,
        "amb_time": 1423708.516,
        "lap_number": 6,
        "racer_id": 1018870
      },
      {
        "id": 659668,
        "kart_number": 13,
        "lap_time": 35.712,
        "amb_time": 1423709.198,
        "lap_number": 6,
        "racer_id": 1001325
      },
      {
        "id": 659669,
        "kart_number": 8,
        "lap_time": 36.243,
        "amb_time": 1423711.823,
        "lap_number": 6,
        "racer_id": 1020645
      },
      {
        "id": 659670,
        "kart_number": 30,
        "lap_time": 35.523,
        "amb_time": 1423720.566,
        "lap_number": 6,
        "racer_id": 1008518
      },
      {
        "id": 659671,
        "kart_number": 18,
        "lap_time": 37.296,
        "amb_time": 1423720.593,
        "lap_number": 6,
        "racer_id": 1018145
      },
      {
        "id": 659672,
        "kart_number": 3,
        "lap_time": 35.263,
        "amb_time": 1423720.959,
        "lap_number": 6,
        "racer_id": 1000219
      },
      {
        "id": 659673,
        "kart_number": 11,
        "lap_time": 37.394,
        "amb_time": 1423723.255,
        "lap_number": 6,
        "racer_id": 1000212
      },
      {
        "id": 659674,
        "kart_number": 19,
        "lap_time": 36.821,
        "amb_time": 1423723.439,
        "lap_number": 4,
        "racer_id": 1012365
      },
      {
        "id": 659675,
        "kart_number": 23,
        "lap_time": 37.671,
        "amb_time": 1423724.908,
        "lap_number": 6,
        "racer_id": 1019834
      },
      {
        "id": 659676,
        "kart_number": 17,
        "lap_time": 37.616,
        "amb_time": 1423725.106,
        "lap_number": 6,
        "racer_id": 1019833
      },
      {
        "id": 659677,
        "kart_number": 27,
        "lap_time": 38.127,
        "amb_time": 1423726.156,
        "lap_number": 5,
        "racer_id": 1016137
      },
      {
        "id": 659678,
        "kart_number": 4,
        "lap_time": 35.461,
        "amb_time": 1423734.084,
        "lap_number": 7,
        "racer_id": 1003475
      },
      {
        "id": 659679,
        "kart_number": 21,
        "lap_time": 37.214,
        "amb_time": 1423734.397,
        "lap_number": 6,
        "racer_id": 1020606
      },
      {
        "id": 659680,
        "kart_number": 1,
        "lap_time": 34.533,
        "amb_time": 1423734.691,
        "lap_number": 7,
        "racer_id": 1002154
      },
      {
        "id": 659681,
        "kart_number": 24,
        "lap_time": 34.563,
        "amb_time": 1423735.491,
        "lap_number": 7,
        "racer_id": 1003345
      },
      {
        "id": 659682,
        "kart_number": 20,
        "lap_time": 34.932,
        "amb_time": 1423737.644,
        "lap_number": 7,
        "racer_id": 1005722
      },
      {
        "id": 659683,
        "kart_number": 14,
        "lap_time": 35.688,
        "amb_time": 1423737.882,
        "lap_number": 7,
        "racer_id": 1001575
      },
      {
        "id": 659684,
        "kart_number": 5,
        "lap_time": 35.615,
        "amb_time": 1423739.168,
        "lap_number": 7,
        "racer_id": 1015388
      },
      {
        "id": 659685,
        "kart_number": 2,
        "lap_time": 36.292,
        "amb_time": 1423739.407,
        "lap_number": 7,
        "racer_id": 1002210
      },
      {
        "id": 659686,
        "kart_number": 16,
        "lap_time": 35.433,
        "amb_time": 1423743.949,
        "lap_number": 7,
        "racer_id": 1018870
      },
      {
        "id": 659687,
        "kart_number": 13,
        "lap_time": 35.531,
        "amb_time": 1423744.729,
        "lap_number": 7,
        "racer_id": 1001325
      },
      {
        "id": 659688,
        "kart_number": 8,
        "lap_time": 36.286,
        "amb_time": 1423748.109,
        "lap_number": 7,
        "racer_id": 1020645
      },
      {
        "id": 659689,
        "kart_number": 30,
        "lap_time": 35.911,
        "amb_time": 1423756.477,
        "lap_number": 7,
        "racer_id": 1008518
      },
      {
        "id": 659690,
        "kart_number": 3,
        "lap_time": 36.486,
        "amb_time": 1423757.445,
        "lap_number": 7,
        "racer_id": 1000219
      },
      {
        "id": 659691,
        "kart_number": 18,
        "lap_time": 37.117,
        "amb_time": 1423757.71,
        "lap_number": 7,
        "racer_id": 1018145
      },
      {
        "id": 659692,
        "kart_number": 19,
        "lap_time": 36.417,
        "amb_time": 1423759.856,
        "lap_number": 5,
        "racer_id": 1012365
      },
      {
        "id": 659693,
        "kart_number": 11,
        "lap_time": 37.021,
        "amb_time": 1423760.276,
        "lap_number": 7,
        "racer_id": 1000212
      },
      {
        "id": 659694,
        "kart_number": 17,
        "lap_time": 37.164,
        "amb_time": 1423762.27,
        "lap_number": 7,
        "racer_id": 1019833
      },
      {
        "id": 659695,
        "kart_number": 23,
        "lap_time": 38.722,
        "amb_time": 1423763.63,
        "lap_number": 7,
        "racer_id": 1019834
      },
      {
        "id": 659696,
        "kart_number": 27,
        "lap_time": 38.109,
        "amb_time": 1423764.265,
        "lap_number": 6,
        "racer_id": 1016137
      },
      {
        "id": 659697,
        "kart_number": 4,
        "lap_time": 34.926,
        "amb_time": 1423769.01,
        "lap_number": 8,
        "racer_id": 1003475
      },
      {
        "id": 659698,
        "kart_number": 1,
        "lap_time": 34.473,
        "amb_time": 1423769.164,
        "lap_number": 8,
        "racer_id": 1002154
      },
      {
        "id": 659699,
        "kart_number": 24,
        "lap_time": 36.479,
        "amb_time": 1423771.97,
        "lap_number": 8,
        "racer_id": 1003345
      },
      {
        "id": 659700,
        "kart_number": 20,
        "lap_time": 35.093,
        "amb_time": 1423772.737,
        "lap_number": 8,
        "racer_id": 1005722
      },
      {
        "id": 659701,
        "kart_number": 14,
        "lap_time": 35.134,
        "amb_time": 1423773.016,
        "lap_number": 8,
        "racer_id": 1001575
      },
      {
        "id": 659702,
        "kart_number": 21,
        "lap_time": 39.17,
        "amb_time": 1423773.567,
        "lap_number": 7,
        "racer_id": 1020606
      },
      {
        "id": 659703,
        "kart_number": 5,
        "lap_time": 34.79,
        "amb_time": 1423773.958,
        "lap_number": 8,
        "racer_id": 1015388
      },
      {
        "id": 659704,
        "kart_number": 2,
        "lap_time": 35.008,
        "amb_time": 1423774.415,
        "lap_number": 8,
        "racer_id": 1002210
      },
      {
        "id": 659705,
        "kart_number": 16,
        "lap_time": 35.473,
        "amb_time": 1423779.422,
        "lap_number": 8,
        "racer_id": 1018870
      },
      {
        "id": 659706,
        "kart_number": 13,
        "lap_time": 35.382,
        "amb_time": 1423780.111,
        "lap_number": 8,
        "racer_id": 1001325
      },
      {
        "id": 659707,
        "kart_number": 30,
        "lap_time": 35.38,
        "amb_time": 1423791.857,
        "lap_number": 8,
        "racer_id": 1008518
      },
      {
        "id": 659708,
        "kart_number": 3,
        "lap_time": 35.655,
        "amb_time": 1423793.1,
        "lap_number": 8,
        "racer_id": 1000219
      },
      {
        "id": 659709,
        "kart_number": 18,
        "lap_time": 36.534,
        "amb_time": 1423794.244,
        "lap_number": 8,
        "racer_id": 1018145
      },
      {
        "id": 659710,
        "kart_number": 19,
        "lap_time": 35.444,
        "amb_time": 1423795.3,
        "lap_number": 6,
        "racer_id": 1012365
      },
      {
        "id": 659711,
        "kart_number": 11,
        "lap_time": 36.921,
        "amb_time": 1423797.197,
        "lap_number": 8,
        "racer_id": 1000212
      },
      {
        "id": 659712,
        "kart_number": 17,
        "lap_time": 35.986,
        "amb_time": 1423798.256,
        "lap_number": 8,
        "racer_id": 1019833
      },
      {
        "id": 659713,
        "kart_number": 23,
        "lap_time": 37.744,
        "amb_time": 1423801.374,
        "lap_number": 8,
        "racer_id": 1019834
      },
      {
        "id": 659714,
        "kart_number": 27,
        "lap_time": 37.453,
        "amb_time": 1423801.718,
        "lap_number": 7,
        "racer_id": 1016137
      },
      {
        "id": 659715,
        "kart_number": 4,
        "lap_time": 35.377,
        "amb_time": 1423804.387,
        "lap_number": 9,
        "racer_id": 1003475
      },
      {
        "id": 659716,
        "kart_number": 1,
        "lap_time": 35.721,
        "amb_time": 1423804.885,
        "lap_number": 9,
        "racer_id": 1002154
      },
      {
        "id": 659717,
        "kart_number": 24,
        "lap_time": 35.028,
        "amb_time": 1423806.998,
        "lap_number": 9,
        "racer_id": 1003345
      },
      {
        "id": 659718,
        "kart_number": 20,
        "lap_time": 34.918,
        "amb_time": 1423807.655,
        "lap_number": 9,
        "racer_id": 1005722
      },
      {
        "id": 659719,
        "kart_number": 14,
        "lap_time": 35.028,
        "amb_time": 1423808.044,
        "lap_number": 9,
        "racer_id": 1001575
      },
      {
        "id": 659720,
        "kart_number": 5,
        "lap_time": 35.147,
        "amb_time": 1423809.105,
        "lap_number": 9,
        "racer_id": 1015388
      },
      {
        "id": 659721,
        "kart_number": 2,
        "lap_time": 36.123,
        "amb_time": 1423810.538,
        "lap_number": 9,
        "racer_id": 1002210
      },
      {
        "id": 659722,
        "kart_number": 21,
        "lap_time": 37.665,
        "amb_time": 1423811.232,
        "lap_number": 8,
        "racer_id": 1020606
      },
      {
        "id": 659723,
        "kart_number": 16,
        "lap_time": 35.532,
        "amb_time": 1423814.954,
        "lap_number": 9,
        "racer_id": 1018870
      },
      {
        "id": 659724,
        "kart_number": 13,
        "lap_time": 35.437,
        "amb_time": 1423815.548,
        "lap_number": 9,
        "racer_id": 1001325
      },
      {
        "id": 659725,
        "kart_number": 30,
        "lap_time": 35.97,
        "amb_time": 1423827.827,
        "lap_number": 9,
        "racer_id": 1008518
      },
      {
        "id": 659726,
        "kart_number": 3,
        "lap_time": 35.407,
        "amb_time": 1423828.507,
        "lap_number": 9,
        "racer_id": 1000219
      },
      {
        "id": 659727,
        "kart_number": 18,
        "lap_time": 35.805,
        "amb_time": 1423830.049,
        "lap_number": 9,
        "racer_id": 1018145
      },
      {
        "id": 659728,
        "kart_number": 19,
        "lap_time": 35.427,
        "amb_time": 1423830.727,
        "lap_number": 7,
        "racer_id": 1012365
      },
      {
        "id": 659729,
        "kart_number": 11,
        "lap_time": 37.439,
        "amb_time": 1423834.636,
        "lap_number": 9,
        "racer_id": 1000212
      },
      {
        "id": 659730,
        "kart_number": 17,
        "lap_time": 36.821,
        "amb_time": 1423835.077,
        "lap_number": 9,
        "racer_id": 1019833
      },
      {
        "id": 659731,
        "kart_number": 23,
        "lap_time": 37.445,
        "amb_time": 1423838.819,
        "lap_number": 9,
        "racer_id": 1019834
      },
      {
        "id": 659732,
        "kart_number": 4,
        "lap_time": 34.875,
        "amb_time": 1423839.262,
        "lap_number": 10,
        "racer_id": 1003475
      },
      {
        "id": 659733,
        "kart_number": 1,
        "lap_time": 34.771,
        "amb_time": 1423839.656,
        "lap_number": 10,
        "racer_id": 1002154
      },
      {
        "id": 659734,
        "kart_number": 27,
        "lap_time": 38.376,
        "amb_time": 1423840.094,
        "lap_number": 8,
        "racer_id": 1016137
      },
      {
        "id": 659735,
        "kart_number": 24,
        "lap_time": 35.005,
        "amb_time": 1423842.003,
        "lap_number": 10,
        "racer_id": 1003345
      },
      {
        "id": 659736,
        "kart_number": 20,
        "lap_time": 34.644,
        "amb_time": 1423842.299,
        "lap_number": 10,
        "racer_id": 1005722
      },
      {
        "id": 659737,
        "kart_number": 14,
        "lap_time": 34.753,
        "amb_time": 1423842.797,
        "lap_number": 10,
        "racer_id": 1001575
      },
      {
        "id": 659738,
        "kart_number": 5,
        "lap_time": 34.942,
        "amb_time": 1423844.047,
        "lap_number": 10,
        "racer_id": 1015388
      },
      {
        "id": 659739,
        "kart_number": 2,
        "lap_time": 34.905,
        "amb_time": 1423845.443,
        "lap_number": 10,
        "racer_id": 1002210
      },
      {
        "id": 659740,
        "kart_number": 21,
        "lap_time": 37.27,
        "amb_time": 1423848.502,
        "lap_number": 9,
        "racer_id": 1020606
      },
      {
        "id": 659741,
        "kart_number": 16,
        "lap_time": 35.618,
        "amb_time": 1423850.572,
        "lap_number": 10,
        "racer_id": 1018870
      },
      {
        "id": 659742,
        "kart_number": 13,
        "lap_time": 35.463,
        "amb_time": 1423851.011,
        "lap_number": 10,
        "racer_id": 1001325
      },
      {
        "id": 659743,
        "kart_number": 10,
        "lap_time": 108.75,
        "amb_time": 1423856.859,
        "lap_number": 8,
        "racer_id": 1020645
      },
      {
        "id": 659744,
        "kart_number": 30,
        "lap_time": 35.372,
        "amb_time": 1423863.199,
        "lap_number": 10,
        "racer_id": 1008518
      },
      {
        "id": 659745,
        "kart_number": 3,
        "lap_time": 35.599,
        "amb_time": 1423864.106,
        "lap_number": 10,
        "racer_id": 1000219
      },
      {
        "id": 659746,
        "kart_number": 18,
        "lap_time": 36.147,
        "amb_time": 1423866.196,
        "lap_number": 10,
        "racer_id": 1018145
      },
      {
        "id": 659747,
        "kart_number": 19,
        "lap_time": 35.566,
        "amb_time": 1423866.293,
        "lap_number": 8,
        "racer_id": 1012365
      },
      {
        "id": 659748,
        "kart_number": 11,
        "lap_time": 37.786,
        "amb_time": 1423872.422,
        "lap_number": 10,
        "racer_id": 1000212
      },
      {
        "id": 659749,
        "kart_number": 17,
        "lap_time": 38.449,
        "amb_time": 1423873.526,
        "lap_number": 10,
        "racer_id": 1019833
      },
      {
        "id": 659750,
        "kart_number": 4,
        "lap_time": 35.216,
        "amb_time": 1423874.478,
        "lap_number": 11,
        "racer_id": 1003475
      },
      {
        "id": 659751,
        "kart_number": 1,
        "lap_time": 35.96,
        "amb_time": 1423875.616,
        "lap_number": 11,
        "racer_id": 1002154
      },
      {
        "id": 659752,
        "kart_number": 27,
        "lap_time": 37.226,
        "amb_time": 1423877.32,
        "lap_number": 9,
        "racer_id": 1016137
      },
      {
        "id": 659753,
        "kart_number": 24,
        "lap_time": 35.549,
        "amb_time": 1423877.552,
        "lap_number": 11,
        "racer_id": 1003345
      },
      {
        "id": 659754,
        "kart_number": 20,
        "lap_time": 35.554,
        "amb_time": 1423877.853,
        "lap_number": 11,
        "racer_id": 1005722
      },
      {
        "id": 659755,
        "kart_number": 14,
        "lap_time": 35.065,
        "amb_time": 1423877.862,
        "lap_number": 11,
        "racer_id": 1001575
      },
      {
        "id": 659756,
        "kart_number": 23,
        "lap_time": 39.587,
        "amb_time": 1423878.406,
        "lap_number": 10,
        "racer_id": 1019834
      },
      {
        "id": 659757,
        "kart_number": 5,
        "lap_time": 34.921,
        "amb_time": 1423878.968,
        "lap_number": 11,
        "racer_id": 1015388
      },
      {
        "id": 659758,
        "kart_number": 2,
        "lap_time": 34.659,
        "amb_time": 1423880.102,
        "lap_number": 11,
        "racer_id": 1002210
      },
      {
        "id": 659759,
        "kart_number": 16,
        "lap_time": 36.109,
        "amb_time": 1423886.681,
        "lap_number": 11,
        "racer_id": 1018870
      },
      {
        "id": 659760,
        "kart_number": 21,
        "lap_time": 38.783,
        "amb_time": 1423887.285,
        "lap_number": 10,
        "racer_id": 1020606
      },
      {
        "id": 659761,
        "kart_number": 13,
        "lap_time": 36.472,
        "amb_time": 1423887.483,
        "lap_number": 11,
        "racer_id": 1001325
      },
      {
        "id": 659762,
        "kart_number": 10,
        "lap_time": 35.757,
        "amb_time": 1423892.616,
        "lap_number": 9,
        "racer_id": 1020645
      },
      {
        "id": 659763,
        "kart_number": 30,
        "lap_time": 35.216,
        "amb_time": 1423898.415,
        "lap_number": 11,
        "racer_id": 1008518
      },
      {
        "id": 659764,
        "kart_number": 3,
        "lap_time": 35.321,
        "amb_time": 1423899.427,
        "lap_number": 11,
        "racer_id": 1000219
      },
      {
        "id": 659765,
        "kart_number": 18,
        "lap_time": 36.42,
        "amb_time": 1423902.616,
        "lap_number": 11,
        "racer_id": 1018145
      },
      {
        "id": 659766,
        "kart_number": 19,
        "lap_time": 36.504,
        "amb_time": 1423902.797,
        "lap_number": 9,
        "racer_id": 1012365
      },
      {
        "id": 659767,
        "kart_number": 11,
        "lap_time": 36.771,
        "amb_time": 1423909.193,
        "lap_number": 11,
        "racer_id": 1000212
      },
      {
        "id": 659768,
        "kart_number": 4,
        "lap_time": 35.754,
        "amb_time": 1423910.232,
        "lap_number": 12,
        "racer_id": 1003475
      },
      {
        "id": 659769,
        "kart_number": 1,
        "lap_time": 35.292,
        "amb_time": 1423910.908,
        "lap_number": 12,
        "racer_id": 1002154
      },
      {
        "id": 659770,
        "kart_number": 17,
        "lap_time": 37.857,
        "amb_time": 1423911.383,
        "lap_number": 11,
        "racer_id": 1019833
      },
      {
        "id": 659771,
        "kart_number": 24,
        "lap_time": 35.654,
        "amb_time": 1423913.206,
        "lap_number": 12,
        "racer_id": 1003345
      },
      {
        "id": 659772,
        "kart_number": 20,
        "lap_time": 35.502,
        "amb_time": 1423913.355,
        "lap_number": 12,
        "racer_id": 1005722
      },
      {
        "id": 659773,
        "kart_number": 14,
        "lap_time": 35.97,
        "amb_time": 1423913.832,
        "lap_number": 12,
        "racer_id": 1001575
      },
      {
        "id": 659774,
        "kart_number": 2,
        "lap_time": 36.72,
        "amb_time": 1423916.822,
        "lap_number": 12,
        "racer_id": 1002210
      },
      {
        "id": 659775,
        "kart_number": 27,
        "lap_time": 39.687,
        "amb_time": 1423917.007,
        "lap_number": 10,
        "racer_id": 1016137
      },
      {
        "id": 659776,
        "kart_number": 23,
        "lap_time": 39.425,
        "amb_time": 1423917.831,
        "lap_number": 11,
        "racer_id": 1019834
      },
      {
        "id": 659777,
        "kart_number": 5,
        "lap_time": 42.849,
        "amb_time": 1423921.817,
        "lap_number": 12,
        "racer_id": 1015388
      },
      {
        "id": 659778,
        "kart_number": 16,
        "lap_time": 35.896,
        "amb_time": 1423922.577,
        "lap_number": 12,
        "racer_id": 1018870
      },
      {
        "id": 659779,
        "kart_number": 13,
        "lap_time": 36.993,
        "amb_time": 1423924.476,
        "lap_number": 12,
        "racer_id": 1001325
      },
      {
        "id": 659780,
        "kart_number": 21,
        "lap_time": 37.749,
        "amb_time": 1423925.034,
        "lap_number": 11,
        "racer_id": 1020606
      },
      {
        "id": 659781,
        "kart_number": 10,
        "lap_time": 35.711,
        "amb_time": 1423928.327,
        "lap_number": 10,
        "racer_id": 1020645
      },
      {
        "id": 659782,
        "kart_number": 30,
        "lap_time": 35.511,
        "amb_time": 1423933.926,
        "lap_number": 12,
        "racer_id": 1008518
      },
      {
        "id": 659783,
        "kart_number": 3,
        "lap_time": 35.45,
        "amb_time": 1423934.877,
        "lap_number": 12,
        "racer_id": 1000219
      },
      {
        "id": 659784,
        "kart_number": 18,
        "lap_time": 36.215,
        "amb_time": 1423938.831,
        "lap_number": 12,
        "racer_id": 1018145
      },
      {
        "id": 659785,
        "kart_number": 19,
        "lap_time": 36.292,
        "amb_time": 1423939.089,
        "lap_number": 10,
        "racer_id": 1012365
      },
      {
        "id": 659786,
        "kart_number": 4,
        "lap_time": 35.799,
        "amb_time": 1423946.031,
        "lap_number": 13,
        "racer_id": 1003475
      },
      {
        "id": 659787,
        "kart_number": 1,
        "lap_time": 35.442,
        "amb_time": 1423946.35,
        "lap_number": 13,
        "racer_id": 1002154
      },
      {
        "id": 659788,
        "kart_number": 11,
        "lap_time": 37.719,
        "amb_time": 1423946.912,
        "lap_number": 12,
        "racer_id": 1000212
      },
      {
        "id": 659789,
        "kart_number": 14,
        "lap_time": 35.356,
        "amb_time": 1423949.188,
        "lap_number": 13,
        "racer_id": 1001575
      },
      {
        "id": 659790,
        "kart_number": 24,
        "lap_time": 35.991,
        "amb_time": 1423949.197,
        "lap_number": 13,
        "racer_id": 1003345
      },
      {
        "id": 659791,
        "kart_number": 20,
        "lap_time": 35.837,
        "amb_time": 1423949.192,
        "lap_number": 13,
        "racer_id": 1005722
      },
      {
        "id": 659792,
        "kart_number": 17,
        "lap_time": 38.192,
        "amb_time": 1423949.575,
        "lap_number": 12,
        "racer_id": 1019833
      },
      {
        "id": 659793,
        "kart_number": 2,
        "lap_time": 35.254,
        "amb_time": 1423952.076,
        "lap_number": 13,
        "racer_id": 1002210
      },
      {
        "id": 659794,
        "kart_number": 27,
        "lap_time": 38.318,
        "amb_time": 1423955.325,
        "lap_number": 11,
        "racer_id": 1016137
      },
      {
        "id": 659795,
        "kart_number": 23,
        "lap_time": 38.554,
        "amb_time": 1423956.385,
        "lap_number": 12,
        "racer_id": 1019834
      },
      {
        "id": 659796,
        "kart_number": 5,
        "lap_time": 35.412,
        "amb_time": 1423957.229,
        "lap_number": 13,
        "racer_id": 1015388
      },
      {
        "id": 659797,
        "kart_number": 16,
        "lap_time": 35.613,
        "amb_time": 1423958.19,
        "lap_number": 13,
        "racer_id": 1018870
      },
      {
        "id": 659798,
        "kart_number": 13,
        "lap_time": 35.614,
        "amb_time": 1423960.09,
        "lap_number": 13,
        "racer_id": 1001325
      },
      {
        "id": 659799,
        "kart_number": 21,
        "lap_time": 37.137,
        "amb_time": 1423962.171,
        "lap_number": 12,
        "racer_id": 1020606
      },
      {
        "id": 659800,
        "kart_number": 10,
        "lap_time": 35.417,
        "amb_time": 1423963.744,
        "lap_number": 11,
        "racer_id": 1020645
      },
      {
        "id": 659801,
        "kart_number": 30,
        "lap_time": 35.533,
        "amb_time": 1423969.459,
        "lap_number": 13,
        "racer_id": 1008518
      },
      {
        "id": 659802,
        "kart_number": 3,
        "lap_time": 35.254,
        "amb_time": 1423970.131,
        "lap_number": 13,
        "racer_id": 1000219
      },
      {
        "id": 659803,
        "kart_number": 18,
        "lap_time": 37.38,
        "amb_time": 1423976.211,
        "lap_number": 13,
        "racer_id": 1018145
      },
      {
        "id": 659804,
        "kart_number": 19,
        "lap_time": 37.221,
        "amb_time": 1423976.31,
        "lap_number": 11,
        "racer_id": 1012365
      },
      {
        "id": 659805,
        "kart_number": 4,
        "lap_time": 34.407,
        "amb_time": 1423980.438,
        "lap_number": 14,
        "racer_id": 1003475
      },
      {
        "id": 659806,
        "kart_number": 1,
        "lap_time": 34.566,
        "amb_time": 1423980.916,
        "lap_number": 14,
        "racer_id": 1002154
      },
      {
        "id": 659807,
        "kart_number": 11,
        "lap_time": 36.882,
        "amb_time": 1423983.794,
        "lap_number": 13,
        "racer_id": 1000212
      },
      {
        "id": 659808,
        "kart_number": 24,
        "lap_time": 35.864,
        "amb_time": 1423985.061,
        "lap_number": 14,
        "racer_id": 1003345
      },
      {
        "id": 659809,
        "kart_number": 14,
        "lap_time": 36.245,
        "amb_time": 1423985.433,
        "lap_number": 14,
        "racer_id": 1001575
      },
      {
        "id": 659810,
        "kart_number": 20,
        "lap_time": 38.125,
        "amb_time": 1423987.317,
        "lap_number": 14,
        "racer_id": 1005722
      },
      {
        "id": 659811,
        "kart_number": 2,
        "lap_time": 35.604,
        "amb_time": 1423987.68,
        "lap_number": 14,
        "racer_id": 1002210
      },
      {
        "id": 659812,
        "kart_number": 17,
        "lap_time": 38.828,
        "amb_time": 1423988.403,
        "lap_number": 13,
        "racer_id": 1019833
      },
      {
        "id": 659813,
        "kart_number": 5,
        "lap_time": 35.453,
        "amb_time": 1423992.682,
        "lap_number": 14,
        "racer_id": 1015388
      },
      {
        "id": 659814,
        "kart_number": 27,
        "lap_time": 37.725,
        "amb_time": 1423993.05,
        "lap_number": 12,
        "racer_id": 1016137
      },
      {
        "id": 659815,
        "kart_number": 16,
        "lap_time": 36.225,
        "amb_time": 1423994.415,
        "lap_number": 14,
        "racer_id": 1018870
      },
      {
        "id": 659816,
        "kart_number": 13,
        "lap_time": 35.596,
        "amb_time": 1423995.686,
        "lap_number": 14,
        "racer_id": 1001325
      },
      {
        "id": 659817,
        "kart_number": 23,
        "lap_time": 39.471,
        "amb_time": 1423995.856,
        "lap_number": 13,
        "racer_id": 1019834
      },
      {
        "id": 659818,
        "kart_number": 10,
        "lap_time": 35.582,
        "amb_time": 1423999.326,
        "lap_number": 12,
        "racer_id": 1020645
      },
      {
        "id": 659819,
        "kart_number": 21,
        "lap_time": 37.482,
        "amb_time": 1423999.653,
        "lap_number": 13,
        "racer_id": 1020606
      },
      {
        "id": 659820,
        "kart_number": 30,
        "lap_time": 35.476,
        "amb_time": 1424004.935,
        "lap_number": 14,
        "racer_id": 1008518
      },
      {
        "id": 659821,
        "kart_number": 3,
        "lap_time": 35.258,
        "amb_time": 1424005.389,
        "lap_number": 14,
        "racer_id": 1000219
      },
      {
        "id": 659822,
        "kart_number": 18,
        "lap_time": 36.78,
        "amb_time": 1424012.991,
        "lap_number": 14,
        "racer_id": 1018145
      },
      {
        "id": 659823,
        "kart_number": 19,
        "lap_time": 36.929,
        "amb_time": 1424013.239,
        "lap_number": 12,
        "racer_id": 1012365
      },
      {
        "id": 659824,
        "kart_number": 4,
        "lap_time": 34.832,
        "amb_time": 1424015.27,
        "lap_number": 15,
        "racer_id": 1003475
      },
      {
        "id": 659825,
        "kart_number": 1,
        "lap_time": 34.488,
        "amb_time": 1424015.404,
        "lap_number": 15,
        "racer_id": 1002154
      },
      {
        "id": 659826,
        "kart_number": 24,
        "lap_time": 35.72,
        "amb_time": 1424020.781,
        "lap_number": 15,
        "racer_id": 1003345
      },
      {
        "id": 659827,
        "kart_number": 14,
        "lap_time": 35.428,
        "amb_time": 1424020.861,
        "lap_number": 15,
        "racer_id": 1001575
      },
      {
        "id": 659828,
        "kart_number": 11,
        "lap_time": 37.461,
        "amb_time": 1424021.255,
        "lap_number": 14,
        "racer_id": 1000212
      },
      {
        "id": 659829,
        "kart_number": 20,
        "lap_time": 34.996,
        "amb_time": 1424022.313,
        "lap_number": 15,
        "racer_id": 1005722
      },
      {
        "id": 659830,
        "kart_number": 2,
        "lap_time": 34.827,
        "amb_time": 1424022.507,
        "lap_number": 15,
        "racer_id": 1002210
      },
      {
        "id": 659831,
        "kart_number": 17,
        "lap_time": 36.841,
        "amb_time": 1424025.244,
        "lap_number": 14,
        "racer_id": 1019833
      },
      {
        "id": 659832,
        "kart_number": 5,
        "lap_time": 35.649,
        "amb_time": 1424028.331,
        "lap_number": 15,
        "racer_id": 1015388
      },
      {
        "id": 659833,
        "kart_number": 16,
        "lap_time": 35.87,
        "amb_time": 1424030.285,
        "lap_number": 15,
        "racer_id": 1018870
      },
      {
        "id": 659834,
        "kart_number": 13,
        "lap_time": 35.42,
        "amb_time": 1424031.106,
        "lap_number": 15,
        "racer_id": 1001325
      },
      {
        "id": 659835,
        "kart_number": 23,
        "lap_time": 38.102,
        "amb_time": 1424033.958,
        "lap_number": 14,
        "racer_id": 1019834
      },
      {
        "id": 659836,
        "kart_number": 10,
        "lap_time": 35.889,
        "amb_time": 1424035.215,
        "lap_number": 13,
        "racer_id": 1020645
      },
      {
        "id": 659837,
        "kart_number": 21,
        "lap_time": 38.65,
        "amb_time": 1424038.303,
        "lap_number": 14,
        "racer_id": 1020606
      },
      {
        "id": 659838,
        "kart_number": 27,
        "lap_time": 46.196,
        "amb_time": 1424039.246,
        "lap_number": 13,
        "racer_id": 1016137
      },
      {
        "id": 659839,
        "kart_number": 30,
        "lap_time": 35.456,
        "amb_time": 1424040.391,
        "lap_number": 15,
        "racer_id": 1008518
      },
      {
        "id": 659840,
        "kart_number": 3,
        "lap_time": 35.525,
        "amb_time": 1424040.914,
        "lap_number": 15,
        "racer_id": 1000219
      },
      {
        "id": 659841,
        "kart_number": 18,
        "lap_time": 36.343,
        "amb_time": 1424049.334,
        "lap_number": 15,
        "racer_id": 1018145
      },
      {
        "id": 659842,
        "kart_number": 19,
        "lap_time": 36.217,
        "amb_time": 1424049.456,
        "lap_number": 13,
        "racer_id": 1012365
      },
      {
        "id": 659843,
        "kart_number": 4,
        "lap_time": 34.546,
        "amb_time": 1424049.816,
        "lap_number": 16,
        "racer_id": 1003475
      },
      {
        "id": 659844,
        "kart_number": 1,
        "lap_time": 34.794,
        "amb_time": 1424050.198,
        "lap_number": 16,
        "racer_id": 1002154
      },
      {
        "id": 659845,
        "kart_number": 14,
        "lap_time": 35.935,
        "amb_time": 1424056.796,
        "lap_number": 16,
        "racer_id": 1001575
      },
      {
        "id": 659846,
        "kart_number": 24,
        "lap_time": 36.686,
        "amb_time": 1424057.467,
        "lap_number": 16,
        "racer_id": 1003345
      },
      {
        "id": 659847,
        "kart_number": 20,
        "lap_time": 35.595,
        "amb_time": 1424057.908,
        "lap_number": 16,
        "racer_id": 1005722
      },
      {
        "id": 659848,
        "kart_number": 2,
        "lap_time": 35.793,
        "amb_time": 1424058.3,
        "lap_number": 16,
        "racer_id": 1002210
      },
      {
        "id": 659849,
        "kart_number": 11,
        "lap_time": 37.941,
        "amb_time": 1424059.196,
        "lap_number": 15,
        "racer_id": 1000212
      },
      {
        "id": 659850,
        "kart_number": 17,
        "lap_time": 37.082,
        "amb_time": 1424062.326,
        "lap_number": 15,
        "racer_id": 1019833
      },
      {
        "id": 659851,
        "kart_number": 5,
        "lap_time": 35.09,
        "amb_time": 1424063.421,
        "lap_number": 16,
        "racer_id": 1015388
      },
      {
        "id": 659852,
        "kart_number": 13,
        "lap_time": 35.87,
        "amb_time": 1424066.976,
        "lap_number": 16,
        "racer_id": 1001325
      },
      {
        "id": 659853,
        "kart_number": 16,
        "lap_time": 37.375,
        "amb_time": 1424067.66,
        "lap_number": 16,
        "racer_id": 1018870
      },
      {
        "id": 659854,
        "kart_number": 10,
        "lap_time": 35.994,
        "amb_time": 1424071.209,
        "lap_number": 14,
        "racer_id": 1020645
      },
      {
        "id": 659855,
        "kart_number": 23,
        "lap_time": 38.109,
        "amb_time": 1424072.067,
        "lap_number": 15,
        "racer_id": 1019834
      },
      {
        "id": 659856,
        "kart_number": 21,
        "lap_time": 37.005,
        "amb_time": 1424075.308,
        "lap_number": 15,
        "racer_id": 1020606
      },
      {
        "id": 659857,
        "kart_number": 27,
        "lap_time": 37.458,
        "amb_time": 1424076.704,
        "lap_number": 14,
        "racer_id": 1016137
      },
      {
        "id": 659858,
        "kart_number": 30,
        "lap_time": 36.518,
        "amb_time": 1424076.909,
        "lap_number": 16,
        "racer_id": 1008518
      },
      {
        "id": 659859,
        "kart_number": 3,
        "lap_time": 36.035,
        "amb_time": 1424076.949,
        "lap_number": 16,
        "racer_id": 1000219
      },
      {
        "id": 659860,
        "kart_number": 18,
        "lap_time": 36.624,
        "amb_time": 1424085.958,
        "lap_number": 16,
        "racer_id": 1018145
      },
      {
        "id": 659861,
        "kart_number": 19,
        "lap_time": 37.07,
        "amb_time": 1424086.526,
        "lap_number": 14,
        "racer_id": 1012365
      },
      {
        "id": 659862,
        "kart_number": 1,
        "lap_time": 36.465,
        "amb_time": 1424086.663,
        "lap_number": 17,
        "racer_id": 1002154
      },
      {
        "id": 659863,
        "kart_number": 4,
        "lap_time": 36.912,
        "amb_time": 1424086.728,
        "lap_number": 17,
        "racer_id": 1003475
      },
      {
        "id": 659864,
        "kart_number": 14,
        "lap_time": 35.2,
        "amb_time": 1424091.996,
        "lap_number": 17,
        "racer_id": 1001575
      },
      {
        "id": 659865,
        "kart_number": 24,
        "lap_time": 34.921,
        "amb_time": 1424092.388,
        "lap_number": 17,
        "racer_id": 1003345
      },
      {
        "id": 659866,
        "kart_number": 20,
        "lap_time": 34.874,
        "amb_time": 1424092.782,
        "lap_number": 17,
        "racer_id": 1005722
      },
      {
        "id": 659867,
        "kart_number": 2,
        "lap_time": 35.466,
        "amb_time": 1424093.766,
        "lap_number": 17,
        "racer_id": 1002210
      },
      {
        "id": 659868,
        "kart_number": 11,
        "lap_time": 38.817,
        "amb_time": 1424098.013,
        "lap_number": 16,
        "racer_id": 1000212
      },
      {
        "id": 659869,
        "kart_number": 17,
        "lap_time": 36.71,
        "amb_time": 1424099.036,
        "lap_number": 16,
        "racer_id": 1019833
      },
      {
        "id": 659870,
        "kart_number": 5,
        "lap_time": 35.749,
        "amb_time": 1424099.17,
        "lap_number": 17,
        "racer_id": 1015388
      },
      {
        "id": 659871,
        "kart_number": 13,
        "lap_time": 35.578,
        "amb_time": 1424102.554,
        "lap_number": 17,
        "racer_id": 1001325
      },
      {
        "id": 659872,
        "kart_number": 16,
        "lap_time": 35.696,
        "amb_time": 1424103.356,
        "lap_number": 17,
        "racer_id": 1018870
      },
      {
        "id": 659873,
        "kart_number": 10,
        "lap_time": 35.278,
        "amb_time": 1424106.487,
        "lap_number": 15,
        "racer_id": 1020645
      },
      {
        "id": 659874,
        "kart_number": 23,
        "lap_time": 37.821,
        "amb_time": 1424109.888,
        "lap_number": 16,
        "racer_id": 1019834
      },
      {
        "id": 659875,
        "kart_number": 21,
        "lap_time": 37.443,
        "amb_time": 1424112.751,
        "lap_number": 16,
        "racer_id": 1020606
      },
      {
        "id": 659876,
        "kart_number": 30,
        "lap_time": 36.04,
        "amb_time": 1424112.949,
        "lap_number": 17,
        "racer_id": 1008518
      },
      {
        "id": 659877,
        "kart_number": 3,
        "lap_time": 37.134,
        "amb_time": 1424114.083,
        "lap_number": 17,
        "racer_id": 1000219
      },
      {
        "id": 659878,
        "kart_number": 4,
        "lap_time": 36.174,
        "amb_time": 1424122.902,
        "lap_number": 18,
        "racer_id": 1003475
      },
      {
        "id": 659879,
        "kart_number": 19,
        "lap_time": 37.183,
        "amb_time": 1424123.709,
        "lap_number": 15,
        "racer_id": 1012365
      },
      {
        "id": 659880,
        "kart_number": 18,
        "lap_time": 38.336,
        "amb_time": 1424124.294,
        "lap_number": 17,
        "racer_id": 1018145
      },
      {
        "id": 659881,
        "kart_number": 1,
        "lap_time": 37.795,
        "amb_time": 1424124.458,
        "lap_number": 18,
        "racer_id": 1002154
      },
      {
        "id": 659882,
        "kart_number": 14,
        "lap_time": 36.152,
        "amb_time": 1424128.148,
        "lap_number": 18,
        "racer_id": 1001575
      },
      {
        "id": 659883,
        "kart_number": 24,
        "lap_time": 35.967,
        "amb_time": 1424128.355,
        "lap_number": 18,
        "racer_id": 1003345
      },
      {
        "id": 659884,
        "kart_number": 20,
        "lap_time": 35.772,
        "amb_time": 1424128.554,
        "lap_number": 18,
        "racer_id": 1005722
      },
      {
        "id": 659885,
        "kart_number": 2,
        "lap_time": 35.508,
        "amb_time": 1424129.274,
        "lap_number": 18,
        "racer_id": 1002210
      },
      {
        "id": 659886,
        "kart_number": 27,
        "lap_time": 54.16,
        "amb_time": 1424130.864,
        "lap_number": 15,
        "racer_id": 1016137
      },
      {
        "id": 659887,
        "kart_number": 5,
        "lap_time": 37.694,
        "amb_time": 1424136.864,
        "lap_number": 18,
        "racer_id": 1015388
      },
      {
        "id": 659888,
        "kart_number": 17,
        "lap_time": 38.479,
        "amb_time": 1424137.515,
        "lap_number": 17,
        "racer_id": 1019833
      },
      {
        "id": 659889,
        "kart_number": 11,
        "lap_time": 39.6,
        "amb_time": 1424137.613,
        "lap_number": 17,
        "racer_id": 1000212
      },
      {
        "id": 659890,
        "kart_number": 13,
        "lap_time": 36.154,
        "amb_time": 1424138.708,
        "lap_number": 18,
        "racer_id": 1001325
      },
      {
        "id": 659891,
        "kart_number": 16,
        "lap_time": 35.68,
        "amb_time": 1424139.036,
        "lap_number": 18,
        "racer_id": 1018870
      },
      {
        "id": 659892,
        "kart_number": 10,
        "lap_time": 35.572,
        "amb_time": 1424142.059,
        "lap_number": 16,
        "racer_id": 1020645
      },
      {
        "id": 659893,
        "kart_number": 23,
        "lap_time": 38.359,
        "amb_time": 1424148.247,
        "lap_number": 17,
        "racer_id": 1019834
      },
      {
        "id": 659894,
        "kart_number": 30,
        "lap_time": 35.594,
        "amb_time": 1424148.543,
        "lap_number": 18,
        "racer_id": 1008518
      },
      {
        "id": 659895,
        "kart_number": 3,
        "lap_time": 36.062,
        "amb_time": 1424150.145,
        "lap_number": 18,
        "racer_id": 1000219
      },
      {
        "id": 659896,
        "kart_number": 21,
        "lap_time": 37.997,
        "amb_time": 1424150.748,
        "lap_number": 17,
        "racer_id": 1020606
      },
      {
        "id": 659897,
        "kart_number": 4,
        "lap_time": 34.81,
        "amb_time": 1424157.712,
        "lap_number": 19,
        "racer_id": 1003475
      },
      {
        "id": 659898,
        "kart_number": 19,
        "lap_time": 35.551,
        "amb_time": 1424159.26,
        "lap_number": 16,
        "racer_id": 1012365
      },
      {
        "id": 659899,
        "kart_number": 1,
        "lap_time": 35.707,
        "amb_time": 1424160.165,
        "lap_number": 19,
        "racer_id": 1002154
      },
      {
        "id": 659900,
        "kart_number": 18,
        "lap_time": 37.443,
        "amb_time": 1424161.737,
        "lap_number": 18,
        "racer_id": 1018145
      },
      {
        "id": 659901,
        "kart_number": 14,
        "lap_time": 35.273,
        "amb_time": 1424163.421,
        "lap_number": 19,
        "racer_id": 1001575
      },
      {
        "id": 659902,
        "kart_number": 24,
        "lap_time": 35.219,
        "amb_time": 1424163.574,
        "lap_number": 19,
        "racer_id": 1003345
      },
      {
        "id": 659903,
        "kart_number": 20,
        "lap_time": 35.503,
        "amb_time": 1424164.057,
        "lap_number": 19,
        "racer_id": 1005722
      },
      {
        "id": 659904,
        "kart_number": 2,
        "lap_time": 35.094,
        "amb_time": 1424164.368,
        "lap_number": 19,
        "racer_id": 1002210
      },
      {
        "id": 659905,
        "kart_number": 27,
        "lap_time": 37.974,
        "amb_time": 1424168.838,
        "lap_number": 16,
        "racer_id": 1016137
      },
      {
        "id": 659906,
        "kart_number": 5,
        "lap_time": 36.281,
        "amb_time": 1424173.145,
        "lap_number": 19,
        "racer_id": 1015388
      },
      {
        "id": 659907,
        "kart_number": 17,
        "lap_time": 37.039,
        "amb_time": 1424174.554,
        "lap_number": 18,
        "racer_id": 1019833
      },
      {
        "id": 659908,
        "kart_number": 16,
        "lap_time": 36.047,
        "amb_time": 1424175.083,
        "lap_number": 19,
        "racer_id": 1018870
      },
      {
        "id": 659909,
        "kart_number": 13,
        "lap_time": 36.63,
        "amb_time": 1424175.338,
        "lap_number": 19,
        "racer_id": 1001325
      },
      {
        "id": 659910,
        "kart_number": 11,
        "lap_time": 39.671,
        "amb_time": 1424177.284,
        "lap_number": 18,
        "racer_id": 1000212
      },
      {
        "id": 659911,
        "kart_number": 10,
        "lap_time": 35.285,
        "amb_time": 1424177.344,
        "lap_number": 17,
        "racer_id": 1020645
      },
      {
        "id": 659912,
        "kart_number": 30,
        "lap_time": 35.76,
        "amb_time": 1424184.303,
        "lap_number": 19,
        "racer_id": 1008518
      },
      {
        "id": 659913,
        "kart_number": 3,
        "lap_time": 35.48,
        "amb_time": 1424185.625,
        "lap_number": 19,
        "racer_id": 1000219
      },
      {
        "id": 659914,
        "kart_number": 23,
        "lap_time": 38.103,
        "amb_time": 1424186.35,
        "lap_number": 18,
        "racer_id": 1019834
      },
      {
        "id": 659915,
        "kart_number": 21,
        "lap_time": 36.689,
        "amb_time": 1424187.437,
        "lap_number": 18,
        "racer_id": 1020606
      },
      {
        "id": 659916,
        "kart_number": 4,
        "lap_time": 34.966,
        "amb_time": 1424192.678,
        "lap_number": 20,
        "racer_id": 1003475
      },
      {
        "id": 659917,
        "kart_number": 19,
        "lap_time": 35.439,
        "amb_time": 1424194.699,
        "lap_number": 17,
        "racer_id": 1012365
      },
      {
        "id": 659918,
        "kart_number": 1,
        "lap_time": 34.66,
        "amb_time": 1424194.825,
        "lap_number": 20,
        "racer_id": 1002154
      },
      {
        "id": 659919,
        "kart_number": 18,
        "lap_time": 36.276,
        "amb_time": 1424198.013,
        "lap_number": 19,
        "racer_id": 1018145
      },
      {
        "id": 659920,
        "kart_number": 14,
        "lap_time": 35.376,
        "amb_time": 1424198.797,
        "lap_number": 20,
        "racer_id": 1001575
      },
      {
        "id": 659921,
        "kart_number": 20,
        "lap_time": 35.104,
        "amb_time": 1424199.161,
        "lap_number": 20,
        "racer_id": 1005722
      },
      {
        "id": 659922,
        "kart_number": 24,
        "lap_time": 35.596,
        "amb_time": 1424199.17,
        "lap_number": 20,
        "racer_id": 1003345
      },
      {
        "id": 659923,
        "kart_number": 2,
        "lap_time": 35.367,
        "amb_time": 1424199.735,
        "lap_number": 20,
        "racer_id": 1002210
      },
      {
        "id": 659924,
        "kart_number": 27,
        "lap_time": 38.359,
        "amb_time": 1424207.197,
        "lap_number": 17,
        "racer_id": 1016137
      },
      {
        "id": 659925,
        "kart_number": 5,
        "lap_time": 35.519,
        "amb_time": 1424208.664,
        "lap_number": 20,
        "racer_id": 1015388
      },
      {
        "id": 659926,
        "kart_number": 17,
        "lap_time": 37.168,
        "amb_time": 1424211.722,
        "lap_number": 19,
        "racer_id": 1019833
      },
      {
        "id": 659927,
        "kart_number": 13,
        "lap_time": 36.438,
        "amb_time": 1424211.776,
        "lap_number": 20,
        "racer_id": 1001325
      },
      {
        "id": 659928,
        "kart_number": 10,
        "lap_time": 35.613,
        "amb_time": 1424212.957,
        "lap_number": 18,
        "racer_id": 1020645
      },
      {
        "id": 659929,
        "kart_number": 16,
        "lap_time": 38.632,
        "amb_time": 1424213.715,
        "lap_number": 20,
        "racer_id": 1018870
      },
      {
        "id": 659930,
        "kart_number": 11,
        "lap_time": 38.016,
        "amb_time": 1424215.3,
        "lap_number": 19,
        "racer_id": 1000212
      },
      {
        "id": 659931,
        "kart_number": 30,
        "lap_time": 35.947,
        "amb_time": 1424220.25,
        "lap_number": 20,
        "racer_id": 1008518
      },
      {
        "id": 659932,
        "kart_number": 3,
        "lap_time": 35.402,
        "amb_time": 1424221.027,
        "lap_number": 20,
        "racer_id": 1000219
      },
      {
        "id": 659933,
        "kart_number": 23,
        "lap_time": 38.502,
        "amb_time": 1424224.852,
        "lap_number": 19,
        "racer_id": 1019834
      },
      {
        "id": 659934,
        "kart_number": 21,
        "lap_time": 38.067,
        "amb_time": 1424225.504,
        "lap_number": 19,
        "racer_id": 1020606
      },
      {
        "id": 659935,
        "kart_number": 4,
        "lap_time": 34.669,
        "amb_time": 1424227.347,
        "lap_number": 21,
        "racer_id": 1003475
      },
      {
        "id": 659936,
        "kart_number": 1,
        "lap_time": 35.514,
        "amb_time": 1424230.339,
        "lap_number": 21,
        "racer_id": 1002154
      },
      {
        "id": 659937,
        "kart_number": 19,
        "lap_time": 35.962,
        "amb_time": 1424230.661,
        "lap_number": 18,
        "racer_id": 1012365
      },
      {
        "id": 659938,
        "kart_number": 20,
        "lap_time": 35.201,
        "amb_time": 1424234.362,
        "lap_number": 21,
        "racer_id": 1005722
      },
      {
        "id": 659939,
        "kart_number": 18,
        "lap_time": 36.641,
        "amb_time": 1424234.654,
        "lap_number": 20,
        "racer_id": 1018145
      },
      {
        "id": 659940,
        "kart_number": 14,
        "lap_time": 36.37,
        "amb_time": 1424235.167,
        "lap_number": 21,
        "racer_id": 1001575
      },
      {
        "id": 659941,
        "kart_number": 24,
        "lap_time": 36.064,
        "amb_time": 1424235.234,
        "lap_number": 21,
        "racer_id": 1003345
      },
      {
        "id": 659942,
        "kart_number": 2,
        "lap_time": 35.641,
        "amb_time": 1424235.376,
        "lap_number": 21,
        "racer_id": 1002210
      },
      {
        "id": 659943,
        "kart_number": 5,
        "lap_time": 35.292,
        "amb_time": 1424243.956,
        "lap_number": 21,
        "racer_id": 1015388
      },
      {
        "id": 659944,
        "kart_number": 27,
        "lap_time": 38.455,
        "amb_time": 1424245.652,
        "lap_number": 18,
        "racer_id": 1016137
      },
      {
        "id": 659945,
        "kart_number": 13,
        "lap_time": 36.066,
        "amb_time": 1424247.842,
        "lap_number": 21,
        "racer_id": 1001325
      },
      {
        "id": 659946,
        "kart_number": 16,
        "lap_time": 36.577,
        "amb_time": 1424250.292,
        "lap_number": 21,
        "racer_id": 1018870
      },
      {
        "id": 659947,
        "kart_number": 10,
        "lap_time": 37.509,
        "amb_time": 1424250.466,
        "lap_number": 19,
        "racer_id": 1020645
      },
      {
        "id": 659948,
        "kart_number": 11,
        "lap_time": 38.279,
        "amb_time": 1424253.579,
        "lap_number": 20,
        "racer_id": 1000212
      },
      {
        "id": 659949,
        "kart_number": 30,
        "lap_time": 35.225,
        "amb_time": 1424255.475,
        "lap_number": 21,
        "racer_id": 1008518
      },
      {
        "id": 659950,
        "kart_number": 3,
        "lap_time": 35.389,
        "amb_time": 1424256.416,
        "lap_number": 21,
        "racer_id": 1000219
      },
      {
        "id": 659951,
        "kart_number": 23,
        "lap_time": 38.008,
        "amb_time": 1424262.86,
        "lap_number": 20,
        "racer_id": 1019834
      },
      {
        "id": 659952,
        "kart_number": 21,
        "lap_time": 37.584,
        "amb_time": 1424263.088,
        "lap_number": 20,
        "racer_id": 1020606
      },
      {
        "id": 659953,
        "kart_number": 4,
        "lap_time": 36.043,
        "amb_time": 1424263.39,
        "lap_number": 22,
        "racer_id": 1003475
      },
      {
        "id": 659954,
        "kart_number": 1,
        "lap_time": 34.385,
        "amb_time": 1424264.724,
        "lap_number": 22,
        "racer_id": 1002154
      },
      {
        "id": 659955,
        "kart_number": 19,
        "lap_time": 35.261,
        "amb_time": 1424265.922,
        "lap_number": 19,
        "racer_id": 1012365
      },
      {
        "id": 659956,
        "kart_number": 20,
        "lap_time": 34.999,
        "amb_time": 1424269.361,
        "lap_number": 22,
        "racer_id": 1005722
      },
      {
        "id": 659957,
        "kart_number": 14,
        "lap_time": 35.695,
        "amb_time": 1424270.862,
        "lap_number": 22,
        "racer_id": 1001575
      },
      {
        "id": 659958,
        "kart_number": 24,
        "lap_time": 36.15,
        "amb_time": 1424271.384,
        "lap_number": 22,
        "racer_id": 1003345
      },
      {
        "id": 659959,
        "kart_number": 2,
        "lap_time": 36.275,
        "amb_time": 1424271.651,
        "lap_number": 22,
        "racer_id": 1002210
      },
      {
        "id": 659960,
        "kart_number": 18,
        "lap_time": 37.298,
        "amb_time": 1424271.952,
        "lap_number": 21,
        "racer_id": 1018145
      },
      {
        "id": 659961,
        "kart_number": 5,
        "lap_time": 35.419,
        "amb_time": 1424279.375,
        "lap_number": 22,
        "racer_id": 1015388
      },
      {
        "id": 659962,
        "kart_number": 27,
        "lap_time": 37.426,
        "amb_time": 1424283.078,
        "lap_number": 19,
        "racer_id": 1016137
      },
      {
        "id": 659963,
        "kart_number": 13,
        "lap_time": 35.386,
        "amb_time": 1424283.228,
        "lap_number": 22,
        "racer_id": 1001325
      },
      {
        "id": 659964,
        "kart_number": 16,
        "lap_time": 35.502,
        "amb_time": 1424285.794,
        "lap_number": 22,
        "racer_id": 1018870
      },
      {
        "id": 659965,
        "kart_number": 10,
        "lap_time": 35.393,
        "amb_time": 1424285.859,
        "lap_number": 20,
        "racer_id": 1020645
      },
      {
        "id": 659966,
        "kart_number": 17,
        "lap_time": 77.585,
        "amb_time": 1424289.307,
        "lap_number": 20,
        "racer_id": 1019833
      },
      {
        "id": 659967,
        "kart_number": 30,
        "lap_time": 35.213,
        "amb_time": 1424290.688,
        "lap_number": 22,
        "racer_id": 1008518
      },
      {
        "id": 659968,
        "kart_number": 11,
        "lap_time": 37.946,
        "amb_time": 1424291.525,
        "lap_number": 21,
        "racer_id": 1000212
      },
      {
        "id": 659969,
        "kart_number": 3,
        "lap_time": 35.687,
        "amb_time": 1424292.103,
        "lap_number": 22,
        "racer_id": 1000219
      },
      {
        "id": 659970,
        "kart_number": 4,
        "lap_time": 36.173,
        "amb_time": 1424299.563,
        "lap_number": 23,
        "racer_id": 1003475
      },
      {
        "id": 659971,
        "kart_number": 1,
        "lap_time": 35.427,
        "amb_time": 1424300.151,
        "lap_number": 23,
        "racer_id": 1002154
      },
      {
        "id": 659972,
        "kart_number": 19,
        "lap_time": 35.5,
        "amb_time": 1424301.422,
        "lap_number": 20,
        "racer_id": 1012365
      },
      {
        "id": 659973,
        "kart_number": 23,
        "lap_time": 39.022,
        "amb_time": 1424301.882,
        "lap_number": 21,
        "racer_id": 1019834
      },
      {
        "id": 659974,
        "kart_number": 21,
        "lap_time": 38.85,
        "amb_time": 1424301.938,
        "lap_number": 21,
        "racer_id": 1020606
      },
      {
        "id": 659975,
        "kart_number": 20,
        "lap_time": 35.127,
        "amb_time": 1424304.488,
        "lap_number": 23,
        "racer_id": 1005722
      },
      {
        "id": 659976,
        "kart_number": 14,
        "lap_time": 35.094,
        "amb_time": 1424305.956,
        "lap_number": 23,
        "racer_id": 1001575
      },
      {
        "id": 659977,
        "kart_number": 24,
        "lap_time": 34.699,
        "amb_time": 1424306.083,
        "lap_number": 23,
        "racer_id": 1003345
      },
      {
        "id": 659978,
        "kart_number": 2,
        "lap_time": 34.973,
        "amb_time": 1424306.624,
        "lap_number": 23,
        "racer_id": 1002210
      },
      {
        "id": 659979,
        "kart_number": 18,
        "lap_time": 36.494,
        "amb_time": 1424308.446,
        "lap_number": 22,
        "racer_id": 1018145
      },
      {
        "id": 659980,
        "kart_number": 5,
        "lap_time": 35.443,
        "amb_time": 1424314.818,
        "lap_number": 23,
        "racer_id": 1015388
      },
      {
        "id": 659981,
        "kart_number": 13,
        "lap_time": 36.29,
        "amb_time": 1424319.518,
        "lap_number": 23,
        "racer_id": 1001325
      },
      {
        "id": 659982,
        "kart_number": 10,
        "lap_time": 35.862,
        "amb_time": 1424321.721,
        "lap_number": 21,
        "racer_id": 1020645
      },
      {
        "id": 659983,
        "kart_number": 16,
        "lap_time": 36.095,
        "amb_time": 1424321.889,
        "lap_number": 23,
        "racer_id": 1018870
      },
      {
        "id": 659984,
        "kart_number": 27,
        "lap_time": 38.907,
        "amb_time": 1424321.985,
        "lap_number": 20,
        "racer_id": 1016137
      },
      {
        "id": 659985,
        "kart_number": 30,
        "lap_time": 35.829,
        "amb_time": 1424326.517,
        "lap_number": 23,
        "racer_id": 1008518
      },
      {
        "id": 659986,
        "kart_number": 17,
        "lap_time": 37.566,
        "amb_time": 1424326.873,
        "lap_number": 21,
        "racer_id": 1019833
      },
      {
        "id": 659987,
        "kart_number": 3,
        "lap_time": 36.548,
        "amb_time": 1424328.651,
        "lap_number": 23,
        "racer_id": 1000219
      },
      {
        "id": 659988,
        "kart_number": 11,
        "lap_time": 37.405,
        "amb_time": 1424328.93,
        "lap_number": 22,
        "racer_id": 1000212
      },
      {
        "id": 659989,
        "kart_number": 4,
        "lap_time": 34.674,
        "amb_time": 1424334.237,
        "lap_number": 24,
        "racer_id": 1003475
      },
      {
        "id": 659990,
        "kart_number": 1,
        "lap_time": 34.329,
        "amb_time": 1424334.48,
        "lap_number": 24,
        "racer_id": 1002154
      },
      {
        "id": 659991,
        "kart_number": 19,
        "lap_time": 35.279,
        "amb_time": 1424336.701,
        "lap_number": 21,
        "racer_id": 1012365
      },
      {
        "id": 659992,
        "kart_number": 21,
        "lap_time": 37.298,
        "amb_time": 1424339.236,
        "lap_number": 22,
        "racer_id": 1020606
      },
      {
        "id": 659993,
        "kart_number": 20,
        "lap_time": 35.227,
        "amb_time": 1424339.715,
        "lap_number": 24,
        "racer_id": 1005722
      },
      {
        "id": 659994,
        "kart_number": 23,
        "lap_time": 39.085,
        "amb_time": 1424340.967,
        "lap_number": 22,
        "racer_id": 1019834
      },
      {
        "id": 659995,
        "kart_number": 24,
        "lap_time": 35.728,
        "amb_time": 1424341.811,
        "lap_number": 24,
        "racer_id": 1003345
      },
      {
        "id": 659996,
        "kart_number": 14,
        "lap_time": 36.077,
        "amb_time": 1424342.033,
        "lap_number": 24,
        "racer_id": 1001575
      },
      {
        "id": 659997,
        "kart_number": 2,
        "lap_time": 35.694,
        "amb_time": 1424342.318,
        "lap_number": 24,
        "racer_id": 1002210
      },
      {
        "id": 659998,
        "kart_number": 18,
        "lap_time": 36.516,
        "amb_time": 1424344.962,
        "lap_number": 23,
        "racer_id": 1018145
      },
      {
        "id": 659999,
        "kart_number": 5,
        "lap_time": 35.206,
        "amb_time": 1424350.024,
        "lap_number": 24,
        "racer_id": 1015388
      },
      {
        "id": 660000,
        "kart_number": 13,
        "lap_time": 35.406,
        "amb_time": 1424354.924,
        "lap_number": 24,
        "racer_id": 1001325
      },
      {
        "id": 660001,
        "kart_number": 10,
        "lap_time": 35.315,
        "amb_time": 1424357.036,
        "lap_number": 22,
        "racer_id": 1020645
      },
      {
        "id": 660002,
        "kart_number": 16,
        "lap_time": 35.846,
        "amb_time": 1424357.735,
        "lap_number": 24,
        "racer_id": 1018870
      },
      {
        "id": 660003,
        "kart_number": 30,
        "lap_time": 35.885,
        "amb_time": 1424362.402,
        "lap_number": 24,
        "racer_id": 1008518
      },
      {
        "id": 660004,
        "kart_number": 27,
        "lap_time": 41.017,
        "amb_time": 1424363.002,
        "lap_number": 21,
        "racer_id": 1016137
      },
      {
        "id": 660005,
        "kart_number": 17,
        "lap_time": 36.92,
        "amb_time": 1424363.793,
        "lap_number": 22,
        "racer_id": 1019833
      },
      {
        "id": 660006,
        "kart_number": 3,
        "lap_time": 35.526,
        "amb_time": 1424364.177,
        "lap_number": 24,
        "racer_id": 1000219
      },
      {
        "id": 660007,
        "kart_number": 11,
        "lap_time": 36.867,
        "amb_time": 1424365.797,
        "lap_number": 23,
        "racer_id": 1000212
      },
      {
        "id": 660008,
        "kart_number": 4,
        "lap_time": 34.513,
        "amb_time": 1424368.75,
        "lap_number": 25,
        "racer_id": 1003475
      },
      {
        "id": 660009,
        "kart_number": 1,
        "lap_time": 34.483,
        "amb_time": 1424368.963,
        "lap_number": 25,
        "racer_id": 1002154
      },
      {
        "id": 660010,
        "kart_number": 19,
        "lap_time": 35.251,
        "amb_time": 1424371.952,
        "lap_number": 22,
        "racer_id": 1012365
      },
      {
        "id": 660011,
        "kart_number": 20,
        "lap_time": 35.331,
        "amb_time": 1424375.046,
        "lap_number": 25,
        "racer_id": 1005722
      },
      {
        "id": 660012,
        "kart_number": 24,
        "lap_time": 35.44,
        "amb_time": 1424377.251,
        "lap_number": 25,
        "racer_id": 1003345
      },
      {
        "id": 660013,
        "kart_number": 14,
        "lap_time": 35.273,
        "amb_time": 1424377.306,
        "lap_number": 25,
        "racer_id": 1001575
      },
      {
        "id": 660014,
        "kart_number": 2,
        "lap_time": 35.433,
        "amb_time": 1424377.751,
        "lap_number": 25,
        "racer_id": 1002210
      },
      {
        "id": 660015,
        "kart_number": 23,
        "lap_time": 37.919,
        "amb_time": 1424378.886,
        "lap_number": 23,
        "racer_id": 1019834
      },
      {
        "id": 660016,
        "kart_number": 18,
        "lap_time": 36.001,
        "amb_time": 1424380.963,
        "lap_number": 24,
        "racer_id": 1018145
      },
      {
        "id": 660017,
        "kart_number": 21,
        "lap_time": 42.071,
        "amb_time": 1424381.307,
        "lap_number": 23,
        "racer_id": 1020606
      },
      {
        "id": 660018,
        "kart_number": 5,
        "lap_time": 35.211,
        "amb_time": 1424385.235,
        "lap_number": 25,
        "racer_id": 1015388
      },
      {
        "id": 660019,
        "kart_number": 13,
        "lap_time": 35.482,
        "amb_time": 1424390.406,
        "lap_number": 25,
        "racer_id": 1001325
      },
      {
        "id": 660020,
        "kart_number": 10,
        "lap_time": 35.25,
        "amb_time": 1424392.286,
        "lap_number": 23,
        "racer_id": 1020645
      },
      {
        "id": 660021,
        "kart_number": 16,
        "lap_time": 35.428,
        "amb_time": 1424393.163,
        "lap_number": 25,
        "racer_id": 1018870
      },
      {
        "id": 660022,
        "kart_number": 30,
        "lap_time": 35.592,
        "amb_time": 1424397.994,
        "lap_number": 25,
        "racer_id": 1008518
      },
      {
        "id": 660023,
        "kart_number": 27,
        "lap_time": 38.187,
        "amb_time": 1424401.189,
        "lap_number": 22,
        "racer_id": 1016137
      },
      {
        "id": 660024,
        "kart_number": 17,
        "lap_time": 37.59,
        "amb_time": 1424401.383,
        "lap_number": 23,
        "racer_id": 1019833
      },
      {
        "id": 660025,
        "kart_number": 3,
        "lap_time": 37.345,
        "amb_time": 1424401.522,
        "lap_number": 25,
        "racer_id": 1000219
      },
      {
        "id": 660026,
        "kart_number": 11,
        "lap_time": 37.458,
        "amb_time": 1424403.255,
        "lap_number": 24,
        "racer_id": 1000212
      },
      {
        "id": 660027,
        "kart_number": 4,
        "lap_time": 34.691,
        "amb_time": 1424403.441,
        "lap_number": 26,
        "racer_id": 1003475
      },
      {
        "id": 660028,
        "kart_number": 1,
        "lap_time": 34.55,
        "amb_time": 1424403.513,
        "lap_number": 26,
        "racer_id": 1002154
      },
      {
        "id": 660029,
        "kart_number": 19,
        "lap_time": 35.135,
        "amb_time": 1424407.087,
        "lap_number": 23,
        "racer_id": 1012365
      },
      {
        "id": 660030,
        "kart_number": 20,
        "lap_time": 34.939,
        "amb_time": 1424409.985,
        "lap_number": 26,
        "racer_id": 1005722
      },
      {
        "id": 660031,
        "kart_number": 14,
        "lap_time": 36.236,
        "amb_time": 1424413.542,
        "lap_number": 26,
        "racer_id": 1001575
      },
      {
        "id": 660032,
        "kart_number": 2,
        "lap_time": 36.027,
        "amb_time": 1424413.778,
        "lap_number": 26,
        "racer_id": 1002210
      },
      {
        "id": 660033,
        "kart_number": 24,
        "lap_time": 37.254,
        "amb_time": 1424414.505,
        "lap_number": 26,
        "racer_id": 1003345
      },
      {
        "id": 660034,
        "kart_number": 23,
        "lap_time": 38.037,
        "amb_time": 1424416.923,
        "lap_number": 24,
        "racer_id": 1019834
      },
      {
        "id": 660035,
        "kart_number": 18,
        "lap_time": 36.687,
        "amb_time": 1424417.65,
        "lap_number": 25,
        "racer_id": 1018145
      },
      {
        "id": 660036,
        "kart_number": 21,
        "lap_time": 36.691,
        "amb_time": 1424417.998,
        "lap_number": 24,
        "racer_id": 1020606
      },
      {
        "id": 660037,
        "kart_number": 5,
        "lap_time": 34.676,
        "amb_time": 1424419.911,
        "lap_number": 26,
        "racer_id": 1015388
      },
      {
        "id": 660038,
        "kart_number": 13,
        "lap_time": 35.567,
        "amb_time": 1424425.973,
        "lap_number": 26,
        "racer_id": 1001325
      },
      {
        "id": 660039,
        "kart_number": 10,
        "lap_time": 35.35,
        "amb_time": 1424427.636,
        "lap_number": 24,
        "racer_id": 1020645
      },
      {
        "id": 660040,
        "kart_number": 16,
        "lap_time": 35.176,
        "amb_time": 1424428.339,
        "lap_number": 26,
        "racer_id": 1018870
      },
      {
        "id": 660041,
        "kart_number": 30,
        "lap_time": 35.718,
        "amb_time": 1424433.712,
        "lap_number": 26,
        "racer_id": 1008518
      },
      {
        "id": 660042,
        "kart_number": 4,
        "lap_time": 35.737,
        "amb_time": 1424439.178,
        "lap_number": 27,
        "racer_id": 1003475
      },
      {
        "id": 660043,
        "kart_number": 17,
        "lap_time": 37.878,
        "amb_time": 1424439.261,
        "lap_number": 24,
        "racer_id": 1019833
      },
      {
        "id": 660044,
        "kart_number": 3,
        "lap_time": 38.801,
        "amb_time": 1424440.323,
        "lap_number": 26,
        "racer_id": 1000219
      },
      {
        "id": 660045,
        "kart_number": 1,
        "lap_time": 37.245,
        "amb_time": 1424440.758,
        "lap_number": 27,
        "racer_id": 1002154
      },
      {
        "id": 660046,
        "kart_number": 27,
        "lap_time": 39.968,
        "amb_time": 1424441.157,
        "lap_number": 23,
        "racer_id": 1016137
      },
      {
        "id": 660047,
        "kart_number": 11,
        "lap_time": 38.423,
        "amb_time": 1424441.678,
        "lap_number": 25,
        "racer_id": 1000212
      },
      {
        "id": 660048,
        "kart_number": 19,
        "lap_time": 35.274,
        "amb_time": 1424442.361,
        "lap_number": 24,
        "racer_id": 1012365
      },
      {
        "id": 660049,
        "kart_number": 20,
        "lap_time": 35.072,
        "amb_time": 1424445.057,
        "lap_number": 27,
        "racer_id": 1005722
      },
      {
        "id": 660050,
        "kart_number": 14,
        "lap_time": 34.942,
        "amb_time": 1424448.484,
        "lap_number": 27,
        "racer_id": 1001575
      },
      {
        "id": 660051,
        "kart_number": 2,
        "lap_time": 34.79,
        "amb_time": 1424448.568,
        "lap_number": 27,
        "racer_id": 1002210
      },
      {
        "id": 660052,
        "kart_number": 24,
        "lap_time": 34.889,
        "amb_time": 1424449.394,
        "lap_number": 27,
        "racer_id": 1003345
      },
      {
        "id": 660053,
        "kart_number": 5,
        "lap_time": 35.483,
        "amb_time": 1424455.394,
        "lap_number": 27,
        "racer_id": 1015388
      },
      {
        "id": 660054,
        "kart_number": 23,
        "lap_time": 38.821,
        "amb_time": 1424455.744,
        "lap_number": 25,
        "racer_id": 1019834
      },
      {
        "id": 660055,
        "kart_number": 18,
        "lap_time": 38.463,
        "amb_time": 1424456.113,
        "lap_number": 26,
        "racer_id": 1018145
      },
      {
        "id": 660056,
        "kart_number": 13,
        "lap_time": 35.685,
        "amb_time": 1424461.658,
        "lap_number": 27,
        "racer_id": 1001325
      },
      {
        "id": 660057,
        "kart_number": 21,
        "lap_time": 45.097,
        "amb_time": 1424463.095,
        "lap_number": 25,
        "racer_id": 1020606
      },
      {
        "id": 660058,
        "kart_number": 10,
        "lap_time": 35.797,
        "amb_time": 1424463.433,
        "lap_number": 25,
        "racer_id": 1020645
      },
      {
        "id": 660059,
        "kart_number": 16,
        "lap_time": 35.408,
        "amb_time": 1424463.747,
        "lap_number": 27,
        "racer_id": 1018870
      },
      {
        "id": 660060,
        "kart_number": 30,
        "lap_time": 35.543,
        "amb_time": 1424469.255,
        "lap_number": 27,
        "racer_id": 1008518
      },
      {
        "id": 660061,
        "kart_number": 4,
        "lap_time": 34.831,
        "amb_time": 1424474.009,
        "lap_number": 28,
        "racer_id": 1003475
      },
      {
        "id": 660062,
        "kart_number": 1,
        "lap_time": 34.753,
        "amb_time": 1424475.511,
        "lap_number": 28,
        "racer_id": 1002154
      },
      {
        "id": 660063,
        "kart_number": 17,
        "lap_time": 37.422,
        "amb_time": 1424476.683,
        "lap_number": 25,
        "racer_id": 1019833
      },
      {
        "id": 660064,
        "kart_number": 3,
        "lap_time": 36.452,
        "amb_time": 1424476.775,
        "lap_number": 27,
        "racer_id": 1000219
      },
      {
        "id": 660065,
        "kart_number": 27,
        "lap_time": 37.825,
        "amb_time": 1424478.982,
        "lap_number": 24,
        "racer_id": 1016137
      },
      {
        "id": 660066,
        "kart_number": 19,
        "lap_time": 36.768,
        "amb_time": 1424479.129,
        "lap_number": 25,
        "racer_id": 1012365
      },
      {
        "id": 660067,
        "kart_number": 11,
        "lap_time": 37.796,
        "amb_time": 1424479.474,
        "lap_number": 26,
        "racer_id": 1000212
      },
      {
        "id": 660068,
        "kart_number": 20,
        "lap_time": 34.64,
        "amb_time": 1424479.697,
        "lap_number": 28,
        "racer_id": 1005722
      },
      {
        "id": 660069,
        "kart_number": 2,
        "lap_time": 35.019,
        "amb_time": 1424483.587,
        "lap_number": 28,
        "racer_id": 1002210
      },
      {
        "id": 660070,
        "kart_number": 14,
        "lap_time": 36.06,
        "amb_time": 1424484.544,
        "lap_number": 28,
        "racer_id": 1001575
      },
      {
        "id": 660071,
        "kart_number": 24,
        "lap_time": 35.163,
        "amb_time": 1424484.557,
        "lap_number": 28,
        "racer_id": 1003345
      },
      {
        "id": 660072,
        "kart_number": 5,
        "lap_time": 35.002,
        "amb_time": 1424490.396,
        "lap_number": 28,
        "racer_id": 1015388
      },
      {
        "id": 660073,
        "kart_number": 23,
        "lap_time": 37.961,
        "amb_time": 1424493.705,
        "lap_number": 26,
        "racer_id": 1019834
      },
      {
        "id": 660074,
        "kart_number": 18,
        "lap_time": 37.743,
        "amb_time": 1424493.856,
        "lap_number": 27,
        "racer_id": 1018145
      },
      {
        "id": 660075,
        "kart_number": 13,
        "lap_time": 35.398,
        "amb_time": 1424497.056,
        "lap_number": 28,
        "racer_id": 1001325
      },
      {
        "id": 660076,
        "kart_number": 16,
        "lap_time": 35.794,
        "amb_time": 1424499.541,
        "lap_number": 28,
        "racer_id": 1018870
      },
      {
        "id": 660077,
        "kart_number": 10,
        "lap_time": 36.116,
        "amb_time": 1424499.549,
        "lap_number": 26,
        "racer_id": 1020645
      },
      {
        "id": 660078,
        "kart_number": 21,
        "lap_time": 37.42,
        "amb_time": 1424500.515,
        "lap_number": 26,
        "racer_id": 1020606
      },
      {
        "id": 660079,
        "kart_number": 30,
        "lap_time": 35.956,
        "amb_time": 1424505.211,
        "lap_number": 28,
        "racer_id": 1008518
      },
      {
        "id": 660080,
        "kart_number": 4,
        "lap_time": 34.688,
        "amb_time": 1424508.697,
        "lap_number": 29,
        "racer_id": 1003475
      },
      {
        "id": 660081,
        "kart_number": 1,
        "lap_time": 34.399,
        "amb_time": 1424509.91,
        "lap_number": 29,
        "racer_id": 1002154
      },
      {
        "id": 660082,
        "kart_number": 3,
        "lap_time": 35.889,
        "amb_time": 1424512.664,
        "lap_number": 28,
        "racer_id": 1000219
      },
      {
        "id": 660083,
        "kart_number": 17,
        "lap_time": 38.436,
        "amb_time": 1424515.119,
        "lap_number": 26,
        "racer_id": 1019833
      },
      {
        "id": 660084,
        "kart_number": 20,
        "lap_time": 36.054,
        "amb_time": 1424515.751,
        "lap_number": 29,
        "racer_id": 1005722
      },
      {
        "id": 660085,
        "kart_number": 19,
        "lap_time": 37.58,
        "amb_time": 1424516.709,
        "lap_number": 26,
        "racer_id": 1012365
      },
      {
        "id": 660086,
        "kart_number": 11,
        "lap_time": 38.102,
        "amb_time": 1424517.576,
        "lap_number": 27,
        "racer_id": 1000212
      },
      {
        "id": 660087,
        "kart_number": 27,
        "lap_time": 38.917,
        "amb_time": 1424517.899,
        "lap_number": 25,
        "racer_id": 1016137
      },
      {
        "id": 660088,
        "kart_number": 2,
        "lap_time": 35.203,
        "amb_time": 1424518.79,
        "lap_number": 29,
        "racer_id": 1002210
      },
      {
        "id": 660089,
        "kart_number": 14,
        "lap_time": 35.114,
        "amb_time": 1424519.658,
        "lap_number": 29,
        "racer_id": 1001575
      },
      {
        "id": 660090,
        "kart_number": 24,
        "lap_time": 35.199,
        "amb_time": 1424519.756,
        "lap_number": 29,
        "racer_id": 1003345
      },
      {
        "id": 660091,
        "kart_number": 5,
        "lap_time": 34.797,
        "amb_time": 1424525.193,
        "lap_number": 29,
        "racer_id": 1015388
      },
      {
        "id": 660092,
        "kart_number": 18,
        "lap_time": 37.844,
        "amb_time": 1424531.7,
        "lap_number": 28,
        "racer_id": 1018145
      },
      {
        "id": 660093,
        "kart_number": 23,
        "lap_time": 38.935,
        "amb_time": 1424532.64,
        "lap_number": 27,
        "racer_id": 1019834
      },
      {
        "id": 660094,
        "kart_number": 13,
        "lap_time": 35.851,
        "amb_time": 1424532.907,
        "lap_number": 29,
        "racer_id": 1001325
      },
      {
        "id": 660095,
        "kart_number": 10,
        "lap_time": 36.331,
        "amb_time": 1424535.88,
        "lap_number": 27,
        "racer_id": 1020645
      },
      {
        "id": 660096,
        "kart_number": 16,
        "lap_time": 36.557,
        "amb_time": 1424536.098,
        "lap_number": 29,
        "racer_id": 1018870
      },
      {
        "id": 660097,
        "kart_number": 21,
        "lap_time": 36.578,
        "amb_time": 1424537.093,
        "lap_number": 27,
        "racer_id": 1020606
      },
      {
        "id": 660098,
        "kart_number": 30,
        "lap_time": 35.391,
        "amb_time": 1424540.602,
        "lap_number": 29,
        "racer_id": 1008518
      },
      {
        "id": 660099,
        "kart_number": 4,
        "lap_time": 34.645,
        "amb_time": 1424543.342,
        "lap_number": 30,
        "racer_id": 1003475
      },
      {
        "id": 660100,
        "kart_number": 1,
        "lap_time": 34.19,
        "amb_time": 1424544.1,
        "lap_number": 30,
        "racer_id": 1002154
      },
      {
        "id": 660101,
        "kart_number": 3,
        "lap_time": 35.418,
        "amb_time": 1424548.082,
        "lap_number": 29,
        "racer_id": 1000219
      },
      {
        "id": 660102,
        "kart_number": 20,
        "lap_time": 35.842,
        "amb_time": 1424551.593,
        "lap_number": 30,
        "racer_id": 1005722
      },
      {
        "id": 660103,
        "kart_number": 19,
        "lap_time": 35.18,
        "amb_time": 1424551.889,
        "lap_number": 27,
        "racer_id": 1012365
      },
      {
        "id": 660104,
        "kart_number": 2,
        "lap_time": 35.622,
        "amb_time": 1424554.412,
        "lap_number": 30,
        "racer_id": 1002210
      },
      {
        "id": 660105,
        "kart_number": 14,
        "lap_time": 35.716,
        "amb_time": 1424555.374,
        "lap_number": 30,
        "racer_id": 1001575
      },
      {
        "id": 660106,
        "kart_number": 24,
        "lap_time": 35.7,
        "amb_time": 1424555.456,
        "lap_number": 30,
        "racer_id": 1003345
      },
      {
        "id": 660107,
        "kart_number": 11,
        "lap_time": 38.575,
        "amb_time": 1424556.151,
        "lap_number": 28,
        "racer_id": 1000212
      },
      {
        "id": 660108,
        "kart_number": 27,
        "lap_time": 38.56,
        "amb_time": 1424556.459,
        "lap_number": 26,
        "racer_id": 1016137
      },
      {
        "id": 660109,
        "kart_number": 5,
        "lap_time": 35.029,
        "amb_time": 1424560.222,
        "lap_number": 30,
        "racer_id": 1015388
      },
      {
        "id": 660110,
        "kart_number": 17,
        "lap_time": 47.982,
        "amb_time": 1424563.101,
        "lap_number": 27,
        "racer_id": 1019833
      },
      {
        "id": 660111,
        "kart_number": 13,
        "lap_time": 36.536,
        "amb_time": 1424569.443,
        "lap_number": 30,
        "racer_id": 1001325
      },
      {
        "id": 660112,
        "kart_number": 18,
        "lap_time": 37.821,
        "amb_time": 1424569.521,
        "lap_number": 29,
        "racer_id": 1018145
      },
      {
        "id": 660113,
        "kart_number": 10,
        "lap_time": 35.83,
        "amb_time": 1424571.71,
        "lap_number": 28,
        "racer_id": 1020645
      },
      {
        "id": 660114,
        "kart_number": 16,
        "lap_time": 35.787,
        "amb_time": 1424571.885,
        "lap_number": 30,
        "racer_id": 1018870
      },
      {
        "id": 660115,
        "kart_number": 21,
        "lap_time": 36.477,
        "amb_time": 1424573.57,
        "lap_number": 28,
        "racer_id": 1020606
      },
      {
        "id": 660116,
        "kart_number": 30,
        "lap_time": 35.936,
        "amb_time": 1424576.538,
        "lap_number": 30,
        "racer_id": 1008518
      }
    ]
  }
}
EOD;

$res = json_decode($json_position);

$racers = array();

echo '<pre>';
foreach($res->race->racers as $racer) {
	$racers[$racer->id] = (array)$racer;
}

foreach($res->race->laps as $key => $lap) {
	if(empty($first_crossing_time)) $first_crossing_time = $lap->amb_time;
	$racers[$lap->racer_id]['laps'][] = $lap;
	$racers[$lap->racer_id]['total_cs_time'] = $lap->lap_time + $racers[$lap->racer_id]['total_cs_time'];
	$racers[$lap->racer_id]['total_amb_time'] = $lap->amb_time - $first_crossing_time;
	$racers[$lap->racer_id]['best_lap'] = (!empty($lap->lap_time) && $lap->lap_time < $racers[$lap->racer_id]['best_lap']) ? $lap->lap_time : $racers[$lap->racer_id]['best_lap'];
	if($lap->win_by == 'position') {
		// Gap is based on next by position
	} elseif($lap->win_by == 'laptime') {
		// Gap is based on next fastest lap
	}
	if($lap->lap_number == 0) {
		$racers[$lap->racer_id]['best_lap'] = 999;
	}
}

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

aasort($racers, 'finish_position');

echo '<ul>';
$i = 0;
foreach($racers as $key => $line) {
	$last_lap = $line['laps'][(count($line['laps'])-1)]->lap_time;
	$gap = empty($last_guy) ? '-' : round($line['total_cs_time'] - $racers[$last_guy]['total_cs_time'], 3);
	
	// Calculate GAP
	if(empty($last_guy)) { // We're first
		$gapa = $gapc = '-';
		$lead_lap = count($line['laps'])-1;
	} elseif(count($line['laps'])-1 == $lead_lap) { // On lead lap
		$gapa = round($line['total_amb_time'] - $racers[$last_guy]['total_amb_time'], 3);
		$gapc += $gapa;
	} else { // Down a lap
		$gapc = ($lead_lap - (count($line['laps']) - 1)) . 'L';
		$gapa = '-';
	}
	
	echo '<li>RACERID' . $key . ' POS'. $line['finish_position'] . ' BEST:' . $line['best_lap'] . ' NICK:' . $line['nickname'] . ' CSTIME:' . $line['total_cs_time'] . ' AMB' . $line['total_amb_time'] . ' LAPS:' . (count($line['laps'])-1) . ' AVG:' . (round($line['total_cs_time']/(count($line['laps'])-1), 3)) . ' LASTLAP:' . $last_lap . ' GAP CS:' . $gap . ' GAP AMB TO FIRST:' . $gapc . ' GAP NEXT:' . $gapa . '</li>';
	$last_guy = $key;
	$i++;
}
echo '</ul>';
//print_r($race->race);