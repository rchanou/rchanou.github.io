<?php

class Countries {

    private $countries = array();

    function __construct() {
        $this->countries = array(
              array(
                  'Name'               => 'Abkhazia'
                , 'ISO_3166-1_Alpha_2' => 'GE'
                , 'ISO_3166-1_Alpha_3' => 'GEO'
                , 'ISO_3166-1_Numeric' => '268'
            )
            , array(
                  'Name'               => 'Afghanistan'
                , 'ISO_3166-1_Alpha_2' => 'AF'
                , 'ISO_3166-1_Alpha_3' => 'AFG'
                , 'ISO_3166-1_Numeric' => '4'
            )
            , array(
                  'Name'               => 'Aland'
                , 'ISO_3166-1_Alpha_2' => 'AX'
                , 'ISO_3166-1_Alpha_3' => 'ALA'
                , 'ISO_3166-1_Numeric' => '248'
            )
            , array(
                  'Name'               => 'Albania'
                , 'ISO_3166-1_Alpha_2' => 'AL'
                , 'ISO_3166-1_Alpha_3' => 'ALB'
                , 'ISO_3166-1_Numeric' => '8'
            )
            , array(
                  'Name'               => 'Algeria'
                , 'ISO_3166-1_Alpha_2' => 'DZ'
                , 'ISO_3166-1_Alpha_3' => 'DZA'
                , 'ISO_3166-1_Numeric' => '12'
            )
            , array(
                  'Name'               => 'American Samoa'
                , 'ISO_3166-1_Alpha_2' => 'AS'
                , 'ISO_3166-1_Alpha_3' => 'ASM'
                , 'ISO_3166-1_Numeric' => '16'
            )
            , array(
                  'Name'               => 'Andorra'
                , 'ISO_3166-1_Alpha_2' => 'AD'
                , 'ISO_3166-1_Alpha_3' => 'AND'
                , 'ISO_3166-1_Numeric' => '20'
            )
            , array(
                  'Name'               => 'Angola'
                , 'ISO_3166-1_Alpha_2' => 'AO'
                , 'ISO_3166-1_Alpha_3' => 'AGO'
                , 'ISO_3166-1_Numeric' => '24'
            )
            , array(
                  'Name'               => 'Anguilla'
                , 'ISO_3166-1_Alpha_2' => 'AI'
                , 'ISO_3166-1_Alpha_3' => 'AIA'
                , 'ISO_3166-1_Numeric' => '660'
            )
            , array(
                  'Name'               => 'Antigua and Barbuda'
                , 'ISO_3166-1_Alpha_2' => 'AG'
                , 'ISO_3166-1_Alpha_3' => 'ATG'
                , 'ISO_3166-1_Numeric' => '28'
            )
            , array(
                  'Name'               => 'Argentina'
                , 'ISO_3166-1_Alpha_2' => 'AR'
                , 'ISO_3166-1_Alpha_3' => 'ARG'
                , 'ISO_3166-1_Numeric' => '32'
            )
            , array(
                  'Name'               => 'Armenia'
                , 'ISO_3166-1_Alpha_2' => 'AM'
                , 'ISO_3166-1_Alpha_3' => 'ARM'
                , 'ISO_3166-1_Numeric' => '51'
            )
            , array(
                  'Name'               => 'Aruba'
                , 'ISO_3166-1_Alpha_2' => 'AW'
                , 'ISO_3166-1_Alpha_3' => 'ABW'
                , 'ISO_3166-1_Numeric' => '533'
            )
            , array(
                  'Name'               => 'Ascension'
                , 'ISO_3166-1_Alpha_2' => 'AC'
                , 'ISO_3166-1_Alpha_3' => 'ASC'
                , 'ISO_3166-1_Numeric' =>' '
            )
            , array(
                  'Name'               => 'Ashmore and Cartier Islands'
                , 'ISO_3166-1_Alpha_2' => 'AU'
                , 'ISO_3166-1_Alpha_3' => 'AUS'
                , 'ISO_3166-1_Numeric' => '36'
            )
            , array(
                  'Name'               => 'Australia'
                , 'ISO_3166-1_Alpha_2' => 'AU'
                , 'ISO_3166-1_Alpha_3' => 'AUS'
                , 'ISO_3166-1_Numeric' => '36'
            )
            , array(
                  'Name'               => 'Australian Antarctic Territory'
                , 'ISO_3166-1_Alpha_2' => 'AQ'
                , 'ISO_3166-1_Alpha_3' => 'ATA'
                , 'ISO_3166-1_Numeric' => '10'
            )
            , array(
                  'Name'               => 'Austria'
                , 'ISO_3166-1_Alpha_2' => 'AT'
                , 'ISO_3166-1_Alpha_3' => 'AUT'
                , 'ISO_3166-1_Numeric' => '40'
            )
            , array(
                  'Name'               => 'Azerbaijan'
                , 'ISO_3166-1_Alpha_2' => 'AZ'
                , 'ISO_3166-1_Alpha_3' => 'AZE'
                , 'ISO_3166-1_Numeric' => '31'
            )
            , array(
                  'Name'               => 'Bahamas, The'
                , 'ISO_3166-1_Alpha_2' => 'BS'
                , 'ISO_3166-1_Alpha_3' => 'BHS'
                , 'ISO_3166-1_Numeric' => '44'
            )
            , array(
                  'Name'               => 'Bahrain'
                , 'ISO_3166-1_Alpha_2' => 'BH'
                , 'ISO_3166-1_Alpha_3' => 'BHR'
                , 'ISO_3166-1_Numeric' => '48'
            )
            , array(
                  'Name'               => 'Baker Island'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Bangladesh'
                , 'ISO_3166-1_Alpha_2' => 'BD'
                , 'ISO_3166-1_Alpha_3' => 'BGD'
                , 'ISO_3166-1_Numeric' => '50'
            )
            , array(
                  'Name'               => 'Barbados'
                , 'ISO_3166-1_Alpha_2' => 'BB'
                , 'ISO_3166-1_Alpha_3' => 'BRB'
                , 'ISO_3166-1_Numeric' => '52'
            )
            , array(
                  'Name'               => 'Belarus'
                , 'ISO_3166-1_Alpha_2' => 'BY'
                , 'ISO_3166-1_Alpha_3' => 'BLR'
                , 'ISO_3166-1_Numeric' => '112'
            )
            , array(
                  'Name'               => 'Belgium'
                , 'ISO_3166-1_Alpha_2' => 'BE'
                , 'ISO_3166-1_Alpha_3' => 'BEL'
                , 'ISO_3166-1_Numeric' => '56'
            )
            , array(
                  'Name'               => 'Belize'
                , 'ISO_3166-1_Alpha_2' => 'BZ'
                , 'ISO_3166-1_Alpha_3' => 'BLZ'
                , 'ISO_3166-1_Numeric' => '84'
            )
            , array(
                  'Name'               => 'Benin'
                , 'ISO_3166-1_Alpha_2' => 'BJ'
                , 'ISO_3166-1_Alpha_3' => 'BEN'
                , 'ISO_3166-1_Numeric' => '204'
            )
            , array(
                  'Name'               => 'Bermuda'
                , 'ISO_3166-1_Alpha_2' => 'BM'
                , 'ISO_3166-1_Alpha_3' => 'BMU'
                , 'ISO_3166-1_Numeric' => '60'
            )
            , array(
                  'Name'               => 'Bhutan'
                , 'ISO_3166-1_Alpha_2' => 'BT'
                , 'ISO_3166-1_Alpha_3' => 'BTN'
                , 'ISO_3166-1_Numeric' => '64'
            )
            , array(
                  'Name'               => 'Bolivia'
                , 'ISO_3166-1_Alpha_2' => 'BO'
                , 'ISO_3166-1_Alpha_3' => 'BOL'
                , 'ISO_3166-1_Numeric' => '68'
            )
            , array(
                  'Name'               => 'Bosnia and Herzegovina'
                , 'ISO_3166-1_Alpha_2' => 'BA'
                , 'ISO_3166-1_Alpha_3' => 'BIH'
                , 'ISO_3166-1_Numeric' => '70'
            )
            , array(
                  'Name'               => 'Botswana'
                , 'ISO_3166-1_Alpha_2' => 'BW'
                , 'ISO_3166-1_Alpha_3' => 'BWA'
                , 'ISO_3166-1_Numeric' => '72'
            )
            , array(
                  'Name'               => 'Bouvet Island'
                , 'ISO_3166-1_Alpha_2' => 'BV'
                , 'ISO_3166-1_Alpha_3' => 'BVT'
                , 'ISO_3166-1_Numeric' => '74'
            )
            , array(
                  'Name'               => 'Brazil'
                , 'ISO_3166-1_Alpha_2' => 'BR'
                , 'ISO_3166-1_Alpha_3' => 'BRA'
                , 'ISO_3166-1_Numeric' => '76'
            )
            , array(
                  'Name'               => 'British Antarctic Territory'
                , 'ISO_3166-1_Alpha_2' => 'AQ'
                , 'ISO_3166-1_Alpha_3' => 'ATA'
                , 'ISO_3166-1_Numeric' => '10'
            )
            , array(
                  'Name'               => 'British Indian Ocean Territory'
                , 'ISO_3166-1_Alpha_2' => 'IO'
                , 'ISO_3166-1_Alpha_3' => 'IOT'
                , 'ISO_3166-1_Numeric' => '86'
            )
            , array(
                  'Name'               => 'British Virgin Islands'
                , 'ISO_3166-1_Alpha_2' => 'VG'
                , 'ISO_3166-1_Alpha_3' => 'VGB'
                , 'ISO_3166-1_Numeric' => '92'
            )
            , array(
                  'Name'               => 'Brunei'
                , 'ISO_3166-1_Alpha_2' => 'BN'
                , 'ISO_3166-1_Alpha_3' => 'BRN'
                , 'ISO_3166-1_Numeric' => '96'
            )
            , array(
                  'Name'               => 'Bulgaria'
                , 'ISO_3166-1_Alpha_2' => 'BG'
                , 'ISO_3166-1_Alpha_3' => 'BGR'
                , 'ISO_3166-1_Numeric' => '100'
            )
            , array(
                  'Name'               => 'Burkina Faso'
                , 'ISO_3166-1_Alpha_2' => 'BF'
                , 'ISO_3166-1_Alpha_3' => 'BFA'
                , 'ISO_3166-1_Numeric' => '854'
            )
            , array(
                  'Name'               => 'Burundi'
                , 'ISO_3166-1_Alpha_2' => 'BI'
                , 'ISO_3166-1_Alpha_3' => 'BDI'
                , 'ISO_3166-1_Numeric' => '108'
            )
            , array(
                  'Name'               => 'Cambodia'
                , 'ISO_3166-1_Alpha_2' => 'KH'
                , 'ISO_3166-1_Alpha_3' => 'KHM'
                , 'ISO_3166-1_Numeric' => '116'
            )
            , array(
                  'Name'               => 'Cameroon'
                , 'ISO_3166-1_Alpha_2' => 'CM'
                , 'ISO_3166-1_Alpha_3' => 'CMR'
                , 'ISO_3166-1_Numeric' => '120'
            )
            , array(
                  'Name'               => 'Canada'
                , 'ISO_3166-1_Alpha_2' => 'CA'
                , 'ISO_3166-1_Alpha_3' => 'CAN'
                , 'ISO_3166-1_Numeric' => '124'
            )
            , array(
                  'Name'               => 'Cape Verde'
                , 'ISO_3166-1_Alpha_2' => 'CV'
                , 'ISO_3166-1_Alpha_3' => 'CPV'
                , 'ISO_3166-1_Numeric' => '132'
            )
            , array(
                  'Name'               => 'Cayman Islands'
                , 'ISO_3166-1_Alpha_2' => 'KY'
                , 'ISO_3166-1_Alpha_3' => 'CYM'
                , 'ISO_3166-1_Numeric' => '136'
            )
            , array(
                  'Name'               => 'Central African Republic'
                , 'ISO_3166-1_Alpha_2' => 'CF'
                , 'ISO_3166-1_Alpha_3' => 'CAF'
                , 'ISO_3166-1_Numeric' => '140'
            )
            , array(
                  'Name'               => 'Chad'
                , 'ISO_3166-1_Alpha_2' => 'TD'
                , 'ISO_3166-1_Alpha_3' => 'TCD'
                , 'ISO_3166-1_Numeric' => '148'
            )
            , array(
                  'Name'               => 'Chile'
                , 'ISO_3166-1_Alpha_2' => 'CL'
                , 'ISO_3166-1_Alpha_3' => 'CHL'
                , 'ISO_3166-1_Numeric' => '152'
            )
            , array(
                  'Name'               => 'China, People\'s Republic of'
                , 'ISO_3166-1_Alpha_2' => 'CN'
                , 'ISO_3166-1_Alpha_3' => 'CHN'
                , 'ISO_3166-1_Numeric' => '156'
            )
            , array(
                  'Name'               => 'China, Republic of (Taiwan)'
                , 'ISO_3166-1_Alpha_2' => 'TW'
                , 'ISO_3166-1_Alpha_3' => 'TWN'
                , 'ISO_3166-1_Numeric' => '158'
            )
            , array(
                  'Name'               => 'Christmas Island'
                , 'ISO_3166-1_Alpha_2' => 'CX'
                , 'ISO_3166-1_Alpha_3' => 'CXR'
                , 'ISO_3166-1_Numeric' => '162'
            )
            , array(
                  'Name'               => 'Clipperton Island'
                , 'ISO_3166-1_Alpha_2' => 'PF'
                , 'ISO_3166-1_Alpha_3' => 'PYF'
                , 'ISO_3166-1_Numeric' => '258'
            )
            , array(
                  'Name'               => 'Cocos (Keeling) Islands'
                , 'ISO_3166-1_Alpha_2' => 'CC'
                , 'ISO_3166-1_Alpha_3' => 'CCK'
                , 'ISO_3166-1_Numeric' => '166'
            )
            , array(
                  'Name'               => 'Colombia'
                , 'ISO_3166-1_Alpha_2' => 'CO'
                , 'ISO_3166-1_Alpha_3' => 'COL'
                , 'ISO_3166-1_Numeric' => '170'
            )
            , array(
                  'Name'               => 'Comoros'
                , 'ISO_3166-1_Alpha_2' => 'KM'
                , 'ISO_3166-1_Alpha_3' => 'COM'
                , 'ISO_3166-1_Numeric' => '174'
            )
            , array(
                  'Name'               => 'Congo, (Congo  Brazzaville)'
                , 'ISO_3166-1_Alpha_2' => 'CG'
                , 'ISO_3166-1_Alpha_3' => 'COG'
                , 'ISO_3166-1_Numeric' => '178'
            )
            , array(
                  'Name'               => 'Congo, (Congo  Kinshasa)'
                , 'ISO_3166-1_Alpha_2' => 'CD'
                , 'ISO_3166-1_Alpha_3' => 'COD'
                , 'ISO_3166-1_Numeric' => '180'
            )
            , array(
                  'Name'               => 'Cook Islands'
                , 'ISO_3166-1_Alpha_2' => 'CK'
                , 'ISO_3166-1_Alpha_3' => 'COK'
                , 'ISO_3166-1_Numeric' => '184'
            )
            , array(
                  'Name'               => 'Coral Sea Islands'
                , 'ISO_3166-1_Alpha_2' => 'AU'
                , 'ISO_3166-1_Alpha_3' => 'AUS'
                , 'ISO_3166-1_Numeric' => '36'
            )
            , array(
                  'Name'               => 'Costa Rica'
                , 'ISO_3166-1_Alpha_2' => 'CR'
                , 'ISO_3166-1_Alpha_3' => 'CRI'
                , 'ISO_3166-1_Numeric' => '188'
            )
            , array(
                  'Name'               => 'Cote d\'Ivoire (Ivory Coast)'
                , 'ISO_3166-1_Alpha_2' => 'CI'
                , 'ISO_3166-1_Alpha_3' => 'CIV'
                , 'ISO_3166-1_Numeric' => '384'
            )
            , array(
                  'Name'               => 'Croatia'
                , 'ISO_3166-1_Alpha_2' => 'HR'
                , 'ISO_3166-1_Alpha_3' => 'HRV'
                , 'ISO_3166-1_Numeric' => '191'
            )
            , array(
                  'Name'               => 'Cuba'
                , 'ISO_3166-1_Alpha_2' => 'CU'
                , 'ISO_3166-1_Alpha_3' => 'CUB'
                , 'ISO_3166-1_Numeric' => '192'
            )
            , array(
                  'Name'               => 'Cyprus'
                , 'ISO_3166-1_Alpha_2' => 'CY'
                , 'ISO_3166-1_Alpha_3' => 'CYP'
                , 'ISO_3166-1_Numeric' => '196'
            )
            , array(
                  'Name'               => 'Czech Republic'
                , 'ISO_3166-1_Alpha_2' => 'CZ'
                , 'ISO_3166-1_Alpha_3' => 'CZE'
                , 'ISO_3166-1_Numeric' => '203'
            )
            , array(
                  'Name'               => 'Denmark'
                , 'ISO_3166-1_Alpha_2' => 'DK'
                , 'ISO_3166-1_Alpha_3' => 'DNK'
                , 'ISO_3166-1_Numeric' => '208'
            )
            , array(
                  'Name'               => 'Djibouti'
                , 'ISO_3166-1_Alpha_2' => 'DJ'
                , 'ISO_3166-1_Alpha_3' => 'DJI'
                , 'ISO_3166-1_Numeric' => '262'
            )
            , array(
                  'Name'               => 'Dominica'
                , 'ISO_3166-1_Alpha_2' => 'DM'
                , 'ISO_3166-1_Alpha_3' => 'DMA'
                , 'ISO_3166-1_Numeric' => '212'
            )
            , array(
                  'Name'               => 'Dominican Republic'
                , 'ISO_3166-1_Alpha_2' => 'DO'
                , 'ISO_3166-1_Alpha_3' => 'DOM'
                , 'ISO_3166-1_Numeric' => '214'
            )
            , array(
                  'Name'               => 'Ecuador'
                , 'ISO_3166-1_Alpha_2' => 'EC'
                , 'ISO_3166-1_Alpha_3' => 'ECU'
                , 'ISO_3166-1_Numeric' => '218'
            )
            , array(
                  'Name'               => 'Egypt'
                , 'ISO_3166-1_Alpha_2' => 'EG'
                , 'ISO_3166-1_Alpha_3' => 'EGY'
                , 'ISO_3166-1_Numeric' => '818'
            )
            , array(
                  'Name'               => 'El Salvador'
                , 'ISO_3166-1_Alpha_2' => 'SV'
                , 'ISO_3166-1_Alpha_3' => 'SLV'
                , 'ISO_3166-1_Numeric' => '222'
            )
            , array(
                  'Name'               => 'Equatorial Guinea'
                , 'ISO_3166-1_Alpha_2' => 'GQ'
                , 'ISO_3166-1_Alpha_3' => 'GNQ'
                , 'ISO_3166-1_Numeric' => '226'
            )
            , array(
                  'Name'               => 'Eritrea'
                , 'ISO_3166-1_Alpha_2' => 'ER'
                , 'ISO_3166-1_Alpha_3' => 'ERI'
                , 'ISO_3166-1_Numeric' => '232'
            )
            , array(
                  'Name'               => 'Estonia'
                , 'ISO_3166-1_Alpha_2' => 'EE'
                , 'ISO_3166-1_Alpha_3' => 'EST'
                , 'ISO_3166-1_Numeric' => '233'
            )
            , array(
                  'Name'               => 'Ethiopia'
                , 'ISO_3166-1_Alpha_2' => 'ET'
                , 'ISO_3166-1_Alpha_3' => 'ETH'
                , 'ISO_3166-1_Numeric' => '231'
            )
            , array(
                  'Name'               => 'Falkland Islands (Islas Malvinas)'
                , 'ISO_3166-1_Alpha_2' => 'FK'
                , 'ISO_3166-1_Alpha_3' => 'FLK'
                , 'ISO_3166-1_Numeric' => '238'
            )
            , array(
                  'Name'               => 'Faroe Islands'
                , 'ISO_3166-1_Alpha_2' => 'FO'
                , 'ISO_3166-1_Alpha_3' => 'FRO'
                , 'ISO_3166-1_Numeric' => '234'
            )
            , array(
                  'Name'               => 'Fiji'
                , 'ISO_3166-1_Alpha_2' => 'FJ'
                , 'ISO_3166-1_Alpha_3' => 'FJI'
                , 'ISO_3166-1_Numeric' => '242'
            )
            , array(
                  'Name'               => 'Finland'
                , 'ISO_3166-1_Alpha_2' => 'FI'
                , 'ISO_3166-1_Alpha_3' => 'FIN'
                , 'ISO_3166-1_Numeric' => '246'
            )
            , array(
                  'Name'               => 'France'
                , 'ISO_3166-1_Alpha_2' => 'FR'
                , 'ISO_3166-1_Alpha_3' => 'FRA'
                , 'ISO_3166-1_Numeric' => '250'
            )
            , array(
                  'Name'               => 'French Guiana'
                , 'ISO_3166-1_Alpha_2' => 'GF'
                , 'ISO_3166-1_Alpha_3' => 'GUF'
                , 'ISO_3166-1_Numeric' => '254'
            )
            , array(
                  'Name'               => 'French Polynesia'
                , 'ISO_3166-1_Alpha_2' => 'PF'
                , 'ISO_3166-1_Alpha_3' => 'PYF'
                , 'ISO_3166-1_Numeric' => '258'
            )
            , array(
                  'Name'               => 'French Southern and Antarctic Lands'
                , 'ISO_3166-1_Alpha_2' => 'TF'
                , 'ISO_3166-1_Alpha_3' => 'ATF'
                , 'ISO_3166-1_Numeric' => '260'
            )
            , array(
                  'Name'               => 'Gabon'
                , 'ISO_3166-1_Alpha_2' => 'GA'
                , 'ISO_3166-1_Alpha_3' => 'GAB'
                , 'ISO_3166-1_Numeric' => '266'
            )
            , array(
                  'Name'               => 'Gambia, The'
                , 'ISO_3166-1_Alpha_2' => 'GM'
                , 'ISO_3166-1_Alpha_3' => 'GMB'
                , 'ISO_3166-1_Numeric' => '270'
            )
            , array(
                  'Name'               => 'Georgia'
                , 'ISO_3166-1_Alpha_2' => 'GE'
                , 'ISO_3166-1_Alpha_3' => 'GEO'
                , 'ISO_3166-1_Numeric' => '268'
            )
            , array(
                  'Name'               => 'Germany'
                , 'ISO_3166-1_Alpha_2' => 'DE'
                , 'ISO_3166-1_Alpha_3' => 'DEU'
                , 'ISO_3166-1_Numeric' => '276'
            )
            , array(
                  'Name'               => 'Ghana'
                , 'ISO_3166-1_Alpha_2' => 'GH'
                , 'ISO_3166-1_Alpha_3' => 'GHA'
                , 'ISO_3166-1_Numeric' => '288'
            )
            , array(
                  'Name'               => 'Gibraltar'
                , 'ISO_3166-1_Alpha_2' => 'GI'
                , 'ISO_3166-1_Alpha_3' => 'GIB'
                , 'ISO_3166-1_Numeric' => '292'
            )
            , array(
                  'Name'               => 'Greece'
                , 'ISO_3166-1_Alpha_2' => 'GR'
                , 'ISO_3166-1_Alpha_3' => 'GRC'
                , 'ISO_3166-1_Numeric' => '300'
            )
            , array(
                  'Name'               => 'Greenland'
                , 'ISO_3166-1_Alpha_2' => 'GL'
                , 'ISO_3166-1_Alpha_3' => 'GRL'
                , 'ISO_3166-1_Numeric' => '304'
            )
            , array(
                  'Name'               => 'Grenada'
                , 'ISO_3166-1_Alpha_2' => 'GD'
                , 'ISO_3166-1_Alpha_3' => 'GRD'
                , 'ISO_3166-1_Numeric' => '308'
            )
            , array(
                  'Name'               => 'Guadeloupe'
                , 'ISO_3166-1_Alpha_2' => 'GP'
                , 'ISO_3166-1_Alpha_3' => 'GLP'
                , 'ISO_3166-1_Numeric' => '312'
            )
            , array(
                  'Name'               => 'Guam'
                , 'ISO_3166-1_Alpha_2' => 'GU'
                , 'ISO_3166-1_Alpha_3' => 'GUM'
                , 'ISO_3166-1_Numeric' => '316'
            )
            , array(
                  'Name'               => 'Guatemala'
                , 'ISO_3166-1_Alpha_2' => 'GT'
                , 'ISO_3166-1_Alpha_3' => 'GTM'
                , 'ISO_3166-1_Numeric' => '320'
            )
            , array(
                  'Name'               => 'Guernsey'
                , 'ISO_3166-1_Alpha_2' => 'GG'
                , 'ISO_3166-1_Alpha_3' => 'GGY'
                , 'ISO_3166-1_Numeric' => '831'
            )
            , array(
                  'Name'               => 'Guinea'
                , 'ISO_3166-1_Alpha_2' => 'GN'
                , 'ISO_3166-1_Alpha_3' => 'GIN'
                , 'ISO_3166-1_Numeric' => '324'
            )
            , array(
                  'Name'               => 'Guinea-Bissau'
                , 'ISO_3166-1_Alpha_2' => 'GW'
                , 'ISO_3166-1_Alpha_3' => 'GNB'
                , 'ISO_3166-1_Numeric' => '624'
            )
            , array(
                  'Name'               => 'Guyana'
                , 'ISO_3166-1_Alpha_2' => 'GY'
                , 'ISO_3166-1_Alpha_3' => 'GUY'
                , 'ISO_3166-1_Numeric' => '328'
            )
            , array(
                  'Name'               => 'Haiti'
                , 'ISO_3166-1_Alpha_2' => 'HT'
                , 'ISO_3166-1_Alpha_3' => 'HTI'
                , 'ISO_3166-1_Numeric' => '332'
            )
            , array(
                  'Name'               => 'Heard Island and McDonald Islands'
                , 'ISO_3166-1_Alpha_2' => 'HM'
                , 'ISO_3166-1_Alpha_3' => 'HMD'
                , 'ISO_3166-1_Numeric' => '334'
            )
            , array(
                  'Name'               => 'Honduras'
                , 'ISO_3166-1_Alpha_2' => 'HN'
                , 'ISO_3166-1_Alpha_3' => 'HND'
                , 'ISO_3166-1_Numeric' => '340'
            )
            , array(
                  'Name'               => 'Hong Kong'
                , 'ISO_3166-1_Alpha_2' => 'HK'
                , 'ISO_3166-1_Alpha_3' => 'HKG'
                , 'ISO_3166-1_Numeric' => '344'
            )
            , array(
                  'Name'               => 'Howland Island'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Hungary'
                , 'ISO_3166-1_Alpha_2' => 'HU'
                , 'ISO_3166-1_Alpha_3' => 'HUN'
                , 'ISO_3166-1_Numeric' => '348'
            )
            , array(
                  'Name'               => 'Iceland'
                , 'ISO_3166-1_Alpha_2' => 'IS'
                , 'ISO_3166-1_Alpha_3' => 'ISL'
                , 'ISO_3166-1_Numeric' => '352'
            )
            , array(
                  'Name'               => 'India'
                , 'ISO_3166-1_Alpha_2' => 'IN'
                , 'ISO_3166-1_Alpha_3' => 'IND'
                , 'ISO_3166-1_Numeric' => '356'
            )
            , array(
                  'Name'               => 'Indonesia'
                , 'ISO_3166-1_Alpha_2' => 'ID'
                , 'ISO_3166-1_Alpha_3' => 'IDN'
                , 'ISO_3166-1_Numeric' => '360'
            )
            , array(
                  'Name'               => 'Iran'
                , 'ISO_3166-1_Alpha_2' => 'IR'
                , 'ISO_3166-1_Alpha_3' => 'IRN'
                , 'ISO_3166-1_Numeric' => '364'
            )
            , array(
                  'Name'               => 'Iraq'
                , 'ISO_3166-1_Alpha_2' => 'IQ'
                , 'ISO_3166-1_Alpha_3' => 'IRQ'
                , 'ISO_3166-1_Numeric' => '368'
            )
            , array(
                  'Name'               => 'Ireland'
                , 'ISO_3166-1_Alpha_2' => 'IE'
                , 'ISO_3166-1_Alpha_3' => 'IRL'
                , 'ISO_3166-1_Numeric' => '372'
            )
            , array(
                  'Name'               => 'Isle of Man'
                , 'ISO_3166-1_Alpha_2' => 'IM'
                , 'ISO_3166-1_Alpha_3' => 'IMN'
                , 'ISO_3166-1_Numeric' => '833'
            )
            , array(
                  'Name'               => 'Israel'
                , 'ISO_3166-1_Alpha_2' => 'IL'
                , 'ISO_3166-1_Alpha_3' => 'ISR'
                , 'ISO_3166-1_Numeric' => '376'
            )
            , array(
                  'Name'               => 'Italy'
                , 'ISO_3166-1_Alpha_2' => 'IT'
                , 'ISO_3166-1_Alpha_3' => 'ITA'
                , 'ISO_3166-1_Numeric' => '380'
            )
            , array(
                  'Name'               => 'Jamaica'
                , 'ISO_3166-1_Alpha_2' => 'JM'
                , 'ISO_3166-1_Alpha_3' => 'JAM'
                , 'ISO_3166-1_Numeric' => '388'
            )
            , array(
                  'Name'               => 'Japan'
                , 'ISO_3166-1_Alpha_2' => 'JP'
                , 'ISO_3166-1_Alpha_3' => 'JPN'
                , 'ISO_3166-1_Numeric' => '392'
            )
            , array(
                  'Name'               => 'Jarvis Island'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Jersey'
                , 'ISO_3166-1_Alpha_2' => 'JE'
                , 'ISO_3166-1_Alpha_3' => 'JEY'
                , 'ISO_3166-1_Numeric' => '832'
            )
            , array(
                  'Name'               => 'Johnston Atoll'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Jordan'
                , 'ISO_3166-1_Alpha_2' => 'JO'
                , 'ISO_3166-1_Alpha_3' => 'JOR'
                , 'ISO_3166-1_Numeric' => '400'
            )
            , array(
                  'Name'               => 'Kazakhstan'
                , 'ISO_3166-1_Alpha_2' => 'KZ'
                , 'ISO_3166-1_Alpha_3' => 'KAZ'
                , 'ISO_3166-1_Numeric' => '398'
            )
            , array(
                  'Name'               => 'Kenya'
                , 'ISO_3166-1_Alpha_2' => 'KE'
                , 'ISO_3166-1_Alpha_3' => 'KEN'
                , 'ISO_3166-1_Numeric' => '404'
            )
            , array(
                  'Name'               => 'Kingman Reef'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Kiribati'
                , 'ISO_3166-1_Alpha_2' => 'KI'
                , 'ISO_3166-1_Alpha_3' => 'KIR'
                , 'ISO_3166-1_Numeric' => '296'
            )
            , array(
                  'Name'               => 'Korea, North'
                , 'ISO_3166-1_Alpha_2' => 'KP'
                , 'ISO_3166-1_Alpha_3' => 'PRK'
                , 'ISO_3166-1_Numeric' => '408'
            )
            , array(
                  'Name'               => 'Korea, South'
                , 'ISO_3166-1_Alpha_2' => 'KR'
                , 'ISO_3166-1_Alpha_3' => 'KOR'
                , 'ISO_3166-1_Numeric' => '410'
            )
            , array(
                  'Name'               => 'Kuwait'
                , 'ISO_3166-1_Alpha_2' => 'KW'
                , 'ISO_3166-1_Alpha_3' => 'KWT'
                , 'ISO_3166-1_Numeric' => '414'
            )
            , array(
                  'Name'               => 'Kyrgyzstan'
                , 'ISO_3166-1_Alpha_2' => 'KG'
                , 'ISO_3166-1_Alpha_3' => 'KGZ'
                , 'ISO_3166-1_Numeric' => '417'
            )
            , array(
                  'Name'               => 'Laos'
                , 'ISO_3166-1_Alpha_2' => 'LA'
                , 'ISO_3166-1_Alpha_3' => 'LAO'
                , 'ISO_3166-1_Numeric' => '418'
            )
            , array(
                  'Name'               => 'Latvia'
                , 'ISO_3166-1_Alpha_2' => 'LV'
                , 'ISO_3166-1_Alpha_3' => 'LVA'
                , 'ISO_3166-1_Numeric' => '428'
            )
            , array(
                  'Name'               => 'Lebanon'
                , 'ISO_3166-1_Alpha_2' => 'LB'
                , 'ISO_3166-1_Alpha_3' => 'LBN'
                , 'ISO_3166-1_Numeric' => '422'
            )
            , array(
                  'Name'               => 'Lesotho'
                , 'ISO_3166-1_Alpha_2' => 'LS'
                , 'ISO_3166-1_Alpha_3' => 'LSO'
                , 'ISO_3166-1_Numeric' => '426'
            )
            , array(
                  'Name'               => 'Liberia'
                , 'ISO_3166-1_Alpha_2' => 'LR'
                , 'ISO_3166-1_Alpha_3' => 'LBR'
                , 'ISO_3166-1_Numeric' => '430'
            )
            , array(
                  'Name'               => 'Libya'
                , 'ISO_3166-1_Alpha_2' => 'LY'
                , 'ISO_3166-1_Alpha_3' => 'LBY'
                , 'ISO_3166-1_Numeric' => '434'
            )
            , array(
                  'Name'               => 'Liechtenstein'
                , 'ISO_3166-1_Alpha_2' => 'LI'
                , 'ISO_3166-1_Alpha_3' => 'LIE'
                , 'ISO_3166-1_Numeric' => '438'
            )
            , array(
                  'Name'               => 'Lithuania'
                , 'ISO_3166-1_Alpha_2' => 'LT'
                , 'ISO_3166-1_Alpha_3' => 'LTU'
                , 'ISO_3166-1_Numeric' => '440'
            )
            , array(
                  'Name'               => 'Luxembourg'
                , 'ISO_3166-1_Alpha_2' => 'LU'
                , 'ISO_3166-1_Alpha_3' => 'LUX'
                , 'ISO_3166-1_Numeric' => '442'
            )
            , array(
                  'Name'               => 'Macau'
                , 'ISO_3166-1_Alpha_2' => 'MO'
                , 'ISO_3166-1_Alpha_3' => 'MAC'
                , 'ISO_3166-1_Numeric' => '446'
            )
            , array(
                  'Name'               => 'Macedonia'
                , 'ISO_3166-1_Alpha_2' => 'MK'
                , 'ISO_3166-1_Alpha_3' => 'MKD'
                , 'ISO_3166-1_Numeric' => '807'
            )
            , array(
                  'Name'               => 'Madagascar'
                , 'ISO_3166-1_Alpha_2' => 'MG'
                , 'ISO_3166-1_Alpha_3' => 'MDG'
                , 'ISO_3166-1_Numeric' => '450'
            )
            , array(
                  'Name'               => 'Malawi'
                , 'ISO_3166-1_Alpha_2' => 'MW'
                , 'ISO_3166-1_Alpha_3' => 'MWI'
                , 'ISO_3166-1_Numeric' => '454'
            )
            , array(
                  'Name'               => 'Malaysia'
                , 'ISO_3166-1_Alpha_2' => 'MY'
                , 'ISO_3166-1_Alpha_3' => 'MYS'
                , 'ISO_3166-1_Numeric' => '458'
            )
            , array(
                  'Name'               => 'Maldives'
                , 'ISO_3166-1_Alpha_2' => 'MV'
                , 'ISO_3166-1_Alpha_3' => 'MDV'
                , 'ISO_3166-1_Numeric' => '462'
            )
            , array(
                  'Name'               => 'Mali'
                , 'ISO_3166-1_Alpha_2' => 'ML'
                , 'ISO_3166-1_Alpha_3' => 'MLI'
                , 'ISO_3166-1_Numeric' => '466'
            )
            , array(
                  'Name'               => 'Malta'
                , 'ISO_3166-1_Alpha_2' => 'MT'
                , 'ISO_3166-1_Alpha_3' => 'MLT'
                , 'ISO_3166-1_Numeric' => '470'
            )
            , array(
                  'Name'               => 'Marshall Islands'
                , 'ISO_3166-1_Alpha_2' => 'MH'
                , 'ISO_3166-1_Alpha_3' => 'MHL'
                , 'ISO_3166-1_Numeric' => '584'
            )
            , array(
                  'Name'               => 'Martinique'
                , 'ISO_3166-1_Alpha_2' => 'MQ'
                , 'ISO_3166-1_Alpha_3' => 'MTQ'
                , 'ISO_3166-1_Numeric' => '474'
            )
            , array(
                  'Name'               => 'Mauritania'
                , 'ISO_3166-1_Alpha_2' => 'MR'
                , 'ISO_3166-1_Alpha_3' => 'MRT'
                , 'ISO_3166-1_Numeric' => '478'
            )
            , array(
                  'Name'               => 'Mauritius'
                , 'ISO_3166-1_Alpha_2' => 'MU'
                , 'ISO_3166-1_Alpha_3' => 'MUS'
                , 'ISO_3166-1_Numeric' => '480'
            )
            , array(
                  'Name'               => 'Mayotte'
                , 'ISO_3166-1_Alpha_2' => 'YT'
                , 'ISO_3166-1_Alpha_3' => 'MYT'
                , 'ISO_3166-1_Numeric' => '175'
            )
            , array(
                  'Name'               => 'Mexico'
                , 'ISO_3166-1_Alpha_2' => 'MX'
                , 'ISO_3166-1_Alpha_3' => 'MEX'
                , 'ISO_3166-1_Numeric' => '484'
            )
            , array(
                  'Name'               => 'Micronesia'
                , 'ISO_3166-1_Alpha_2' => 'FM'
                , 'ISO_3166-1_Alpha_3' => 'FSM'
                , 'ISO_3166-1_Numeric' => '583'
            )
            , array(
                  'Name'               => 'Midway Islands'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Moldova'
                , 'ISO_3166-1_Alpha_2' => 'MD'
                , 'ISO_3166-1_Alpha_3' => 'MDA'
                , 'ISO_3166-1_Numeric' => '498'
            )
            , array(
                  'Name'               => 'Monaco'
                , 'ISO_3166-1_Alpha_2' => 'MC'
                , 'ISO_3166-1_Alpha_3' => 'MCO'
                , 'ISO_3166-1_Numeric' => '492'
            )
            , array(
                  'Name'               => 'Mongolia'
                , 'ISO_3166-1_Alpha_2' => 'MN'
                , 'ISO_3166-1_Alpha_3' => 'MNG'
                , 'ISO_3166-1_Numeric' => '496'
            )
            , array(
                  'Name'               => 'Montenegro'
                , 'ISO_3166-1_Alpha_2' => 'ME'
                , 'ISO_3166-1_Alpha_3' => 'MNE'
                , 'ISO_3166-1_Numeric' => '499'
            )
            , array(
                  'Name'               => 'Montserrat'
                , 'ISO_3166-1_Alpha_2' => 'MS'
                , 'ISO_3166-1_Alpha_3' => 'MSR'
                , 'ISO_3166-1_Numeric' => '500'
            )
            , array(
                  'Name'               => 'Morocco'
                , 'ISO_3166-1_Alpha_2' => 'MA'
                , 'ISO_3166-1_Alpha_3' => 'MAR'
                , 'ISO_3166-1_Numeric' => '504'
            )
            , array(
                  'Name'               => 'Mozambique'
                , 'ISO_3166-1_Alpha_2' => 'MZ'
                , 'ISO_3166-1_Alpha_3' => 'MOZ'
                , 'ISO_3166-1_Numeric' => '508'
            )
            , array(
                  'Name'               => 'Myanmar (Burma)'
                , 'ISO_3166-1_Alpha_2' => 'MM'
                , 'ISO_3166-1_Alpha_3' => 'MMR'
                , 'ISO_3166-1_Numeric' => '104'
            )
            , array(
                  'Name'               => 'Nagorno-Karabakh'
                , 'ISO_3166-1_Alpha_2' => 'AZ'
                , 'ISO_3166-1_Alpha_3' => 'AZE'
                , 'ISO_3166-1_Numeric' => '31'
            )
            , array(
                  'Name'               => 'Namibia'
                , 'ISO_3166-1_Alpha_2' => 'NA'
                , 'ISO_3166-1_Alpha_3' => 'NAM'
                , 'ISO_3166-1_Numeric' => '516'
            )
            , array(
                  'Name'               => 'Nauru'
                , 'ISO_3166-1_Alpha_2' => 'NR'
                , 'ISO_3166-1_Alpha_3' => 'NRU'
                , 'ISO_3166-1_Numeric' => '520'
            )
            , array(
                  'Name'               => 'Navassa Island'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Nepal'
                , 'ISO_3166-1_Alpha_2' => 'NP'
                , 'ISO_3166-1_Alpha_3' => 'NPL'
                , 'ISO_3166-1_Numeric' => '524'
            )
            , array(
                  'Name'               => 'Netherlands'
                , 'ISO_3166-1_Alpha_2' => 'NL'
                , 'ISO_3166-1_Alpha_3' => 'NLD'
                , 'ISO_3166-1_Numeric' => '528'
            )
            , array(
                  'Name'               => 'Netherlands Antilles'
                , 'ISO_3166-1_Alpha_2' => 'AN'
                , 'ISO_3166-1_Alpha_3' => 'ANT'
                , 'ISO_3166-1_Numeric' => '530'
            )
            , array(
                  'Name'               => 'New Caledonia'
                , 'ISO_3166-1_Alpha_2' => 'NC'
                , 'ISO_3166-1_Alpha_3' => 'NCL'
                , 'ISO_3166-1_Numeric' => '540'
            )
            , array(
                  'Name'               => 'New Zealand'
                , 'ISO_3166-1_Alpha_2' => 'NZ'
                , 'ISO_3166-1_Alpha_3' => 'NZL'
                , 'ISO_3166-1_Numeric' => '554'
            )
            , array(
                  'Name'               => 'Nicaragua'
                , 'ISO_3166-1_Alpha_2' => 'NI'
                , 'ISO_3166-1_Alpha_3' => 'NIC'
                , 'ISO_3166-1_Numeric' => '558'
            )
            , array(
                  'Name'               => 'Niger'
                , 'ISO_3166-1_Alpha_2' => 'NE'
                , 'ISO_3166-1_Alpha_3' => 'NER'
                , 'ISO_3166-1_Numeric' => '562'
            )
            , array(
                  'Name'               => 'Nigeria'
                , 'ISO_3166-1_Alpha_2' => 'NG'
                , 'ISO_3166-1_Alpha_3' => 'NGA'
                , 'ISO_3166-1_Numeric' => '566'
            )
            , array(
                  'Name'               => 'Niue'
                , 'ISO_3166-1_Alpha_2' => 'NU'
                , 'ISO_3166-1_Alpha_3' => 'NIU'
                , 'ISO_3166-1_Numeric' => '570'
            )
            , array(
                  'Name'               => 'Norfolk Island'
                , 'ISO_3166-1_Alpha_2' => 'NF'
                , 'ISO_3166-1_Alpha_3' => 'NFK'
                , 'ISO_3166-1_Numeric' => '574'
            )
            , array(
                  'Name'               => 'Northern Cyprus'
                , 'ISO_3166-1_Alpha_2' => 'CY'
                , 'ISO_3166-1_Alpha_3' => 'CYP'
                , 'ISO_3166-1_Numeric' => '196'
            )
            , array(
                  'Name'               => 'Northern Mariana Islands'
                , 'ISO_3166-1_Alpha_2' => 'MP'
                , 'ISO_3166-1_Alpha_3' => 'MNP'
                , 'ISO_3166-1_Numeric' => '580'
            )
            , array(
                  'Name'               => 'Norway'
                , 'ISO_3166-1_Alpha_2' => 'NO'
                , 'ISO_3166-1_Alpha_3' => 'NOR'
                , 'ISO_3166-1_Numeric' => '578'
            )
            , array(
                  'Name'               => 'Oman'
                , 'ISO_3166-1_Alpha_2' => 'OM'
                , 'ISO_3166-1_Alpha_3' => 'OMN'
                , 'ISO_3166-1_Numeric' => '512'
            )
            , array(
                  'Name'               => 'Pakistan'
                , 'ISO_3166-1_Alpha_2' => 'PK'
                , 'ISO_3166-1_Alpha_3' => 'PAK'
                , 'ISO_3166-1_Numeric' => '586'
            )
            , array(
                  'Name'               => 'Palau'
                , 'ISO_3166-1_Alpha_2' => 'PW'
                , 'ISO_3166-1_Alpha_3' => 'PLW'
                , 'ISO_3166-1_Numeric' => '585'
            )
            , array(
                  'Name'               => 'Palmyra Atoll'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '581'
            )
            , array(
                  'Name'               => 'Panama'
                , 'ISO_3166-1_Alpha_2' => 'PA'
                , 'ISO_3166-1_Alpha_3' => 'PAN'
                , 'ISO_3166-1_Numeric' => '591'
            )
            , array(
                  'Name'               => 'Papua New Guinea'
                , 'ISO_3166-1_Alpha_2' => 'PG'
                , 'ISO_3166-1_Alpha_3' => 'PNG'
                , 'ISO_3166-1_Numeric' => '598'
            )
            , array(
                  'Name'               => 'Paraguay'
                , 'ISO_3166-1_Alpha_2' => 'PY'
                , 'ISO_3166-1_Alpha_3' => 'PRY'
                , 'ISO_3166-1_Numeric' => '600'
            )
            , array(
                  'Name'               => 'Peru'
                , 'ISO_3166-1_Alpha_2' => 'PE'
                , 'ISO_3166-1_Alpha_3' => 'PER'
                , 'ISO_3166-1_Numeric' => '604'
            )
            , array(
                  'Name'               => 'Peter I Island'
                , 'ISO_3166-1_Alpha_2' => 'AQ'
                , 'ISO_3166-1_Alpha_3' => 'ATA'
                , 'ISO_3166-1_Numeric' => '10'
            )
            , array(
                  'Name'               => 'Philippines'
                , 'ISO_3166-1_Alpha_2' => 'PH'
                , 'ISO_3166-1_Alpha_3' => 'PHL'
                , 'ISO_3166-1_Numeric' => '608'
            )
            , array(
                  'Name'               => 'Pitcairn Islands'
                , 'ISO_3166-1_Alpha_2' => 'PN'
                , 'ISO_3166-1_Alpha_3' => 'PCN'
                , 'ISO_3166-1_Numeric' => '612'
            )
            , array(
                  'Name'               => 'Poland'
                , 'ISO_3166-1_Alpha_2' => 'PL'
                , 'ISO_3166-1_Alpha_3' => 'POL'
                , 'ISO_3166-1_Numeric' => '616'
            )
            , array(
                  'Name'               => 'Portugal'
                , 'ISO_3166-1_Alpha_2' => 'PT'
                , 'ISO_3166-1_Alpha_3' => 'PRT'
                , 'ISO_3166-1_Numeric' => '620'
            )
            , array(
                  'Name'               => 'Pridnestrovie (Transnistria)'
                , 'ISO_3166-1_Alpha_2' => 'MD'
                , 'ISO_3166-1_Alpha_3' => 'MDA'
                , 'ISO_3166-1_Numeric' => '498'
            )
            , array(
                  'Name'               => 'Puerto Rico'
                , 'ISO_3166-1_Alpha_2' => 'PR'
                , 'ISO_3166-1_Alpha_3' => 'PRI'
                , 'ISO_3166-1_Numeric' => '630'
            )
            , array(
                  'Name'               => 'Qatar'
                , 'ISO_3166-1_Alpha_2' => 'QA'
                , 'ISO_3166-1_Alpha_3' => 'QAT'
                , 'ISO_3166-1_Numeric' => '634'
            )
            , array(
                  'Name'               => 'Queen Maud Land'
                , 'ISO_3166-1_Alpha_2' => 'AQ'
                , 'ISO_3166-1_Alpha_3' => 'ATA'
                , 'ISO_3166-1_Numeric' => '10'
            )
            , array(
                  'Name'               => 'Reunion'
                , 'ISO_3166-1_Alpha_2' => 'RE'
                , 'ISO_3166-1_Alpha_3' => 'REU'
                , 'ISO_3166-1_Numeric' => '638'
            )
            , array(
                  'Name'               => 'Romania'
                , 'ISO_3166-1_Alpha_2' => 'RO'
                , 'ISO_3166-1_Alpha_3' => 'ROU'
                , 'ISO_3166-1_Numeric' => '642'
            )
            , array(
                  'Name'               => 'Ross Dependency'
                , 'ISO_3166-1_Alpha_2' => 'AQ'
                , 'ISO_3166-1_Alpha_3' => 'ATA'
                , 'ISO_3166-1_Numeric' => '10'
            )
            , array(
                  'Name'               => 'Russia'
                , 'ISO_3166-1_Alpha_2' => 'RU'
                , 'ISO_3166-1_Alpha_3' => 'RUS'
                , 'ISO_3166-1_Numeric' => '643'
            )
            , array(
                  'Name'               => 'Rwanda'
                , 'ISO_3166-1_Alpha_2' => 'RW'
                , 'ISO_3166-1_Alpha_3' => 'RWA'
                , 'ISO_3166-1_Numeric' => '646'
            )
            , array(
                  'Name'               => 'Saint Barthelemy'
                , 'ISO_3166-1_Alpha_2' => 'GP'
                , 'ISO_3166-1_Alpha_3' => 'GLP'
                , 'ISO_3166-1_Numeric' => '312'
            )
            , array(
                  'Name'               => 'Saint Helena'
                , 'ISO_3166-1_Alpha_2' => 'SH'
                , 'ISO_3166-1_Alpha_3' => 'SHN'
                , 'ISO_3166-1_Numeric' => '654'
            )
            , array(
                  'Name'               => 'Saint Kitts and Nevis'
                , 'ISO_3166-1_Alpha_2' => 'KN'
                , 'ISO_3166-1_Alpha_3' => 'KNA'
                , 'ISO_3166-1_Numeric' => '659'
            )
            , array(
                  'Name'               => 'Saint Lucia'
                , 'ISO_3166-1_Alpha_2' => 'LC'
                , 'ISO_3166-1_Alpha_3' => 'LCA'
                , 'ISO_3166-1_Numeric' => '662'
            )
            , array(
                  'Name'               => 'Saint Martin'
                , 'ISO_3166-1_Alpha_2' => 'GP'
                , 'ISO_3166-1_Alpha_3' => 'GLP'
                , 'ISO_3166-1_Numeric' => '312'
            )
            , array(
                  'Name'               => 'Saint Pierre and Miquelon'
                , 'ISO_3166-1_Alpha_2' => 'PM'
                , 'ISO_3166-1_Alpha_3' => 'SPM'
                , 'ISO_3166-1_Numeric' => '666'
            )
            , array(
                  'Name'               => 'Saint Vincent and the Grenadines'
                , 'ISO_3166-1_Alpha_2' => 'VC'
                , 'ISO_3166-1_Alpha_3' => 'VCT'
                , 'ISO_3166-1_Numeric' => '670'
            )
            , array(
                  'Name'               => 'Samoa'
                , 'ISO_3166-1_Alpha_2' => 'WS'
                , 'ISO_3166-1_Alpha_3' => 'WSM'
                , 'ISO_3166-1_Numeric' => '882'
            )
            , array(
                  'Name'               => 'San Marino'
                , 'ISO_3166-1_Alpha_2' => 'SM'
                , 'ISO_3166-1_Alpha_3' => 'SMR'
                , 'ISO_3166-1_Numeric' => '674'
            )
            , array(
                  'Name'               => 'Sao Tome and Principe'
                , 'ISO_3166-1_Alpha_2' => 'ST'
                , 'ISO_3166-1_Alpha_3' => 'STP'
                , 'ISO_3166-1_Numeric' => '678'
            )
            , array(
                  'Name'               => 'Saudi Arabia'
                , 'ISO_3166-1_Alpha_2' => 'SA'
                , 'ISO_3166-1_Alpha_3' => 'SAU'
                , 'ISO_3166-1_Numeric' => '682'
            )
            , array(
                  'Name'               => 'Senegal'
                , 'ISO_3166-1_Alpha_2' => 'SN'
                , 'ISO_3166-1_Alpha_3' => 'SEN'
                , 'ISO_3166-1_Numeric' => '686'
            )
            , array(
                  'Name'               => 'Serbia'
                , 'ISO_3166-1_Alpha_2' => 'RS'
                , 'ISO_3166-1_Alpha_3' => 'SRB'
                , 'ISO_3166-1_Numeric' => '688'
            )
            , array(
                  'Name'               => 'Seychelles'
                , 'ISO_3166-1_Alpha_2' => 'SC'
                , 'ISO_3166-1_Alpha_3' => 'SYC'
                , 'ISO_3166-1_Numeric' => '690'
            )
            , array(
                  'Name'               => 'Sierra Leone'
                , 'ISO_3166-1_Alpha_2' => 'SL'
                , 'ISO_3166-1_Alpha_3' => 'SLE'
                , 'ISO_3166-1_Numeric' => '694'
            )
            , array(
                  'Name'               => 'Singapore'
                , 'ISO_3166-1_Alpha_2' => 'SG'
                , 'ISO_3166-1_Alpha_3' => 'SGP'
                , 'ISO_3166-1_Numeric' => '702'
            )
            , array(
                  'Name'               => 'Slovakia'
                , 'ISO_3166-1_Alpha_2' => 'SK'
                , 'ISO_3166-1_Alpha_3' => 'SVK'
                , 'ISO_3166-1_Numeric' => '703'
            )
            , array(
                  'Name'               => 'Slovenia'
                , 'ISO_3166-1_Alpha_2' => 'SI'
                , 'ISO_3166-1_Alpha_3' => 'SVN'
                , 'ISO_3166-1_Numeric' => '705'
            )
            , array(
                  'Name'               => 'Solomon Islands'
                , 'ISO_3166-1_Alpha_2' => 'SB'
                , 'ISO_3166-1_Alpha_3' => 'SLB'
                , 'ISO_3166-1_Numeric' => '90'
            )
            , array(
                  'Name'               => 'Somalia'
                , 'ISO_3166-1_Alpha_2' => 'SO'
                , 'ISO_3166-1_Alpha_3' => 'SOM'
                , 'ISO_3166-1_Numeric' => '706'
            )
            , array(
                  'Name'               => 'Somaliland'
                , 'ISO_3166-1_Alpha_2' => 'SO'
                , 'ISO_3166-1_Alpha_3' => 'SOM'
                , 'ISO_3166-1_Numeric' => '706'
            )
            , array(
                  'Name'               => 'South Africa'
                , 'ISO_3166-1_Alpha_2' => 'ZA'
                , 'ISO_3166-1_Alpha_3' => 'ZAF'
                , 'ISO_3166-1_Numeric' => '710'
            )
            , array(
                  'Name'               => 'South Georgia & South Sandwich Islands'
                , 'ISO_3166-1_Alpha_2' => 'GS'
                , 'ISO_3166-1_Alpha_3' => 'SGS'
                , 'ISO_3166-1_Numeric' => '239'
            )
            , array(
                  'Name'               => 'South Ossetia'
                , 'ISO_3166-1_Alpha_2' => 'GE'
                , 'ISO_3166-1_Alpha_3' => 'GEO'
                , 'ISO_3166-1_Numeric' => '268'
            )
            , array(
                  'Name'               => 'Spain'
                , 'ISO_3166-1_Alpha_2' => 'ES'
                , 'ISO_3166-1_Alpha_3' => 'ESP'
                , 'ISO_3166-1_Numeric' => '724'
            )
            , array(
                  'Name'               => 'Sri Lanka'
                , 'ISO_3166-1_Alpha_2' => 'LK'
                , 'ISO_3166-1_Alpha_3' => 'LKA'
                , 'ISO_3166-1_Numeric' => '144'
            )
            , array(
                  'Name'               => 'Sudan'
                , 'ISO_3166-1_Alpha_2' => 'SD'
                , 'ISO_3166-1_Alpha_3' => 'SDN'
                , 'ISO_3166-1_Numeric' => '736'
            )
            , array(
                  'Name'               => 'Suriname'
                , 'ISO_3166-1_Alpha_2' => 'SR'
                , 'ISO_3166-1_Alpha_3' => 'SUR'
                , 'ISO_3166-1_Numeric' => '740'
            )
            , array(
                  'Name'               => 'Svalbard'
                , 'ISO_3166-1_Alpha_2' => 'SJ'
                , 'ISO_3166-1_Alpha_3' => 'SJM'
                , 'ISO_3166-1_Numeric' => '744'
            )
            , array(
                  'Name'               => 'Swaziland'
                , 'ISO_3166-1_Alpha_2' => 'SZ'
                , 'ISO_3166-1_Alpha_3' => 'SWZ'
                , 'ISO_3166-1_Numeric' => '748'
            )
            , array(
                  'Name'               => 'Sweden'
                , 'ISO_3166-1_Alpha_2' => 'SE'
                , 'ISO_3166-1_Alpha_3' => 'SWE'
                , 'ISO_3166-1_Numeric' => '752'
            )
            , array(
                  'Name'               => 'Switzerland'
                , 'ISO_3166-1_Alpha_2' => 'CH'
                , 'ISO_3166-1_Alpha_3' => 'CHE'
                , 'ISO_3166-1_Numeric' => '756'
            )
            , array(
                  'Name'               => 'Syria'
                , 'ISO_3166-1_Alpha_2' => 'SY'
                , 'ISO_3166-1_Alpha_3' => 'SYR'
                , 'ISO_3166-1_Numeric' => '760'
            )
            , array(
                  'Name'               => 'Tajikistan'
                , 'ISO_3166-1_Alpha_2' => 'TJ'
                , 'ISO_3166-1_Alpha_3' => 'TJK'
                , 'ISO_3166-1_Numeric' => '762'
            )
            , array(
                  'Name'               => 'Tanzania'
                , 'ISO_3166-1_Alpha_2' => 'TZ'
                , 'ISO_3166-1_Alpha_3' => 'TZA'
                , 'ISO_3166-1_Numeric' => '834'
            )
            , array(
                  'Name'               => 'Thailand'
                , 'ISO_3166-1_Alpha_2' => 'TH'
                , 'ISO_3166-1_Alpha_3' => 'THA'
                , 'ISO_3166-1_Numeric' => '764'
            )
            , array(
                  'Name'               => 'Timor-Leste (East Timor)'
                , 'ISO_3166-1_Alpha_2' => 'TL'
                , 'ISO_3166-1_Alpha_3' => 'TLS'
                , 'ISO_3166-1_Numeric' => '626'
            )
            , array(
                  'Name'               => 'Togo'
                , 'ISO_3166-1_Alpha_2' => 'TG'
                , 'ISO_3166-1_Alpha_3' => 'TGO'
                , 'ISO_3166-1_Numeric' => '768'
            )
            , array(
                  'Name'               => 'Tokelau'
                , 'ISO_3166-1_Alpha_2' => 'TK'
                , 'ISO_3166-1_Alpha_3' => 'TKL'
                , 'ISO_3166-1_Numeric' => '772'
            )
            , array(
                  'Name'               => 'Tonga'
                , 'ISO_3166-1_Alpha_2' => 'TO'
                , 'ISO_3166-1_Alpha_3' => 'TON'
                , 'ISO_3166-1_Numeric' => '776'
            )
            , array(
                  'Name'               => 'Trinidad and Tobago'
                , 'ISO_3166-1_Alpha_2' => 'TT'
                , 'ISO_3166-1_Alpha_3' => 'TTO'
                , 'ISO_3166-1_Numeric' => '780'
            )
            , array(
                  'Name'               => 'Tristan da Cunha'
                , 'ISO_3166-1_Alpha_2' => 'TA'
                , 'ISO_3166-1_Alpha_3' => 'TAA'
                , 'ISO_3166-1_Numeric' =>' '
            )
            , array(
                  'Name'               => 'Tunisia'
                , 'ISO_3166-1_Alpha_2' => 'TN'
                , 'ISO_3166-1_Alpha_3' => 'TUN'
                , 'ISO_3166-1_Numeric' => '788'
            )
            , array(
                  'Name'               => 'Turkey'
                , 'ISO_3166-1_Alpha_2' => 'TR'
                , 'ISO_3166-1_Alpha_3' => 'TUR'
                , 'ISO_3166-1_Numeric' => '792'
            )
            , array(
                  'Name'               => 'Turkmenistan'
                , 'ISO_3166-1_Alpha_2' => 'TM'
                , 'ISO_3166-1_Alpha_3' => 'TKM'
                , 'ISO_3166-1_Numeric' => '795'
            )
            , array(
                  'Name'               => 'Turks and Caicos Islands'
                , 'ISO_3166-1_Alpha_2' => 'TC'
                , 'ISO_3166-1_Alpha_3' => 'TCA'
                , 'ISO_3166-1_Numeric' => '796'
            )
            , array(
                  'Name'               => 'Tuvalu'
                , 'ISO_3166-1_Alpha_2' => 'TV'
                , 'ISO_3166-1_Alpha_3' => 'TUV'
                , 'ISO_3166-1_Numeric' => '798'
            )
            , array(
                  'Name'               => 'U.S. Virgin Islands'
                , 'ISO_3166-1_Alpha_2' => 'VI'
                , 'ISO_3166-1_Alpha_3' => 'VIR'
                , 'ISO_3166-1_Numeric' => '850'
            )
            , array(
                  'Name'               => 'Uganda'
                , 'ISO_3166-1_Alpha_2' => 'UG'
                , 'ISO_3166-1_Alpha_3' => 'UGA'
                , 'ISO_3166-1_Numeric' => '800'
            )
            , array(
                  'Name'               => 'Ukraine'
                , 'ISO_3166-1_Alpha_2' => 'UA'
                , 'ISO_3166-1_Alpha_3' => 'UKR'
                , 'ISO_3166-1_Numeric' => '804'
            )
            , array(
                  'Name'               => 'United Arab Emirates'
                , 'ISO_3166-1_Alpha_2' => 'AE'
                , 'ISO_3166-1_Alpha_3' => 'ARE'
                , 'ISO_3166-1_Numeric' => '784'
            )
            , array(
                  'Name'               => 'United Kingdom'
                , 'ISO_3166-1_Alpha_2' => 'GB'
                , 'ISO_3166-1_Alpha_3' => 'GBR'
                , 'ISO_3166-1_Numeric' => '826'
            )
            , array(
                  'Name'               => 'United States'
                , 'ISO_3166-1_Alpha_2' => 'US'
                , 'ISO_3166-1_Alpha_3' => 'USA'
                , 'ISO_3166-1_Numeric' => '840'
            )
            , array(
                  'Name'               => 'Uruguay'
                , 'ISO_3166-1_Alpha_2' => 'UY'
                , 'ISO_3166-1_Alpha_3' => 'URY'
                , 'ISO_3166-1_Numeric' => '858'
            )
            , array(
                  'Name'               => 'Uzbekistan'
                , 'ISO_3166-1_Alpha_2' => 'UZ'
                , 'ISO_3166-1_Alpha_3' => 'UZB'
                , 'ISO_3166-1_Numeric' => '860'
            )
            , array(
                  'Name'               => 'Vanuatu'
                , 'ISO_3166-1_Alpha_2' => 'VU'
                , 'ISO_3166-1_Alpha_3' => 'VUT'
                , 'ISO_3166-1_Numeric' => '548'
            )
            , array(
                  'Name'               => 'Vatican City'
                , 'ISO_3166-1_Alpha_2' => 'VA'
                , 'ISO_3166-1_Alpha_3' => 'VAT'
                , 'ISO_3166-1_Numeric' => '336'
            )
            , array(
                  'Name'               => 'Venezuela'
                , 'ISO_3166-1_Alpha_2' => 'VE'
                , 'ISO_3166-1_Alpha_3' => 'VEN'
                , 'ISO_3166-1_Numeric' => '862'
            )
            , array(
                  'Name'               => 'Vietnam'
                , 'ISO_3166-1_Alpha_2' => 'VN'
                , 'ISO_3166-1_Alpha_3' => 'VNM'
                , 'ISO_3166-1_Numeric' => '704'
            )
            , array(
                  'Name'               => 'Wake Island'
                , 'ISO_3166-1_Alpha_2' => 'UM'
                , 'ISO_3166-1_Alpha_3' => 'UMI'
                , 'ISO_3166-1_Numeric' => '850'
            )
            , array(
                  'Name'               => 'Wallis and Futuna'
                , 'ISO_3166-1_Alpha_2' => 'WF'
                , 'ISO_3166-1_Alpha_3' => 'WLF'
                , 'ISO_3166-1_Numeric' => '876'
            )
            , array(
                  'Name'               => 'Yemen'
                , 'ISO_3166-1_Alpha_2' => 'YE'
                , 'ISO_3166-1_Alpha_3' => 'YEM'
                , 'ISO_3166-1_Numeric' => '887'
            )
            , array(
                  'Name'               => 'Zambia'
                , 'ISO_3166-1_Alpha_2' => 'ZM'
                , 'ISO_3166-1_Alpha_3' => 'ZMB'
                , 'ISO_3166-1_Numeric' => '894'
            )
            , array(
                  'Name'               => 'Zimbabwe'
                , 'ISO_3166-1_Alpha_2' => 'ZW'
                , 'ISO_3166-1_Alpha_3' => 'ZWE'
                , 'ISO_3166-1_Numeric' => '716'
            )
        );
    }

    /**
     * @url GET /
     */
    public function get($request_data = null) {
         if (!\ClubSpeed\Security\Authenticate::publicAccess())
            throw new RestException(401, "Invalid authorization!");
        try {
            return $this->countries;
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}