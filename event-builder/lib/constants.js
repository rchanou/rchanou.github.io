var CONSTANTS = {

    // vb enums

    CHECK_DETAIL_STATUS: {
          IS_NEW         : 1
        , HAS_VOIDED     : 2
        , CANNOT_DELETED : 3
    },
    CHECK_STATUS: {
          OPEN   : 0
        , CLOSED : 1
    },
    CHECK_TYPE: {
          SHOW_ALL : 0
        , REGULAR  : 1
        , EVENT    : 2
    },
    PAY_STATUS: {
          PAID : 1
        , VOID : 2
    },
    PAY_TYPE: {
          CASH          : 1
        , CREDIT        : 2
        , EXTERNAL      : 3
        , GIFT_CARD     : 4
        , VOUCHER       : 5
        , COMPLIMENTARY : 6
        , CHECK         : 7
        , GAME_CARD     : 8
        , DEBIT         : 9
        , SAGE_PAY      : 10
        , PAY_FLOW      : 11
    },
    PRODUCT_TYPE: {
          REGULAR     : 1
        , POINT       : 2
        , FOOD        : 3
        , RESERVATION : 4
        , GAME_CARD   : 5
        , MEMBERSHIP  : 6
        , GIFT_CARD   : 7
        , ENTITLE     : 8
    },
    RACE_BY: {
          TIME: 0
        , LAPS: 1
    },
    SALE_BY: {
          LAPS: 0
        , TIME: 1
    },
    SURVEY_SOURCE: {
          HEAT  : 0
        , EVENT : 1
        , CHECK : 2
    },
    WIN_BY: {
          TIME      : 0
        , POSITION  : 1
    },

    // receipt template constants

    PLACEHOLDERS: {
          CSLOGO      : "{{ClubSpeedLogo}}"
        , COMPANYLOGO : "{{CompanyLogo}}"
        , BARCODE     : "{{Barcode=###VAL###}}"
        , CUTPAPER    : "{{CutPaper}}"
        , SIGNATURE   : "{{SIGNATURE_SPLIT}}"
    },
    SIZES: {
        MAX_WIDTH: 42
    }
};

module.exports = CONSTANTS;
