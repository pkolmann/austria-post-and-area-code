#!/usr/bin/php
<?php
require_once('phayes-geoPHP-1.2-20-g6855624/geoPHP.inc');

/*
 * https://overpass-turbo.eu/

[out:json][timeout:25];
(relation["boundary"="administrative"][admin_level=8]({{bbox}}););
out body;
>;
out skel qt;

*/

$gemeinden = json_decode(file_get_contents('gemeinden_95_geo.json'), true);
$wien = json_decode(file_get_contents('BezirksgrenzenWien.json'), true);
$wienMap = array();
foreach ($wien['features'] as $bez) {
    $name = 'Wien '.$bez['properties']['NAMEK_NUM'];
    $iso = $bez['properties']['STATAUSTRIA_GEM_CODE'];
    $wienMap['Wien-'.$bez['properties']['BEZ']] = $name;
    $bez['properties'] = array();
    $bez['properties']['name'] = $name;
    $bez['properties']['iso'] = $iso;
    $gemeinden['features'][] = $bez;
}

foreach ($gemeinden['features'] as $gk => $gd) {
    if (
        array_key_exists('geometry', $gd)
        && array_key_exists('type', $gd['geometry'])
        && array_key_exists('coordinates', $gd['geometry'])
    ) {
        try {
            $geometry = geoPHP::load(json_encode($gd['geometry']));
        } catch (Exception $e) {
            print "Error loading geoPHP: $gk - {$gd['properties']['name']}:\n";
            print $e->getMessage();
            die("\n\n");
        }
        $centroid = $geometry->centroid();

        $gemeinden['features'][$gk]['properties']['centroid'] = [
            $centroid->{'coords'}[0],
            $centroid->{'coords'}[1]
        ];
    }
}

$telefonDaten = json_decode(file_get_contents('rnOrtsnetze.json'), true);
$plzDaten = json_decode(file_get_contents('plz.json'), true);

// https://de.wikipedia.org/wiki/Telefonvorwahl_(%C3%96sterreich)
$mapping = array();
$mapping[2165] = "Hainburg a. d. Donau";
$mapping[2214] = "Eckartsau";
$mapping[2215] = "Andlersdorf";
$mapping[2233] = "Pressbaum";
$mapping[2255] = "Leithaprodersdorf";
$mapping[2264] = "Harmannsdorf";
$mapping[2269] = "Niederhollabrunn";
$mapping[2271] = "Sieghartskirchen";
$mapping[2276] = "Sitzenberg-Reidling";
$mapping[2277] = "Zwentendorf an der Donau";
$mapping[2284] = "Weiden an der March";
$mapping[2287] = "Strasshof an der Nordbahn";
$mapping[2289] = "Matzen-Raggendorf";
$mapping[2523] = "Neudorf bei Staatz";
$mapping[2524] = "Staatz";
$mapping[2527] = "Laa an der Thaya";
$mapping[2534] = "Sulz im Weinviertel";
$mapping[2554] = "Drasenhofen";
$mapping[2614] = "Großwarasdorf";
$mapping[2639] = "Bad Fischau-Brunn";
$mapping[2642] = "Aspang-Markt";
$mapping[2648] = "Hochneukirchen-Gschaidt";
$mapping[2665] = "Reichenau an der Rax";
$mapping[2666] = "Reichenau an der Rax";
$mapping[2674] = "Weissenbach an der Triesting";
$mapping[2714] = "Rossatz-Arnsdorf";
$mapping[2717] = "Senftenberg";
$mapping[2728] = "Annaberg";
$mapping[2731] = "Krumau am Kamp";
$mapping[2735] = "Hadersdorf-Kammern";
$mapping[2739] = "Paudorf";
$mapping[2741] = "Neidling";
$mapping[2753] = "Dunkelsteinerwald";
$mapping[2756] = "St. Leonhard am Forst";
$mapping[2774] = "Neustift-Innermanzing";
$mapping[2784] = "Weißenkirchen an der Perschling";
$mapping[2786] = "Wölbling";
$mapping[2816] = "Bad Großpertholz";
$mapping[2784] = "Weißenkirchen an der Perschling";
$mapping[2823] = "Zwettl-Niederösterreich";
$mapping[2845] = "Raabs an der Thaya";
$mapping[2848] = "Pfaffenschlag bei Waidhofen a. d. Thaya";
$mapping[2873] = "Kottes-Purk";
$mapping[2876] = "Albrechtsberg an der großen Krems";
$mapping[2877] = "Sallingberg";
$mapping[2878] = "Bad Traunstein";
$mapping[2913] = "Geras";
$mapping[2916] = "Hardegg";
$mapping[2943] = "Hadres";
$mapping[2947] = "Sigmundsherberg";
$mapping[2949] = "Hardegg";
$mapping[2953] = "Nappersdorf-Kammersdorf";
$mapping[2957] = "Hohenwarth-Mühlbach am Manhartsberg";
$mapping[2986] = "Irnfritz-Messern";
$mapping[2987] = "St. Leonhard am Hornerwald";
$mapping[2988] = "Pölla";

$mapping[3113] = "Pischelsdorf";
$mapping[3114] = "Hartmannsdorf";
$mapping[3115] = "Kirchberg";
$mapping[3116] = "Kirchbach";
$mapping[3117] = "Eggersdorf";
$mapping[3119] = "St. Marein";
$mapping[3123] = "St. Oswald";
$mapping[3134] = "Heiligenkreuz";
$mapping[3135] = "Kalsdorf";
$mapping[3136] = "Dobl-Zwaring";
$mapping[3137] = "Söding-St. Johann";
$mapping[3140] = "St. Martin";
$mapping[3141] = "Hirschegg-Pack";
$mapping[3143] = "Krottendorf-Gaisfeld";
$mapping[3146] = "Edelschrott";
$mapping[3147] = "Maria Lankowitz";
$mapping[3148] = "Kainach";
$mapping[3149] = "Geistthal-Södingberg";
$mapping[3158] = "St. Anna";
$mapping[3159] = "Gleichenberg";
$mapping[3177] = "Puch";
$mapping[3178] = "St. Ruprech"; // BUG: St. Ruprecht
$mapping[3183] = "St. Georgen";
$mapping[3184] = "Schwarzautal";
$mapping[3331] = "St. Lorenzen";
$mapping[3333] = "Waltersdorf";
$mapping[3336] = "Waldbach-Mönichwald";
$mapping[3456] = "Kitzeck";
$mapping[3460] = "Eibiswald";
$mapping[3461] = "Deutschlandsberg";
$mapping[3464] = "St. Florian";
$mapping[3468] = "Eibiswald";
$mapping[3469] = "Deutschlandsberg";
$mapping[3475] = "Halbenrain";
$mapping[3476] = "Radkersburg";
$mapping[3477] = "St. Peter";
$mapping[3513] = "Gaal";
$mapping[3515] = "St. Margarethen";
$mapping[3516] = "Großlobming";
$mapping[3533] = "Stadl-Predlitz";
$mapping[3534] = "Stadl-Predlitz";
$mapping[3535] = "Krakau";
$mapping[3536] = "St. Peter";
$mapping[3537] = "St. Georgen";
$mapping[3571] = "Pölstal";
$mapping[3575] = "Pölstal";
$mapping[3576] = "Pölstal";
$mapping[3579] = "Pöls-Oberkurzheim";
$mapping[3583] = "Unzmarkt-Frauenburg";
$mapping[3584] = "Neumarkt";
$mapping[3587] = "Oberwölz";
$mapping[3588] = "Teufenbach-Katsch";
$mapping[3611] = "Admont";
$mapping[3617] = "Gaishorn";
$mapping[3619] = "Rottenmann";
$mapping[3622] = "Aussee";
$mapping[3623] = "Mitterndorf";
$mapping[3624] = "Mitterndorf";
$mapping[3631] = "St. Gallen";
$mapping[3634] = "Landl";
$mapping[3637] = "Landl";
$mapping[3638] = "Landl";
$mapping[3680] = "Irdning-Donnersbachtal";
$mapping[3682] = "Stainach-Pürgg";
$mapping[3683] = "Irdning-Donnersbachtal";
$mapping[3684] = "Mitterberg-St. Martin";
$mapping[3688] = "Mitterndorf";
$mapping[3689] = "Sölk";
$mapping[3832] = "Kraubath";
$mapping[3834] = "Wald";
$mapping[3843] = "St. Michael";
$mapping[3844] = "Kammern";
$mapping[3845] = "Mautern";
$mapping[3853] = "Spita"; // BUG: Spital am Semmering
$mapping[3856] = "St. Barbara";
$mapping[3857] = "Neuberg";
$mapping[3858] = "St. Barbara";
$mapping[3859] = "Neuberg";
$mapping[3862] = "Bruck";
$mapping[3864] = "St. Marein";
$mapping[3866] = "Breitenau";
$mapping[3867] = "Pernegg";
$mapping[3868] = "Tragöß-St. Katharein";
$mapping[3869] = "Tragöß-St. Katharein";
$mapping[3883] = "Mariazell";
$mapping[3884] = "Mariazell";
$mapping[3885] = "Mariazell";
$mapping[3886] = "Mariazell";

$mapping[4213] = "St. Georgen am Längssee";
$mapping[4224] = "Poggersdorf";
$mapping[4229] = "Krumpendorf am Wörthersee";
$mapping[4231] = "Völkermarkt";
$mapping[4237] = "Sittersdorf";
$mapping[4239] = "St. Kanzian am Klopeinersee";
$mapping[4243] = "Steindorf am Ossiacher See";
$mapping[4245] = "Paternion";
$mapping[4254] = "Finkenstein";
$mapping[4257] = "Finkenstein";
$mapping[4258] = "Weißenstein";
$mapping[4262] = "Althofen";
$mapping[4269] = "Glödnitz";
$mapping[4272] = "Pörtschach am Wörthersee";
$mapping[4273] = "Maria Wörth";
$mapping[4274] = "Velden am Wörthersee";
$mapping[4275] = "Reichenau";
$mapping[4279] = "Albeck";
$mapping[4282] = "Hermagor-Pressegger See";
$mapping[4285] = "Hermagor-Pressegger See";
$mapping[4286] = "Gitschtal";
$mapping[4353] = "Wolfsberg";
$mapping[4355] = "St. Andrä";
$mapping[463] = "Klagenfurt am Wörthersee";
$mapping[4713] = "Weissensee";
$mapping[4732] = "Gmünd";
$mapping[4734] = "Rennweg am Katschberg";
$mapping[4735] = "Krems in Kärnten";
$mapping[4736] = "Krems in Kärnten";
$mapping[4767] = "Spittal an der Drau";
$mapping[4769] = "Lurnfeld";
$mapping[4785] = "Flattach";
$mapping[4823] = "Rangersdorf";
$mapping[4872] = "Matrei in Osttirol";


$mapping[5223] = "Hall in Tirol";
$mapping[5239] = "Silz";
$mapping[5243] = "Buch in Tirol";
$mapping[5245] = "Vomp";
$mapping[5256] = "Sölden";
$mapping[5266] = "Haiming";
$mapping[5279] = "Vals";
$mapping[5280] = "Fügenberg";
$mapping[5286] = "Mayrhofen";
$mapping[5289] = "Brandberg";
$mapping[5413] = "St. Leonhard im Pitztal";
$mapping[5475] = "Kaunertal";
$mapping[5517] = "Mittelberg";
$mapping[5557] = "St. Gallenkirc"; // BUG: St. Gallenkirch
$mapping[5575] = "Langen";
$mapping[5633] = "Steeg";

$mapping[6135] = "Bad Goisern am Hallstättersee";
$mapping[6454] = "Radstadt";
$mapping[6456] = "Untertauern";
$mapping[6470] = "Tamsweg";
$mapping[6541] = "Saalbach-Hinterglemm";
$mapping[6545] = "Bruck an der Großglocknerstraße";
$mapping[6546] = "Fusch an der Großglocknerstraße";

$mapping[7218] = "Liebenau";
$mapping[7260] = "Waldhausen im Strudengau";
$mapping[7267] = "Königswiesen";
$mapping[7281] = "Aigen-Schägl";
$mapping[7289] = "Rohrbach-Berg";
$mapping[7357] = "Weyer";
$mapping[7414] = "Nöchling";
$mapping[7415] = "Yspertal";
$mapping[7433] = "Wallsee-Sindelburg";
$mapping[7471] = "Neustadtl an der Donau";
$mapping[7475] = "Amstetten";
$mapping[7480] = "Gaming";
$mapping[7618] = "Altmünster";
$mapping[7666] = "Attersee am Attersee";
$mapping[7727] = "Hochburg-Ach";
$mapping[7745] = "Lochen am See";
$mapping[7746] = "Lengau";


$vorwahl2gkz = array();
$vorwahl2gkz[2629] = 31843;
$vorwahl2gkz[2647] = 32315;
$vorwahl2gkz[2852] = 30908;
$vorwahl2gkz[3116] = 62381;
$vorwahl2gkz[3119] = 60668;
$vorwahl2gkz[3123] = 60641;
$vorwahl2gkz[3134] = 61052;
$vorwahl2gkz[3140] = 61621;
$vorwahl2gkz[3183] = 61055;
$vorwahl2gkz[3331] = 62245;
$vorwahl2gkz[3464] = 60346;
$vorwahl2gkz[3477] = 62388;
$vorwahl2gkz[3515] = 62046;
$vorwahl2gkz[3536] = 61425;
$vorwahl2gkz[3537] = 61442;
$vorwahl2gkz[3864] = 62146;
$vorwahl2gkz[3866] = 62105;
$vorwahl2gkz[3867] = 62125;
$vorwahl2gkz[3868] = 62125;
$vorwahl2gkz[4284] = 20306;
$vorwahl2gkz[4732] = 20608;
$vorwahl2gkz[7224] = 41013;


$plzmapping = array();
$plzmapping[1010] = $wienMap['Wien-01'];
$plzmapping[1011] = $wienMap['Wien-01'];
$plzmapping[1015] = $wienMap['Wien-01'];
$plzmapping[1016] = $wienMap['Wien-01'];
$plzmapping[1017] = $wienMap['Wien-01'];
$plzmapping[1020] = $wienMap['Wien-02'];
$plzmapping[1021] = $wienMap['Wien-02'];
$plzmapping[1024] = $wienMap['Wien-02'];
$plzmapping[1025] = $wienMap['Wien-02'];
$plzmapping[1029] = $wienMap['Wien-02'];
$plzmapping[1030] = $wienMap['Wien-03'];
$plzmapping[1031] = $wienMap['Wien-03'];
$plzmapping[1032] = $wienMap['Wien-03'];
$plzmapping[1035] = $wienMap['Wien-03'];
$plzmapping[1037] = $wienMap['Wien-03'];
$plzmapping[1038] = $wienMap['Wien-03'];
$plzmapping[1039] = $wienMap['Wien-03'];
$plzmapping[1040] = $wienMap['Wien-04'];
$plzmapping[1041] = $wienMap['Wien-04'];
$plzmapping[1042] = $wienMap['Wien-04'];
$plzmapping[1043] = $wienMap['Wien-04'];
$plzmapping[1045] = $wienMap['Wien-04'];
$plzmapping[1050] = $wienMap['Wien-05'];
$plzmapping[1051] = $wienMap['Wien-05'];
$plzmapping[1052] = $wienMap['Wien-05'];
$plzmapping[1053] = $wienMap['Wien-05'];
$plzmapping[1060] = $wienMap['Wien-06'];
$plzmapping[1061] = $wienMap['Wien-06'];
$plzmapping[1063] = $wienMap['Wien-06'];
$plzmapping[1064] = $wienMap['Wien-06'];
$plzmapping[1065] = $wienMap['Wien-06'];
$plzmapping[1070] = $wienMap['Wien-07'];
$plzmapping[1071] = $wienMap['Wien-07'];
$plzmapping[1072] = $wienMap['Wien-07'];
$plzmapping[1075] = $wienMap['Wien-07'];
$plzmapping[1080] = $wienMap['Wien-08'];
$plzmapping[1081] = $wienMap['Wien-08'];
$plzmapping[1082] = $wienMap['Wien-08'];
$plzmapping[1090] = $wienMap['Wien-09'];
$plzmapping[1091] = $wienMap['Wien-09'];
$plzmapping[1092] = $wienMap['Wien-09'];
$plzmapping[1095] = $wienMap['Wien-09'];
$plzmapping[1097] = $wienMap['Wien-09'];
$plzmapping[1100] = $wienMap['Wien-10'];
$plzmapping[1101] = $wienMap['Wien-10'];
$plzmapping[1103] = $wienMap['Wien-10'];
$plzmapping[1104] = $wienMap['Wien-10'];
$plzmapping[1105] = $wienMap['Wien-10'];
$plzmapping[1106] = $wienMap['Wien-10'];
$plzmapping[1107] = $wienMap['Wien-10'];
$plzmapping[1108] = $wienMap['Wien-10'];
$plzmapping[1109] = $wienMap['Wien-10'];
$plzmapping[1110] = $wienMap['Wien-11'];
$plzmapping[1111] = $wienMap['Wien-11'];
$plzmapping[1114] = $wienMap['Wien-11'];
$plzmapping[1115] = $wienMap['Wien-11'];
$plzmapping[1120] = $wienMap['Wien-12'];
$plzmapping[1121] = $wienMap['Wien-12'];
$plzmapping[1122] = $wienMap['Wien-12'];
$plzmapping[1124] = $wienMap['Wien-12'];
$plzmapping[1125] = $wienMap['Wien-12'];
$plzmapping[1127] = $wienMap['Wien-12'];
$plzmapping[1128] = $wienMap['Wien-12'];
$plzmapping[1130] = $wienMap['Wien-13'];
$plzmapping[1131] = $wienMap['Wien-13'];
$plzmapping[1132] = $wienMap['Wien-13'];
$plzmapping[1133] = $wienMap['Wien-13'];
$plzmapping[1134] = $wienMap['Wien-13'];
$plzmapping[1136] = $wienMap['Wien-13'];
$plzmapping[1140] = $wienMap['Wien-14'];
$plzmapping[1141] = $wienMap['Wien-14'];
$plzmapping[1142] = $wienMap['Wien-14'];
$plzmapping[1143] = $wienMap['Wien-14'];
$plzmapping[1146] = $wienMap['Wien-14'];
$plzmapping[1147] = $wienMap['Wien-14'];
$plzmapping[1148] = $wienMap['Wien-14'];
$plzmapping[1150] = $wienMap['Wien-15'];
$plzmapping[1151] = $wienMap['Wien-15'];
$plzmapping[1152] = $wienMap['Wien-15'];
$plzmapping[1153] = $wienMap['Wien-15'];
$plzmapping[1155] = $wienMap['Wien-15'];
$plzmapping[1156] = $wienMap['Wien-15'];
$plzmapping[1160] = $wienMap['Wien-16'];
$plzmapping[1161] = $wienMap['Wien-16'];
$plzmapping[1163] = $wienMap['Wien-16'];
$plzmapping[1165] = $wienMap['Wien-16'];
$plzmapping[1166] = $wienMap['Wien-16'];
$plzmapping[1170] = $wienMap['Wien-17'];
$plzmapping[1171] = $wienMap['Wien-17'];
$plzmapping[1172] = $wienMap['Wien-17'];
$plzmapping[1180] = $wienMap['Wien-18'];
$plzmapping[1181] = $wienMap['Wien-18'];
$plzmapping[1182] = $wienMap['Wien-18'];
$plzmapping[1183] = $wienMap['Wien-18'];
$plzmapping[1190] = $wienMap['Wien-19'];
$plzmapping[1191] = $wienMap['Wien-19'];
$plzmapping[1192] = $wienMap['Wien-19'];
$plzmapping[1193] = $wienMap['Wien-19'];
$plzmapping[1194] = $wienMap['Wien-19'];
$plzmapping[1195] = $wienMap['Wien-19'];
$plzmapping[1196] = $wienMap['Wien-19'];
$plzmapping[1200] = $wienMap['Wien-20'];
$plzmapping[1201] = $wienMap['Wien-20'];
$plzmapping[1203] = $wienMap['Wien-20'];
$plzmapping[1205] = $wienMap['Wien-20'];
$plzmapping[1208] = $wienMap['Wien-20'];
$plzmapping[1210] = $wienMap['Wien-21'];
$plzmapping[1211] = $wienMap['Wien-21'];
$plzmapping[1213] = $wienMap['Wien-21'];
$plzmapping[1215] = $wienMap['Wien-21'];
$plzmapping[1217] = $wienMap['Wien-21'];
$plzmapping[1218] = $wienMap['Wien-21'];
$plzmapping[1219] = $wienMap['Wien-21'];
$plzmapping[1220] = $wienMap['Wien-22'];
$plzmapping[1221] = $wienMap['Wien-22'];
$plzmapping[1222] = $wienMap['Wien-22'];
$plzmapping[1223] = $wienMap['Wien-22'];
$plzmapping[1224] = $wienMap['Wien-22'];
$plzmapping[1225] = $wienMap['Wien-22'];
$plzmapping[1228] = $wienMap['Wien-22'];
$plzmapping[1229] = $wienMap['Wien-22'];
$plzmapping[1230] = $wienMap['Wien-23'];
$plzmapping[1231] = $wienMap['Wien-23'];
$plzmapping[1233] = $wienMap['Wien-23'];
$plzmapping[1235] = $wienMap['Wien-23'];
$plzmapping[1236] = $wienMap['Wien-23'];
$plzmapping[1238] = $wienMap['Wien-23'];
$plzmapping[1239] = $wienMap['Wien-23'];
$plzmapping[1300] = "Wien";
$plzmapping[1400] = "Wien";
$plzmapping[1500] = "Wien";
$plzmapping[1502] = "Wien";
$plzmapping[1503] = "Wien";

$plzmapping[2014] = "Hollabrunn";
$plzmapping[2022] = "Wullersdorf";
$plzmapping[2023] = "Nappersdorf-Kammersdorf";
$plzmapping[2031] = "Hollabrunn";
$plzmapping[2032] = "Hollabrunn";
$plzmapping[2033] = "Nappersdorf-Kammersdorf";
$plzmapping[2052] = "Pernersdorf";
$plzmapping[2053] = "Haugsdorf";
$plzmapping[2062] = "Seefeld-Kadolz";
$plzmapping[2063] = "Großharras";
$plzmapping[2064] = "Laa an der Thaya";
$plzmapping[2074] = "Retzbach";
$plzmapping[2081] = "Hardegg";
$plzmapping[2083] = "Hardegg";
$plzmapping[2092] = "Hardegg";
$plzmapping[2094] = "Drosendorf-Zissersdorf";
$plzmapping[2095] = "Drosendorf-Zissersdorf";
$plzmapping[2105] = "Leobendorf";
$plzmapping[2106] = "Bisamberg";
$plzmapping[2111] = "Harmannsdorf";
$plzmapping[2112] = "Harmannsdorf";
$plzmapping[2113] = "Großrußbach";
$plzmapping[2122] = "Ulrichskirchen-Schleinbach";
$plzmapping[2123] = "Ulrichskirchen-Schleinbach";
$plzmapping[2124] = "Kreuzstetten";
$plzmapping[2125] = "Ladendorf";
$plzmapping[2127] = "Kreuttal";
$plzmapping[2128] = "Hochleithen";
$plzmapping[2132] = "Mistelbach";
$plzmapping[2133] = "Fallbach";
$plzmapping[2134] = "Staatz";
$plzmapping[2135] = "Neudorf bei Staatz";
$plzmapping[2137] = "Mistelbach";
$plzmapping[2141] = "Staatz";
$plzmapping[2161] = "Poysdorf";
$plzmapping[2173] = "Poysdorf";
$plzmapping[2181] = "Palterndorf-Dobermannsdorf";
$plzmapping[2182] = "Palterndorf-Dobermannsdorf";
$plzmapping[2185] = "Hauskirchen";
$plzmapping[2192] = "Mistelbach";
$plzmapping[2201] = "Gerasdorf bei Wien";
$plzmapping[2202] = "Enzersfeld im Weinviertel";
$plzmapping[2215] = "Matzen-Raggendorf";
$plzmapping[2221] = "Groß-Schweinbarth";
$plzmapping[2224] = "Sulz im Weinviertel";
$plzmapping[2232] = "Deutsch-Wagram";
$plzmapping[2241] = "Schönkirchen-Reyersdorf";
$plzmapping[2243] = "Matzen-Raggendorf";
$plzmapping[2252] = "Angern an der March";
$plzmapping[2262] = "Angern an der March";
$plzmapping[2272] = "Ringelsdorf-Niederabsdorf";
$plzmapping[2276] = "Bernhardsthal";
$plzmapping[2293] = "Marchegg";
$plzmapping[2294] = "Marchegg";
$plzmapping[2295] = "Weiden an der March";
$plzmapping[2324] = "Schwechat";
$plzmapping[2326] = "Maria-Lanzendorf";
$plzmapping[2346] = "Maria Enzersdorf";
$plzmapping[2392] = "Wienerwald";
$plzmapping[2393] = "Wienerwald";
$plzmapping[2402] = "Haslau-Maria Ellend";
$plzmapping[2403] = "Scharndorf";
$plzmapping[2404] = "Petronell-Carnuntum";
$plzmapping[2405] = "Bad Deutsch-Altenburg";
$plzmapping[2406] = "Haslau-Maria Ellend";
$plzmapping[2410] = "Hainburg a. d. Donau";
$plzmapping[2431] = "Klein-Neusiedl";
$plzmapping[2433] = "Enzersdorf an der Fischa";
$plzmapping[2442] = "Ebreichsdorf";
$plzmapping[2443] = "Seibersdorf";
$plzmapping[2462] = "Bruck an der Leitha";
$plzmapping[2463] = "Trautmannsdorf an der Leitha";
$plzmapping[2464] = "Göttlesbrunn-Arbesthal";
$plzmapping[2484] = "Ebreichsdorf";
$plzmapping[2485] = "Pottendorf";
$plzmapping[2493] = "Lichtenwörth";
$plzmapping[2505] = "Baden";
$plzmapping[2512] = "Traiskirchen";
$plzmapping[2513] = "Traiskirchen";
$plzmapping[2532] = "Heiligenkreuz";
$plzmapping[2565] = "Weissenbach an der Triesting";
$plzmapping[2571] = "Altenmarkt an der Triesting";
$plzmapping[2625] = "Schwarzau am Steinfeld";
$plzmapping[2631] = "Ternitz";
$plzmapping[2642] = "Schottwien";
$plzmapping[2654] = "Reichenau an der Rax";
$plzmapping[2661] = "Schwarzau im Gebirge";
$plzmapping[2671] = "Payerbach";
$plzmapping[2721] = "Bad Fischau-Brunn";
$plzmapping[2722] = "Winzendorf-Muthmannsdorf";
$plzmapping[2723] = "Winzendorf-Muthmannsdorf";
$plzmapping[2724] = "Hohe Wand";
$plzmapping[2751] = "Wöllersdorf-Steinabrückl";
$plzmapping[2752] = "Wöllersdorf-Steinabrückl";
$plzmapping[2755] = "Waldegg";
$plzmapping[2832] = "Scheiblingkirchen-Thernberg";
$plzmapping[2852] = "Hochneukirchen-Gschaidt";
$plzmapping[2870] = "Aspang-Markt";

$plzmapping[3004] = "Sieghartskirchen";
$plzmapping[3011] = "Tullnerbach";
$plzmapping[3013] = "Tullnerbach";
$plzmapping[3031] = "Pressbaum";
$plzmapping[3034] = "Maria-Anzbach";
$plzmapping[3051] = "Neulengbach";
$plzmapping[3052] = "Neustift-Innermanzing";
$plzmapping[3053] = "Brand-Laaben";
$plzmapping[3061] = "Neulengbach";
$plzmapping[3101] = "St. Pölten";
$plzmapping[3104] = "St. Pölten";
$plzmapping[3105] = "St. Pölten";
$plzmapping[3106] = "St. Pölten";
$plzmapping[3107] = "St. Pölten";
$plzmapping[3108] = "St. Pölten";
$plzmapping[3122] = "Dunkelsteinerwald";
$plzmapping[3123] = "Obritzberg-Rust";
$plzmapping[3124] = "Wölbling";
$plzmapping[3131] = "Inzersdorf-Getzersdorf";
$plzmapping[3134] = "Nußdorf ob der Traisen";
$plzmapping[3140] = "St. Pölten";
$plzmapping[3142] = "Weißenkirchen an der Perschling";
$plzmapping[3144] = "Pyhra";
$plzmapping[3151] = "St. Pölten";
$plzmapping[3162] = "St. Veit an der Gölsen";
$plzmapping[3163] = "Rohrbach an der Gölsen";
$plzmapping[3171] = "Kleinzell";
$plzmapping[3182] = "Lilienfeld";
$plzmapping[3183] = "Türnitz";
$plzmapping[3195] = "St. Aegyd am Neuwalde";
$plzmapping[3202] = "Hofstetten-Grünau";
$plzmapping[3221] = "Puchenstuben";
$plzmapping[3223] = "Annaberg";
$plzmapping[3224] = "Mitterbach am Erlaufsee";
$plzmapping[3242] = "Texingtal";
$plzmapping[3251] = "Purgstall an der Erlauf";
$plzmapping[3291] = "Gaming";
$plzmapping[3294] = "Gaming";
$plzmapping[3295] = "Gaming";
$plzmapping[3312] = "Hafnerbach";
$plzmapping[3313] = "Wallsee-Sindelburg";
$plzmapping[3331] = "Kematen an der Ybbs";
$plzmapping[3332] = "Sonntagberg";
$plzmapping[3333] = "Sonntagberg";
$plzmapping[3361] = "Aschbach-Markt";
$plzmapping[3362] = "Oed-Oehling";
$plzmapping[3363] = "Amstetten";
$plzmapping[3366] = "Allhartsberg";
$plzmapping[3373] = "Neumarkt an der Ybbs";
$plzmapping[3374] = "Ybbs an der Donau";
$plzmapping[3381] = "Golling an der Erlauf";
$plzmapping[3384] = "Haunoldstein";
$plzmapping[3387] = "Haunoldstein";
$plzmapping[3392] = "Schönbühel-Aggsbach";
$plzmapping[3393] = "Zelking-Matzleinsdorf";
$plzmapping[3413] = "St. Andrä-Wördern";
$plzmapping[3420] = "Klosterneuburg";
$plzmapping[3421] = "Klosterneuburg";
$plzmapping[3422] = "St. Andrä-Wördern";
$plzmapping[3424] = "Zeiselmauer-Wolfpassing";
$plzmapping[3425] = "Tulln an der Donau";
$plzmapping[3441] = "Judenau-Baumgarten";
$plzmapping[3455] = "Atzenbrugg";
$plzmapping[3472] = "Hohenwarth-Mühlbach am Manhartsberg";
$plzmapping[3473] = "Hohenwarth-Mühlbach am Manhartsberg";
$plzmapping[3474] = "Kirchberg am Wagram";
$plzmapping[3482] = "Fels am Wagram";
$plzmapping[3483] = "Grafenwörth";
$plzmapping[3485] = "Grafenegg";
$plzmapping[3492] = "Grafenegg";
$plzmapping[3493] = "Hadersdorf-Kammern";
$plzmapping[3501] = "Krems an der Donau";
$plzmapping[3503] = "Krems an der Donau";
$plzmapping[3504] = "Krems an der Donau";
$plzmapping[3505] = "Krems an der Donau";
$plzmapping[3506] = "Krems an der Donau";
$plzmapping[3512] = "Mautern an der Donau";
$plzmapping[3521] = "Gföhl";
$plzmapping[3522] = "Lichtenau im Waldviertel";
$plzmapping[3524] = "Sallingberg";
$plzmapping[3531] = "Waldhausen";
$plzmapping[3533] = "Zwettl-Niederösterreich";
$plzmapping[3544] = "Krumau am Kamp";
$plzmapping[3553] = "Langenlois";
$plzmapping[3561] = "Langenlois";
$plzmapping[3562] = "Schönberg am Kamp";
$plzmapping[3564] = "Schönberg am Kamp";
$plzmapping[3573] = "Rosenburg-Mold";
$plzmapping[3593] = "Pölla";
$plzmapping[3594] = "Pölla";
$plzmapping[3602] = "Rossatz-Arnsdorf";
$plzmapping[3611] = "Weinzierl am Walde";
$plzmapping[3613] = "Albrechtsberg an der großen Krems";
$plzmapping[3621] = "Rossatz-Arnsdorf";
$plzmapping[3623] = "Kottes-Purk";
$plzmapping[3641] = "Aggsbach";
$plzmapping[3642] = "Schönbühel-Aggsbach";
$plzmapping[3661] = "Artstetten-Pöbring";
$plzmapping[3662] = "Münichreith-Laimbach";
$plzmapping[3663] = "Münichreith-Laimbach";
$plzmapping[3680] = "Persenbeug-Gottsdorf";
$plzmapping[3702] = "Rußbach";
$plzmapping[3704] = "Heldenberg";
$plzmapping[3711] = "Ziersdorf";
$plzmapping[3713] = "Burgschleinitz-Kühnring";
$plzmapping[3721] = "Maissau";
$plzmapping[3722] = "Straning-Grafenberg";
$plzmapping[3742] = "Sigmundsherberg";
$plzmapping[3744] = "Meiseldorf";
$plzmapping[3752] = "Sigmundsherberg";
$plzmapping[3753] = "Geras";
$plzmapping[3754] = "Irnfritz-Messern";
$plzmapping[3761] = "Irnfritz-Messern";
$plzmapping[3762] = "Ludweis-Aigen";
$plzmapping[3811] = "Göpfritz an der Wild";
$plzmapping[3812] = "Groß-Siegharts";
$plzmapping[3814] = "Ludweis-Aigen";
$plzmapping[3823] = "Raabs an der Thaya";
$plzmapping[3824] = "Raabs an der Thaya";
$plzmapping[3834] = "Pfaffenschlag bei Waidhofen a. d. Thaya";
$plzmapping[3871] = "Brand-Nagelberg";
$plzmapping[3872] = "Schrems";
$plzmapping[3873] = "Brand-Nagelberg";
$plzmapping[3910] = "Zwettl-Niederösterreich";
$plzmapping[3923] = "Zwettl-Niederösterreich";
$plzmapping[3924] = "Zwettl-Niederösterreich";
$plzmapping[3944] = "Schrems";
$plzmapping[3962] = "Unserfrau-Altweitra";
$plzmapping[3973] = "Bad Großpertholz";
$plzmapping[4010] = "Linz";
$plzmapping[4016] = "Linz";
$plzmapping[4018] = "Linz";
$plzmapping[4021] = "Linz";
$plzmapping[4024] = "Linz";
$plzmapping[4025] = "Linz";
$plzmapping[4031] = "Linz";
$plzmapping[4033] = "Linz";
$plzmapping[4041] = "Linz";
$plzmapping[4046] = "Linz";
$plzmapping[4053] = "Ansfelden";
$plzmapping[4056] = "Traun";
$plzmapping[4062] = "Kirchberg-Thening";
$plzmapping[4075] = "Scharten";
$plzmapping[4077] = "Alkoven";
$plzmapping[4085] = "Waldkirchen am Wesen";
$plzmapping[4090] = "Engelhartszell an der Donau";
$plzmapping[4112] = "St. Gotthard im Mühlkreis";
$plzmapping[4114] = "St. Martin im Mühlkreis";
$plzmapping[4150] = "Rohrbach-Berg";
$plzmapping[4160] = "Aigen-Schägl";
$plzmapping[4182] = "Oberneukirchen";
$plzmapping[4183] = "Oberneukirchen";
$plzmapping[4225] = "Luftenberg an der Donau";
$plzmapping[4231] = "Wartberg ob der Aist";
$plzmapping[4281] = "Königswiesen";
$plzmapping[4303] = "St. Pantaleon-Erla";
$plzmapping[4331] = "Naarn im Machlande";
$plzmapping[4332] = "Naarn im Machlande";
$plzmapping[4343] = "Mitterkirchen im Machland";
$plzmapping[4364] = "St. Thomas am Blasenstein";
$plzmapping[4382] = "St. Nikola an der Donau";
$plzmapping[4401] = "Steyr";
$plzmapping[4405] = "Steyr";
$plzmapping[4407] = "Steyr";
$plzmapping[4442] = "St. Ulrich bei Steyr";
$plzmapping[4453] = "Ternberg";
$plzmapping[4464] = "Weyer";
$plzmapping[4523] = "Sierning";
$plzmapping[4563] = "Micheldorf in Oberösterreich";
$plzmapping[4571] = "Klaus an der Pyhrnbahn";
$plzmapping[4592] = "Grünburg";
$plzmapping[4593] = "Grünburg";
$plzmapping[4602] = "Wels";
$plzmapping[4613] = "Buchkirchen";
$plzmapping[4616] = "Weißkirchen an der Traun";
$plzmapping[4618] = "Wels";
$plzmapping[4661] = "Roitham";
$plzmapping[4662] = "Laakirchen";
$plzmapping[4664] = "Laakirchen";
$plzmapping[4674] = "Gaspoltshofen";
$plzmapping[4691] = "Schlatt";
$plzmapping[4751] = "Dorf an der Pram";
$plzmapping[4814] = "Altmünster";
$plzmapping[4821] = "Bad Ischl";
$plzmapping[4823] = "Bad Goisern am Hallstättersee";
$plzmapping[4825] = "Gosau";
$plzmapping[4843] = "Ampflwang im Hausruckwald";
$plzmapping[4845] = "Regau";
$plzmapping[4854] = "Steinbach am Attersee";
$plzmapping[4861] = "Schörfling am Attersee";
$plzmapping[4864] = "Attersee am Attersee";
$plzmapping[4866] = "Unterach am Attersee";
$plzmapping[4871] = "Neukirchen an der Vöckla";
$plzmapping[4901] = "Ottnang am Hausruck";
$plzmapping[4905] = "Ottnang am Hausruck";
$plzmapping[4923] = "Lohnsburg am Kobernaußerwald";
$plzmapping[4933] = "Aspach";
$plzmapping[4973] = "St. Martin im Innkreis";
$plzmapping[4985] = "Kirchdorf am Inn";

$plzmapping[5000] = "Wals-Siezenheim";
$plzmapping[5013] = "Salzburg";
$plzmapping[5018] = "Salzburg";
$plzmapping[5021] = "Salzburg";
$plzmapping[5023] = "Salzburg";
$plzmapping[5025] = "Salzburg";
$plzmapping[5026] = "Salzburg";
$plzmapping[5061] = "Elsbethen";
$plzmapping[5071] = "Wals-Siezenheim";
$plzmapping[5072] = "Wals-Siezenheim";
$plzmapping[5083] = "Grödig";
$plzmapping[5089] = "Anif";
$plzmapping[5122] = "Hochburg-Ach";
$plzmapping[5152] = "Dorfbeuern";
$plzmapping[5201] = "Seekirchen am Wallersee";
$plzmapping[5211] = "Lengau";
$plzmapping[5212] = "Lengau";
$plzmapping[5261] = "Helpfau-Uttendorf";
$plzmapping[5282] = "Braunau am Inn";
$plzmapping[5311] = "Innerschwand am Mondsee";
$plzmapping[5342] = "St. Gilgen";
$plzmapping[5351] = "Strobl";
$plzmapping[5422] = "Hallein";
$plzmapping[5451] = "Werfen";
$plzmapping[5521] = "Hüttau";
$plzmapping[5523] = "Annaberg-Lungötz";
$plzmapping[5524] = "Annaberg-Lungötz";
$plzmapping[5562] = "Untertauern";
$plzmapping[5645] = "Bad Gastein";
$plzmapping[5662] = "Bruck an der Großglocknerstraße";
$plzmapping[5672] = "Fusch an der Großglocknerstraße";
$plzmapping[5702] = "Zell am See";
$plzmapping[5732] = "Bramberg am Wildkogel";
$plzmapping[5753] = "Saalbach-Hinterglemm";
$plzmapping[5754] = "Saalbach-Hinterglemm";

$plzmapping[6021] = "Innsbruck";
$plzmapping[6033] = "Innsbruck";
$plzmapping[6080] = "Innsbruck";
$plzmapping[6154] = "Schmirn";
$plzmapping[6183] = "Silz";
$plzmapping[6184] = "St. Sigmund im Sellrain";
$plzmapping[6202] = "Buch in Tirol";
$plzmapping[6212] = "Eben am Achensee";
$plzmapping[6213] = "Eben am Achensee";
$plzmapping[6274] = "Aschau im Zillertal";
$plzmapping[6294] = "Tux";
$plzmapping[6295] = "Mayrhofen";
$plzmapping[6311] = "Wildschönau";
$plzmapping[6313] = "Wildschönau";
$plzmapping[6314] = "Wildschönau";
$plzmapping[6361] = "Hopfgarten im Brixental";
$plzmapping[6362] = "Hopfgarten im Brixental";
$plzmapping[6371] = "Aurach bei Kitzbühel";
$plzmapping[6383] = "Kirchdorf in Tirol";
$plzmapping[6404] = "Polling in Tirol";
$plzmapping[6412] = "Telfs";
$plzmapping[6430] = "Haiming";
$plzmapping[6439] = "Mils bei Imst";
$plzmapping[6452] = "Sölden";
$plzmapping[6456] = "Sölden";
$plzmapping[6458] = "Sölden";
$plzmapping[6493] = "Mils bei Imst";
$plzmapping[6524] = "Kaunertal";
$plzmapping[6562] = "Ischgl";
$plzmapping[6701] = "Bludenz";
$plzmapping[6751] = "Innerbraz";
$plzmapping[6762] = "Klösterle";
$plzmapping[6763] = "Lech";
$plzmapping[6771] = "St. Anton";
$plzmapping[6787] = "St. Gallenkirc";
$plzmapping[6791] = "St. Gallenkirc"; // BUG: St. Gallenkirch
$plzmapping[6794] = "Gaschurn";
$plzmapping[6801] = "Feldkirch";
$plzmapping[6805] = "Feldkirch";
$plzmapping[6807] = "Feldkirch";
$plzmapping[6808] = "Feldkirch";
$plzmapping[6832] = "Röthis";
$plzmapping[6833] = "Klaus";
$plzmapping[6851] = "Dornbirn";
$plzmapping[6854] = "Dornbirn";
$plzmapping[6857] = "Dornbirn";
$plzmapping[6893] = "Lustenau";
$plzmapping[6901] = "Bregenz";
$plzmapping[6904] = "Bregenz";
$plzmapping[6905] = "Bregenz";
$plzmapping[6932] = "Langen";
$plzmapping[6960] = "Wolfurt";
$plzmapping[6961] = "Wolfurt";
$plzmapping[6991] = "Mittelberg";
$plzmapping[6992] = "Mittelberg";

$plzmapping[7001] = "Eisenstadt";
$plzmapping[7023] = "Zemendorf-Stöttera";
$plzmapping[7063] = "Oggau am Neusiedler See";
$plzmapping[7073] = "Rust";
$plzmapping[7091] = "Breitenbrunn";
$plzmapping[7341] = "Markt Sankt Martin"; /* BUG */
$plzmapping[7371] = "Unterrabnitz-Schwendgraben";
$plzmapping[7413] = "Markt Allhau";
$plzmapping[7421] = "Pinggau";
$plzmapping[7443] = "Mannersdorf an der Rabnitz";
$plzmapping[7452] = "Frankenau-Unterpullendorf";
$plzmapping[7474] = "Deutsch Schützen-Eisenberg";
$plzmapping[7542] = "Gerersdorf-Sulz";
$plzmapping[7564] = "Rudersdorf";

$plzmapping[8011] = "Graz";
$plzmapping[8014] = "Kainbach";
$plzmapping[8021] = "Graz";
$plzmapping[8040] = "Graz";
$plzmapping[8041] = "Graz";
$plzmapping[8042] = "Graz";
$plzmapping[8043] = "Graz";
$plzmapping[8044] = "Graz";
$plzmapping[8045] = "Graz";
$plzmapping[8046] = "Graz";
$plzmapping[8047] = "Graz";
$plzmapping[8051] = "Graz";
$plzmapping[8052] = "Graz";
$plzmapping[8053] = "Graz";
$plzmapping[8054] = "Graz";
$plzmapping[8055] = "Graz";
$plzmapping[8057] = "Graz";
$plzmapping[8061] = "St. Radegund";
$plzmapping[8063] = "Eggersdorf";
$plzmapping[8072] = "Fernitz-Mellach";
$plzmapping[8073] = "Feldkirchen";
$plzmapping[8074] = "Raaba-Grambach";
$plzmapping[8075] = "Hart";
$plzmapping[8081] = "Heiligenkreuz";
$plzmapping[8082] = "Kirchbach"; /* BUG "Kirchbach-Zerlach"; */
$plzmapping[8083] = "St. Stefan";
$plzmapping[8092] = "Mettersdorf";
$plzmapping[8093] = "St. Peter";
$plzmapping[8103] = "Gratwein-Straßengel";
$plzmapping[8111] = "Gratwein-Straßengel";
$plzmapping[8112] = "Gratwein-Straßengel";
$plzmapping[8113] = "St. Oswald";
$plzmapping[8114] = "Deutschfeistritz";
$plzmapping[8122] = "Deutschfeistritz";
$plzmapping[8131] = "Pernegg";
$plzmapping[8132] = "Pernegg";
$plzmapping[8141] = "Unterpremstätten-Zettling";
$plzmapping[8143] = "Dobl-Zwaring";
$plzmapping[8144] = "Haselsdorf-Tobelbad";
$plzmapping[8153] = "Geistthal-Södingberg";
$plzmapping[8163] = "Fladnitz";
$plzmapping[8171] = "St. Kathrein";
$plzmapping[8172] = "Anger";
$plzmapping[8181] = "St. Ruprech"; // BUG: 8181 St. Ruprecht
$plzmapping[8182] = "Puch";
$plzmapping[8191] = "Birkfeld";
$plzmapping[8193] = "Miesenbach";
$plzmapping[8194] = "Birkfeld";
$plzmapping[8212] = "Pischelsdorf";
$plzmapping[8221] = "Feistritztal";
$plzmapping[8222] = "Feistritztal";
$plzmapping[8223] = "Stubenberg";
$plzmapping[8224] = "Kaindorf";
$plzmapping[8225] = "Pöllau";
$plzmapping[8226] = "Pöllau";
$plzmapping[8232] = "Grafendorf";
$plzmapping[8234] = "Rohrbach";
$plzmapping[8242] = "St. Lorenzen";
$plzmapping[8251] = "St. Lorenzen";
$plzmapping[8252] = "Waldbach-Mönichwald";
$plzmapping[8253] = "Waldbach-Mönichwald";
$plzmapping[8264] = "Großwilfersdorf";
$plzmapping[8271] = "Waltersdorf";
$plzmapping[8272] = "Waltersdorf";
$plzmapping[8274] = "Buch-St. Magdalena";
$plzmapping[8282] = "Loipersdorf";
$plzmapping[8283] = "Blumau";
$plzmapping[8293] = "Rohr";
$plzmapping[8294] = "Rohr";
$plzmapping[8295] = "St. Johann";
$plzmapping[8302] = "Nestelbach";
$plzmapping[8311] = "Hartmannsdorf";
$plzmapping[8312] = "Ottendorf";
$plzmapping[8313] = "Riegersburg";
$plzmapping[8321] = "St. Margarethen";
$plzmapping[8322] = "Kirchberg";
$plzmapping[8323] = "St. Marein";
$plzmapping[8324] = "Kirchberg";
$plzmapping[8331] = "Feldbach";
$plzmapping[8332] = "Edelsbach";
$plzmapping[8334] = "Riegersburg";
$plzmapping[8335] = "Feldbach";
$plzmapping[8343] = "Gleichenberg";
$plzmapping[8344] = "Gleichenberg";
$plzmapping[8354] = "St. Anna";
$plzmapping[8361] = "Fehring";
$plzmapping[8363] = "Fürstenfeld";
$plzmapping[8401] = "Kalsdorf";
$plzmapping[8403] = "Lebring-St. Margarethen";
$plzmapping[8404] = "Kalsdorf";
$plzmapping[8412] = "Allerheiligen";
$plzmapping[8413] = "St. Georgen";
$plzmapping[8421] = "Schwarzautal";
$plzmapping[8422] = "St. Veit";
$plzmapping[8423] = "St. Veit";
$plzmapping[8432] = "Leibnitz";
$plzmapping[8441] = "Kitzeck";
$plzmapping[8442] = "Kitzeck";
$plzmapping[8444] = "St. Andrä-Höch";
$plzmapping[8453] = "St. Johann";
$plzmapping[8471] = "Straß-Spielfeld";
$plzmapping[8472] = "Straß-Spielfeld";
$plzmapping[8473] = "Straß-Spielfeld";
$plzmapping[8481] = "St. Veit";
$plzmapping[8482] = "Mureck";
$plzmapping[8484] = "Halbenrain";
$plzmapping[8490] = "Radkersburg";
$plzmapping[8503] = "St. Josef";
$plzmapping[8505] = "St. Nikolai";
$plzmapping[8506] = "Stainz";
$plzmapping[8511] = "St. Stefan";
$plzmapping[8522] = "St. Florian";
$plzmapping[8523] = "Frauental";
$plzmapping[8524] = "Deutschlandsberg";
$plzmapping[8542] = "St. Peter";
$plzmapping[8543] = "St. Martin";
$plzmapping[8553] = "Eibiswald";
$plzmapping[8554] = "Eibiswald";
$plzmapping[8555] = "Wies";
$plzmapping[8561] = "Söding-St. Johann";
$plzmapping[8565] = "Söding-St. Johann";
$plzmapping[8573] = "Kainach";
$plzmapping[8582] = "Rosental";
$plzmapping[8584] = "Hirschegg-Pack";
$plzmapping[8592] = "Maria Lankowitz";
$plzmapping[8593] = "Köflach";
$plzmapping[8600] = "Bruck";
$plzmapping[8611] = "Tragöß-St. Katharein";
$plzmapping[8612] = "Tragöß-St. Katharein";
$plzmapping[8622] = "Thörl";
$plzmapping[8623] = "Aflenz";
$plzmapping[8624] = "Turnau";
$plzmapping[8632] = "Mariazell";
$plzmapping[8634] = "Mariazell";
$plzmapping[8635] = "Mariazell";
$plzmapping[8636] = "Turnau";
$plzmapping[8641] = "St. Marein";
$plzmapping[8642] = "St. Marein";
$plzmapping[8643] = "Kindberg";
$plzmapping[8644] = "Kindberg";
$plzmapping[8652] = "Kindberg";
$plzmapping[8653] = "Stanz";
$plzmapping[8661] = "St. Barbara";
$plzmapping[8662] = "St. Barbara";
$plzmapping[8663] = "St. Barbara";
$plzmapping[8664] = "St. Barbara";
$plzmapping[8671] = "Krieglach";
$plzmapping[8672] = "St. Kathrein";
$plzmapping[8682] = "Mürzzuschlag";
$plzmapping[8684] = "Spita"; // BUG: Spital am Semmering
$plzmapping[8685] = "Spita";
$plzmapping[8691] = "Neuberg";
$plzmapping[8692] = "Neuberg";
$plzmapping[8693] = "Neuberg";
$plzmapping[8694] = "Neuberg";
$plzmapping[8703] = "Leoben";
$plzmapping[8709] = "Leoben";
$plzmapping[8713] = "St. Stefan";
$plzmapping[8714] = "Kraubath";
$plzmapping[8715] = "St. Margarethen";
$plzmapping[8731] = "Gaal";
$plzmapping[8733] = "St. Marein-Feistritz"; 
$plzmapping[8741] = "Weißkirchen";
$plzmapping[8743] = "Weißkirchen";
$plzmapping[8751] = "Judenburg";
$plzmapping[8754] = "Pöls-Oberkurzheim";
$plzmapping[8761] = "Pöls-Oberkurzheim";
$plzmapping[8762] = "Pölstal";
$plzmapping[8763] = "Pölstal";
$plzmapping[8765] = "Pölstal";
$plzmapping[8766] = "Pölstal";
$plzmapping[8770] = "St. Michael";
$plzmapping[8772] = "Traboch";
$plzmapping[8773] = "Kammern";
$plzmapping[8774] = "Mautern";
$plzmapping[8781] = "Wald";
$plzmapping[8782] = "Gaishorn";
$plzmapping[8783] = "Gaishorn";
$plzmapping[8800] = "Unzmarkt-Frauenburg";
$plzmapping[8812] = "Neumarkt";
$plzmapping[8820] = "Neumarkt";
$plzmapping[8833] = "Teufenbach-Katsch";
$plzmapping[8841] = "Teufenbach-Katsch";
$plzmapping[8842] = "Teufenbach-Katsch"; 
$plzmapping[8843] = "St. Peter";
$plzmapping[8852] = "Murau";
$plzmapping[8854] = "Krakau";
$plzmapping[8861] = "St. Georgen";
$plzmapping[8862] = "Stadl-Predlitz";
$plzmapping[8863] = "Stadl-Predlitz";
$plzmapping[8864] = "Stadl-Predlitz";
$plzmapping[8912] = "Admont";
$plzmapping[8913] = "Admont";
$plzmapping[8920] = "Landl";
$plzmapping[8921] = "Landl";
$plzmapping[8922] = "Landl";
$plzmapping[8923] = "Landl";
$plzmapping[8932] = "St. Gallen";
$plzmapping[8934] = "Altenmark"; // BUG: "Altenmarkt";
$plzmapping[8941] = "Liezen";
$plzmapping[8943] = "Aigen";
$plzmapping[8950] = "Stainach-Pürgg";
$plzmapping[8951] = "Stainach-Pürgg";
$plzmapping[8952] = "Irdning-Donnersbachtal";
$plzmapping[8953] = "Irdning-Donnersbachtal";
$plzmapping[8954] = "Mitterberg-St. Martin";
$plzmapping[8961] = "Sölk";
$plzmapping[8963] = "Sölk";
$plzmapping[8965] = "Michaelerberg-Pruggern";
$plzmapping[8966] = "Aich";
$plzmapping[8971] = "Schladming";
$plzmapping[8973] = "Schladming";
$plzmapping[8974] = "Schladming";
$plzmapping[8982] = "Mitterndorf";
$plzmapping[8983] = "Mitterndorf";
$plzmapping[8984] = "Mitterndorf";
$plzmapping[8990] = "Aussee";

$plzmapping[9021] = "Klagenfurt am Wörthersee";
$plzmapping[9033] = "Klagenfurt am Wörthersee";
$plzmapping[9034] = "Klagenfurt am Wörthersee";
$plzmapping[9035] = "Klagenfurt am Wörthersee";
$plzmapping[9061] = "Klagenfurt am Wörthersee";
$plzmapping[9064] = "Magdalensberg";
$plzmapping[9073] = "Klagenfurt am Wörthersee";
$plzmapping[9074] = "Keutschach am See";
$plzmapping[9081] = "Völkermarkt";
$plzmapping[9102] = "Völkermarkt";
$plzmapping[9104] = "Griffen";
$plzmapping[9111] = "Völkermarkt";
$plzmapping[9121] = "Völkermarkt";
$plzmapping[9122] = "St. Kanzian am Klopeinersee";
$plzmapping[9123] = "St. Kanzian am Klopeinersee";
$plzmapping[9125] = "Eberndorf";
$plzmapping[9135] = "Eisenkappel-Vellach";
$plzmapping[9143] = "Feistritz ob Bleiburg";
$plzmapping[9162] = "Ferlach"; 
$plzmapping[9163] = "Ferlach"; 
$plzmapping[9172] = "Zell";
$plzmapping[9182] = "St. Jakob im Rosental";
$plzmapping[9183] = "St. Jakob im Rosental";
$plzmapping[9201] = "Krumpendorf am Wörthersee";
$plzmapping[9210] = "Pörtschach am Wörthersee";
$plzmapping[9212] = "Techelsberg am Wörthersee";
$plzmapping[9220] = "Velden am Wörthersee";
$plzmapping[9231] = "Velden am Wörthersee";
$plzmapping[9311] = "Frauenstein";
$plzmapping[9312] = "Mölbling";
$plzmapping[9313] = "St. Georgen am Längssee"; // BUG: St. Georgen am Längsee
$plzmapping[9314] = "St. Georgen am Längssee";
$plzmapping[9323] = "Neumarkt";
$plzmapping[9324] = "Kappel am Krappfeld";
$plzmapping[9330] = "Althofen";
$plzmapping[9335] = "Hüttenberg";
$plzmapping[9343] = "Weitensfeld im Gurktal";
$plzmapping[9344] = "Weitensfeld im Gurktal";
$plzmapping[9345] = "Glödnitz";
$plzmapping[9361] = "Friesach";
$plzmapping[9362] = "Metnitz"; 
$plzmapping[9373] = "Klein Sankt Paul"; //BUG: Klein St. Paul
$plzmapping[9374] = "Klein Sankt Paul";
$plzmapping[9376] = "Hüttenberg";
$plzmapping[9402] = "Wolfsberg";
$plzmapping[9411] = "Wolfsberg";
$plzmapping[9412] = "Wolfsberg";
$plzmapping[9413] = "Frantschach-St. Gertraud";
$plzmapping[9421] = "St. Andrä";
$plzmapping[9422] = "St. Andrä";
$plzmapping[9423] = "St. Georgen im Lavanttal";
$plzmapping[9431] = "Wolfsberg";
$plzmapping[9441] = "Bad Sankt Leonhard im Lavanttal"; // BUG: Bad St. Leonhard im Lavanttal
$plzmapping[9461] = "Wolfsberg";
$plzmapping[9462] = "Bad Sankt Leonhard im Lavanttal";
$plzmapping[9472] = "Lavamünd";
$plzmapping[9504] = "Villach";
$plzmapping[9509] = "Villach";
$plzmapping[9520] = "Treffen";
$plzmapping[9523] = "Villach";
$plzmapping[9524] = "Villach";
$plzmapping[9531] = "Bad Bleiberg";
$plzmapping[9535] = "Schiefling am See"; // BUG: Schiefling am Wörthersee
$plzmapping[9536] = "Velden am Wörthersee";
$plzmapping[9541] = "Treffen";
$plzmapping[9551] = "Steindorf am Ossiacher See";
$plzmapping[9564] = "Reichenau";
$plzmapping[9565] = "Reichenau";
$plzmapping[9571] = "Albeck";
$plzmapping[9572] = "Deutsch-Griffen";
$plzmapping[9580] = "Villach";
$plzmapping[9581] = "Finkenstein";
$plzmapping[9582] = "Finkenstein";
$plzmapping[9583] = "Finkenstein";
$plzmapping[9585] = "Finkenstein";
$plzmapping[9586] = "Finkenstein";
$plzmapping[9587] = "Arnoldstein";
$plzmapping[9602] = "Arnoldstein";
$plzmapping[9611] = "Nötsch im Gailtal";
$plzmapping[9612] = "Nötsch im Gailtal";
$plzmapping[9613] = "Feistritz a. d. Gail";
$plzmapping[9614] = "St. Stefan im Gailtal";
$plzmapping[9615] = "Hermagor-Pressegger See";
$plzmapping[9620] = "Hermagor-Pressegger See";
$plzmapping[9622] = "Gitschtal";
$plzmapping[9623] = "St. Stefan im Gailtal";
$plzmapping[9624] = "Hermagor-Pressegger See";
$plzmapping[9631] = "Hermagor-Pressegger See";
$plzmapping[9633] = "Kirchbach";
$plzmapping[9634] = "Dellach";
$plzmapping[9642] = "Kötschach-Mauthen";
$plzmapping[9651] = "Kötschach-Mauthen";
$plzmapping[9652] = "Lesachtal";
$plzmapping[9653] = "Lesachtal";
$plzmapping[9654] = "Lesachtal";
$plzmapping[9655] = "Lesachtal";
$plzmapping[9701] = "Spittal an der Drau";
$plzmapping[9710] = "Paternion";
$plzmapping[9713] = "Stockenboi";
$plzmapping[9722] = "Weißenstein";
$plzmapping[9753] = "Kleblach-Lind";
$plzmapping[9762] = "Weissensee";
$plzmapping[9772] = "Dellach im Drautal";
$plzmapping[9812] = "Lurnfeld";
$plzmapping[9813] = "Lurnfeld";
$plzmapping[9815] = "Reißeck";
$plzmapping[9816] = "Reißeck";
$plzmapping[9851] = "Seeboden";
$plzmapping[9861] = "Krems in Kärnten";
$plzmapping[9862] = "Krems in Kärnten";
$plzmapping[9863] = "Rennweg am Katschberg";
$plzmapping[9872] = "Millstatt";
$plzmapping[9873] = "Radenthein";
$plzmapping[9953] = "Matrei in Osttirol";
$plzmapping[9974] = "Prägraten am Großvenediger";
$plzmapping[9981] = "Kals am Großglockner";

$plz2gkz = array();
$plz2gkz[2251] = 30812;
$plz2gkz[2532] = 30613;
$plz2gkz[2624] = 31804;
$plz2gkz[2761] = 32321;
$plz2gkz[2831] = 31843;
$plz2gkz[2851] = 32315;
$plz2gkz[3172] = 31409;
$plz2gkz[3622] = 31330;
$plz2gkz[3684] = 31541;
$plz2gkz[3950] = 30908;
$plz2gkz[3971] = 30932;
$plz2gkz[4056] = 41021;
$plz2gkz[4490] = 41013;
$plz2gkz[6771] = 80119;
$plz2gkz[6767] = 80239;
$plz2gkz[6942] = 80221;
$plz2gkz[8061] = 60642;
$plz2gkz[8081] = 61052;
$plz2gkz[8083] = 62381;
$plz2gkz[8082] = 62381;
$plz2gkz[8083] = 62389;
$plz2gkz[8093] = 62388;
$plz2gkz[8113] = 60641;
$plz2gkz[8131] = 62125;
$plz2gkz[8132] = 62125;
$plz2gkz[8171] = 61745;
$plz2gkz[8193] = 61728;
$plz2gkz[8212] = 61764;
$plz2gkz[8242] = 62245;
$plz2gkz[8251] = 62245;
$plz2gkz[8295] = 62244;
$plz2gkz[8321] = 61746;
$plz2gkz[8323] = 60668;
$plz2gkz[8413] = 61055;
$plz2gkz[8453] = 61032;
$plz2gkz[8511] = 60348;
$plz2gkz[8522] = 60346;
$plz2gkz[8542] = 60329;
$plz2gkz[8543] = 60347;
$plz2gkz[8614] = 62105;
$plz2gkz[8641] = 62146;
$plz2gkz[8642] = 62146;
$plz2gkz[8672] = 61744;
$plz2gkz[8713] = 61115;
$plz2gkz[8715] = 62046;
$plz2gkz[8843] = 61425;
$plz2gkz[8861] = 61442;
$plz2gkz[8972] = 61236;
$plz2gkz[9065] = 20402;
$plz2gkz[9423] = 20914;
$plz2gkz[9531] = 20402;
$plz2gkz[9632] = 20306;
$plz2gkz[9633] = 20306;
$plz2gkz[9814] = 20624;
$plz2gkz[9853] = 20608;

// Add GemeindeData for not otherwise not recognized ones.
$gemAdd = array();
// either add plz and vorwahl as array, then array value will be taken as valid for description, or just as string, then key will be taken
// $gemAdd['Seebenstein'] = ['vorwahl' => '2627'];
// $gemAdd['Hernstein'] = ['plz' => '2560', 'vorwahl' => ['2633' => 'Hernstein, Aigen, Alkersdorf', '2672' => 'Neusiedl, Grillenberg, Kleinfeld, Pöllau']];
$gemAdd['Bad Erlach'] = ['vorwahl' => '2627'];
$gemAdd['Bromberg'] = ['vorwahl' => '2629'];
$gemAdd['Hochwolkersdorf'] = ['vorwahl' => '2645'];
$gemAdd['Lanzenkirchen'] = ['vorwahl' => '2627'];
$gemAdd['Hernstein'] = ['plz' => '2560', 'vorwahl' => ['2633' => 'Hernstein, Aigen, Alkersdorf', '2672' => 'Neusiedl, Grillenberg, Kleinfeld, Pöllau']];
$gemAdd['Moosbrunn'] = ['plz' => '2440', 'vorwahl' => '2234'];
$gemAdd['Natschbach-Loipersbach'] = ['vorwahl' => '2635'];
$gemAdd['Scheiblingkirchen-Thernberg'] = ['vorwahl' => '2629'];
$gemAdd['Seebenstein'] = ['vorwahl' => '2627'];
$gemAdd['Schwarzau am Steinfeld'] = ['vorwahl' => '2627'];
$gemAdd['Walpersbach'] = ['vorwahl' => '2627'];
$gemAdd['Breitenbrunn'] = ['vorwahl' => '2683'];
$gemAdd['Donnerskirchen'] = ['vorwahl' => '2683'];
$gemAdd['Großhöflein'] = ['vorwahl' => '2682'];
$gemAdd['Klingenbach'] = ['vorwahl' => '2687'];
$gemAdd['Mörbisch am See'] = ['vorwahl' => '2685'];
$gemAdd['Müllendorf'] = ['vorwahl' => '2682'];
$gemAdd['Neufeld an der Leitha'] = ['vorwahl' => '2624'];
$gemAdd['Oggau am Neusiedler See'] = ['vorwahl' => '2685'];
$gemAdd['Oslip'] = ['vorwahl' => '2684'];
$gemAdd['Trausdorf an der Wulka'] = ['vorwahl' => '2682'];
$gemAdd['Wimpassing an der Leitha'] = ['plz' => '2485', 'vorwahl' => '2623'];
$gemAdd['Wulkaprodersdorf'] = ['vorwahl' => '2687'];
$gemAdd['Loretto'] = ['plz' => '2443', 'vorwahl' => '2255'];
$gemAdd['Stotzing'] = ['plz' => '2443', 'vorwahl' => '2255'];
$gemAdd['Zillingtal'] = ['vorwahl' => '2688'];
$gemAdd['Zagersdorf'] = ['vorwahl' => '2687'];
$gemAdd['Bocksdorf'] = ['plz' => '7553', 'vorwahl' => '3326'];
$gemAdd['Burgauberg-Neudauberg'] = ['vorwahl' => '3326'];
$gemAdd['Gerersdorf-Sulz'] = ['vorwahl' => '3328'];
$gemAdd['Güttenbach'] = ['vorwahl' => '3327'];
$gemAdd['Heiligenbrunn'] = ['plz' => '7522', 'vorwahl' => '3324'];
$gemAdd['Neuberg im Burgenland'] = ['vorwahl' => '3327'];
$gemAdd['Neustift bei Güssing'] = ['plz' => '7545', 'vorwahl' => '3325'];
$gemAdd['Olbendorf'] = ['vorwahl' => '3326'];
$gemAdd['Ollersdorf im Burgenland'] = ['vorwahl' => '3326'];
$gemAdd['Stinatz'] = ['vorwahl' => '3358'];
$gemAdd['Tobaj'] = ['vorwahl' => '3322'];
$gemAdd['Hackerberg'] = ['plz' => '8292', 'vorwahl' => '3358'];
$gemAdd['Wörterberg'] = ['plz' => '7550', 'vorwahl' => '3358'];
$gemAdd['Großmürbisch'] = ['plz' => '7540', 'vorwahl' => '3322'];
$gemAdd['Inzenhof'] = ['plz' => '7540', 'vorwahl' => '3322'];
$gemAdd['Kleinmürbisch'] = ['plz' => '7540', 'vorwahl' => '3322'];
$gemAdd['Tschanigraben'] = ['plz' => '7540', 'vorwahl' => '3322'];
$gemAdd['Heugraben'] = ['plz' => '7551', 'vorwahl' => '3326'];
$gemAdd['Rohr im Burgenland'] = ['plz' => '7554', 'vorwahl' => '3326'];
$gemAdd['Bildein'] = ['plz' => '7521', 'vorwahl' => '3323'];
$gemAdd['Rauchwart'] = ['plz' => ['7534' => 'Olbendorf', '7535' => 'St. Michael im Burgenland'], 'vorwahl' => '3327'];
$gemAdd['Moschendorf'] = ['plz' => '7546', 'vorwahl' => '3324'];
$gemAdd['Deutsch Kaltenbrunn'] = ['vorwahl' => '3382'];
$gemAdd['Eltendorf'] = ['vorwahl' => '3325'];
$gemAdd['Minihof-Liebau'] = ['vorwahl' => '3329'];
$gemAdd['Mogersdorf'] = ['vorwahl' => '3325'];
$gemAdd['Neuhaus am Klausenbach'] = ['vorwahl' => '3329'];
$gemAdd['Rudersdorf'] = ['vorwahl' => '3382'];
$gemAdd['St. Martin an der Raab'] = ['vorwahl' => '3329'];
$gemAdd['Weichselbaum'] = ['plz' => '8382', 'vorwahl' => '3329'];
$gemAdd['Königsdorf'] = ['vorwahl' => '3325'];
$gemAdd['Mühlgraben'] = ['plz' => '8385', 'vorwahl' => '3329'];
$gemAdd['Forchtenstein'] = ['vorwahl' => '2626'];
$gemAdd['Hirm'] = ['vorwahl' => '2687'];
$gemAdd['Loipersbach im Burgenland'] = ['vorwahl' => '2686'];
$gemAdd['Marz'] = ['vorwahl' => '2626'];
$gemAdd['Neudörfl'] = ['vorwahl' => '2622'];
$gemAdd['Pöttelsdorf'] = ['plz' => '7025', 'vorwahl' => '2626'];
$gemAdd['Rohrbach bei Mattersburg'] = ['vorwahl' => '2626'];
$gemAdd['Schattendorf'] = ['vorwahl' => '2686'];
$gemAdd['Sigleß'] = ['vorwahl' => '2626'];
$gemAdd['Wiesen'] = ['vorwahl' => '2626'];
$gemAdd['Antau'] = ['vorwahl' => '2687'];
$gemAdd['Baumgarten'] = ['plz' => '7021', 'vorwahl' => '2686'];
$gemAdd['Zemendorf-Stöttera'] = ['vorwahl' => '2626'];
$gemAdd['Krensdorf'] = ['vorwahl' => '2626'];
$gemAdd['Andau'] = ['vorwahl' => '2176'];
$gemAdd['Bruckneudorf'] = ['plz' => ['2460' => 'Bruck an der Leitha', '2462' => 'Wilfleinsdorf'], 'vorwahl' => '2162'];
$gemAdd['Halbturn'] = ['vorwahl' => '2172'];
$gemAdd['Illmitz'] = ['vorwahl' => '2175'];
$gemAdd['Mönchhof'] = ['vorwahl' => '2173'];
$gemAdd['Pama'] = ['vorwahl' => '2142'];
$gemAdd['Pamhagen'] = ['vorwahl' => '2174'];
$gemAdd['St. Andrä am Zicksee'] = ['vorwahl' => '2176'];
$gemAdd['Weiden am See'] = ['vorwahl' => '2167'];
$gemAdd['Winden am See'] = ['vorwahl' => '2160'];
$gemAdd['Neudorf'] = ['vorwahl' => '2142'];
$gemAdd['Potzneusiedl'] = ['vorwahl' => '2145'];
$gemAdd['Edelstal'] = ['plz' => '2413', 'vorwahl' => '2145'];
$gemAdd['Frankenau-Unterpullendorf'] = ['vorwahl' => ['2615' => 'Lutzmannsburg', '2612' => 'Oberpullendorf']];
$gemAdd['Kaisersdorf'] = ['vorwahl' => '2617'];
$gemAdd['Kobersdorf'] = ['vorwahl' => '2618'];
$gemAdd['Lackenbach'] = ['vorwahl' => '2619'];
$gemAdd['Neckenmarkt'] = ['vorwahl' => '2610'];
$gemAdd['Neutal'] = ['vorwahl' => '2618'];
$gemAdd['Nikitsch'] = ['vorwahl' => '2614'];
$gemAdd['Pilgersdorf'] = ['vorwahl' => '2616'];
$gemAdd['Piringsdorf'] = ['vorwahl' => '2616'];
$gemAdd['Raiding'] = ['plz' => '7321', 'vorwahl' => '2619'];
$gemAdd['Ritzing'] = ['vorwahl' => '2619'];
$gemAdd['Steinberg-Dörfl'] = ['vorwahl' => '2612'];
$gemAdd['Stoob'] = ['vorwahl' => '2612'];
$gemAdd['Weppersdorf'] = ['vorwahl' => '2618'];
$gemAdd['Unterfrauenhaid'] = ['plz' => '7321', 'vorwahl' => '2619'];
$gemAdd['Unterrabnitz-Schwendgraben'] = ['vorwahl' => '2616'];
$gemAdd['Weingraben'] = ['vorwahl' => '2617'];
$gemAdd['Oberloisdorf'] = ['vorwahl' => '2619'];
$gemAdd['Bad Tatzmannsdorf'] = ['vorwahl' => '3353'];
$gemAdd['Grafenschachen'] = ['vorwahl' => '3359'];
$gemAdd['Kemeten'] = ['vorwahl' => '3352'];
$gemAdd['Mariasdorf'] = ['vorwahl' => '3353'];
$gemAdd['Markt Neuhodis'] = ['vorwahl' => '3363'];
$gemAdd['Mischendorf'] = ['vorwahl' => ['3366' => 'Kohfidisch', '3362' => 'Großpetersdorf']];
$gemAdd['Oberdorf im Burgenland'] = ['plz' => '7501', 'vorwahl' => '3352'];
$gemAdd['Riedlingsdorf'] = ['vorwahl' => '3357'];
$gemAdd['Rotenturm an der Pinka'] = ['vorwahl' => '3352'];
$gemAdd['Schachendorf'] = ['vorwahl' => '3364'];
$gemAdd['Unterkohlstätten'] = ['vorwahl' => '3354'];
$gemAdd['Unterwart'] = ['vorwahl' => '3352'];
$gemAdd['Weiden bei Rechnitz'] = ['vorwahl' => '3355'];
$gemAdd['Wiesfleck'] = ['vorwahl' => '3357'];
$gemAdd['Wolfau'] = ['vorwahl' => '3356'];
$gemAdd['Neustift an der Lafnitz'] = ['plz' => '7420', 'vorwahl' => '3338'];
$gemAdd['Jabing'] = ['vorwahl' => '3362', 'plz' => '7503'];
$gemAdd['Badersdorf'] = ['plz' => '7512', 'vorwahl' => '3366'];
$gemAdd['Schandorf'] = ['plz' => '7472', 'vorwahl' => '3364'];
$gemAdd[20402] = ['vorwahl' => '463'];
$gemAdd['Keutschach am See'] = ['vorwahl' => '4273'];
$gemAdd['Ludmannsdorf'] = ['vorwahl' => '4228'];
$gemAdd['Maria Rain'] = ['vorwahl' => '4227'];
$gemAdd['Moosburg'] = ['vorwahl' => '4272'];
$gemAdd['Schiefling am See'] = ['vorwahl' => '4274'];
$gemAdd['Techelsberg am Wörthersee'] = ['vorwahl' => '4272'];
$gemAdd['Zell'] = ['vorwahl' => '4227'];
$gemAdd['Magdalensberg'] = ['vorwahl' => '4224'];
$gemAdd['Deutsch-Griffen'] = ['vorwahl' => '4279'];
$gemAdd['Eberstein'] = ['vorwahl' => '4264'];
$gemAdd['Gurk'] = ['vorwahl' => '4266'];
$gemAdd['Guttaring'] = ['vorwahl' => '4262'];
$gemAdd['Kappel am Krappfeld'] = ['vorwahl' => '4262'];
$gemAdd['Micheldorf'] = ['vorwahl' => '4268'];
$gemAdd['Mölbling'] = ['vorwahl' => '4262'];
$gemAdd['Frauenstein'] = ['vorwahl' => '4212'];
$gemAdd['Baldramsdorf'] = ['vorwahl' => '4762'];
$gemAdd['Berg im Drautal'] = ['vorwahl' => '4712'];
$gemAdd['Irschen'] = ['vorwahl' => '4710'];
$gemAdd['Lendorf'] = ['vorwahl' => '4762'];
$gemAdd[20624] = ['vorwahl' => '4769'];
$gemAdd['Sachsenburg'] = ['vorwahl' => '4769'];
$gemAdd['Seeboden'] = ['vorwahl' => '4762'];
$gemAdd['Stall'] = ['vorwahl' => '4823'];
$gemAdd['Trebesing'] = ['vorwahl' => '4732'];
$gemAdd['Arriach'] = ['vorwahl' => '4247'];
$gemAdd['Feistritz a. d. Gail'] = ['vorwahl' => '4256'];
$gemAdd['Feld am See'] = ['vorwahl' => '4246'];
$gemAdd['Ferndorf'] = ['vorwahl' => '4245'];
$gemAdd['Fresach'] = ['vorwahl' => '4245'];
$gemAdd['Hohenthurn'] = ['vorwahl' => '4256'];
$gemAdd['Rosegg'] = ['vorwahl' => ['4253' => 'Rosegg', '4274' => 'Rosegg']];
$gemAdd['Hohenthurn'] = ['vorwahl' => '4256'];
$gemAdd['Diex'] = ['vorwahl' => '4231'];
$gemAdd['Hohenthurn'] = ['vorwahl' => '4256', 'plz' => '9613'];
$gemAdd['Feistritz ob Bleiburg'] = ['vorwahl' => '4235'];
$gemAdd['Neuhaus'] = ['vorwahl' => '4356'];
$gemAdd['Frantschach-St. Gertraud'] = ['vorwahl' => '4352'];
$gemAdd['St. Georgen im Lavanttal'] = ['vorwahl' => '4357'];
$gemAdd['Himmelberg'] = ['vorwahl' => '4276'];
$gemAdd['Ossiach'] = ['vorwahl' => '4243'];
$gemAdd['St. Urban'] = ['vorwahl' => '4277'];
$gemAdd['Steuerberg'] = ['vorwahl' => '4271', 'plz' => '9560'];
$gemAdd['Allhartsberg'] = ['vorwahl' => '7448'];
$gemAdd['Behamberg'] = ['vorwahl' => '7252'];
$gemAdd['Biberbach'] = ['vorwahl' => '7476'];
$gemAdd['Ennsdorf'] = ['vorwahl' => '7223'];
$gemAdd['Ernsthofen'] = ['vorwahl' => '7435'];
$gemAdd['Ertl'] = ['vorwahl' => '7477'];
$gemAdd['Ferschnitz'] = ['vorwahl' => '7473'];
$gemAdd['Haidershofen'] = ['vorwahl' => ['7434' => '?', '7252' => '?']];
$gemAdd['Neuhofen an der Ybbs'] = ['vorwahl' => '7475'];
$gemAdd['St. Georgen am Reith'] = ['vorwahl' => '7484'];
$gemAdd['St. Georgen am Ybbsfelde'] = ['vorwahl' => '7473'];
$gemAdd['St. Pantaleon-Erla'] = ['vorwahl' => ['7435' => '?', '7223' => '?']];
$gemAdd['Seitenstetten'] = ['vorwahl' => '7477'];
$gemAdd['Sonntagberg'] = ['vorwahl' => '7448'];
$gemAdd['Viehdorf'] = ['vorwahl' => '7472'];
$gemAdd['Weistrach'] = ['vorwahl' => '7477'];
$gemAdd['Winklarn'] = ['vorwahl' => '7472', 'plz' => ['3300' => '?', '3363' => '?']];
$gemAdd['Wolfsbach'] = ['vorwahl' => '7477'];
$gemAdd['Zeillern'] = ['vorwahl' => '7472'];
$gemAdd['Bad Vöslau'] = ['vorwahl' => '2252'];
$gemAdd['Enzesfeld-Lindabrunn'] = ['vorwahl' => '2256'];
$gemAdd['Furth an der Triesting'] = ['vorwahl' => '2674'];
$gemAdd['Günselsdorf'] = ['vorwahl' => '2256'];
$gemAdd[30613] = ['vorwahl' => '2258'];
$gemAdd['Hirtenberg'] = ['vorwahl' => '2256'];
$gemAdd['Kottingbrunn'] = ['vorwahl' => '2252'];
$gemAdd['Mitterndorf an der Fischa'] = ['vorwahl' => '2234'];
$gemAdd['Pfaffstätten'] = ['vorwahl' => '2252'];
$gemAdd['Pottenstein'] = ['vorwahl' => '2672'];
$gemAdd['Furth an der Triesting'] = ['vorwahl' => '2674'];
$gemAdd['Reisenberg'] = ['vorwahl' => '2234', 'plz' => '2440'];
$gemAdd['Schönau an der Triesting'] = ['vorwahl' => '2256'];
$gemAdd['Seibersdorf'] = ['vorwahl' => '2255'];
$gemAdd['Sooß'] = ['vorwahl' => '2252'];
$gemAdd['Furth an der Triesting'] = ['vorwahl' => '2674'];
$gemAdd['Schönau an der Triesting'] = ['vorwahl' => '2256'];
$gemAdd['Tattendorf'] = ['vorwahl' => '2253'];
$gemAdd['Teesdorf'] = ['vorwahl' => '2253'];
$gemAdd['Traiskirchen'] = ['vorwahl' => '2252'];
$gemAdd['Trumau'] = ['vorwahl' => '2253'];
$gemAdd['Blumau-Neurißhof'] = ['vorwahl' => '2628'];
$gemAdd['Au am Leithaberge'] = ['vorwahl' => '2168'];
$gemAdd['Bad Deutsch-Altenburg'] = ['vorwahl' => '2165'];
$gemAdd['Berg'] = ['vorwahl' => '2143'];
$gemAdd['Enzersdorf an der Fischa'] = ['vorwahl' => '2230'];
$gemAdd['Göttlesbrunn-Arbesthal'] = ['vorwahl' => '2162'];
$gemAdd['Götzendorf an der Leitha'] = ['vorwahl' => '2169'];
$gemAdd['Haslau-Maria Ellend'] = ['vorwahl' => '2232'];
$gemAdd['Höflein'] = ['vorwahl' => '2162'];
$gemAdd['Furth an der Triesting'] = ['vorwahl' => '2674', 'plz' => '2564'];
$gemAdd['Schönau an der Triesting'] = ['vorwahl' => '2256', 'plz' => ['2525' => '?', '2544' => '?', '2602' => '?']];
$gemAdd['Au am Leithaberge'] = ['vorwahl' => '2168', 'plz' => '2451'];
$gemAdd['Hof am Leithaberge'] = ['vorwahl' => '2168'];
$gemAdd['Hundsheim'] = ['vorwahl' => '2165', 'plz' => '2405'];
$gemAdd['Scharndorf'] = ['vorwahl' => '2163'];
$gemAdd['Sommerein'] = ['vorwahl' => '2168'];
$gemAdd['Wolfsthal'] = ['vorwahl' => '2165'];
$gemAdd['Ebergassing'] = ['vorwahl' => ['2234' => 'Ebergassing', '2230' => 'Wienerherberg']];
$gemAdd['Himberg'] = ['vorwahl' => ['2235' => 'Himberg', '2234' => 'Velm']];
$gemAdd['Klein-Neusiedl'] = ['vorwahl' => '2230'];
$gemAdd['Lanzendorf'] = ['vorwahl' => '2235', 'plz' => '2326'];
$gemAdd['Leopoldsdorf'] = ['vorwahl' => '2235'];
$gemAdd['Rauchenwarth'] = ['vorwahl' => '2230', 'plz' => '2320'];
$gemAdd['Schwechat'] = ['vorwahl' => '1'];
$gemAdd['Zwölfaxing'] = ['vorwahl' => '1'];
$gemAdd['Aderklaa'] = ['vorwahl' => '2247', 'plz' => '2232'];
$gemAdd['Andlersdorf'] = ['vorwahl' => '2215', 'plz' => '2301'];
$gemAdd['Bad Pirawarth'] = ['vorwahl' => '2574'];
$gemAdd['Dürnkrut'] = ['vorwahl' => '2538'];
$gemAdd[30812] = ['vorwahl' => '2538'];
$gemAdd['Engelhartstetten'] = ['vorwahl' => '2214'];
$gemAdd['Glinzendorf'] = ['vorwahl' => '2248'];
$gemAdd['Großhofen'] = ['vorwahl' => '2248', 'plz' => '2282'];
$gemAdd['Groß-Schweinbarth'] = ['vorwahl' => '2289'];
$gemAdd['Haringsee'] = ['vorwahl' => '2214'];
$gemAdd['Hauskirchen'] = ['vorwahl' => '2533'];
$gemAdd['Hohenruppersdorf'] = ['vorwahl' => '2574'];
$gemAdd['Jedenspeigen'] = ['vorwahl' => '2536'];
$gemAdd['Mannsdorf an der Donau'] = ['vorwahl' => '2212', 'plz' => '2304'];
$gemAdd['Palterndorf-Dobermannsdorf'] = ['vorwahl' => '2533'];
$gemAdd['Parbasdorf'] = ['vorwahl' => '2247', 'plz' => '2232'];;
$gemAdd['Prottes'] = ['vorwahl' => '2282'];
$gemAdd['Raasdorf'] = ['vorwahl' => '2249'];
$gemAdd['Ringelsdorf-Niederabsdorf'] = ['vorwahl' => '2536'];
$gemAdd['Schönkirchen-Reyersdorf'] = ['vorwahl' => '2282'];
$gemAdd['Spannberg'] = ['vorwahl' => '2538'];
$gemAdd['Untersiebenbrunn'] = ['vorwahl' => '2286'];
$gemAdd['Weikendorf'] = ['vorwahl' => ['2282' => '?', '2283' => '?']];
$gemAdd['Amaliendorf-Aalfang'] = ['vorwahl' => '2862', 'plz' => '3872'];
$gemAdd['Eisgarn'] = ['vorwahl' => '2863'];
$gemAdd['Großdietmanns'] = ['vorwahl' => '2852', 'plz' => '3950'];
$gemAdd['Moorbad Harbach'] = ['vorwahl' => '2858', 'plz' => '3970'];
$gemAdd['Haugschlag'] = ['vorwahl' => '2865', 'plz' => '3874'];
$gemAdd['Hirschbach'] = ['vorwahl' => '2854'];
$gemAdd['Hoheneich'] = ['vorwahl' => '2852'];
$gemAdd['Reingers'] = ['vorwahl' => '2863'];
$gemAdd[30932] = ['vorwahl' => '2857'];
$gemAdd['Unserfrau-Altweitra'] = ['vorwahl' => '2856'];
$gemAdd['Alberndorf im Pulkautal'] = ['vorwahl' => '2944', 'plz' => '2054'];;
$gemAdd['Grabern'] = ['vorwahl' => '2952', 'plz' => '2020'];;
$gemAdd['Heldenberg'] = ['vorwahl' => '2956'];
$gemAdd['Mailberg'] = ['vorwahl' => '2943'];
$gemAdd['Pernersdorf'] = ['vorwahl' => '2944'];
$gemAdd['Ravelsbach'] = ['vorwahl' => '2958'];
$gemAdd['Retzbach'] = ['vorwahl' => '2942'];
$gemAdd['Schrattenthal'] = ['vorwahl' => '2942'];
$gemAdd['Seefeld-Kadolz'] = ['vorwahl' => '2943'];
$gemAdd['Wullersdorf'] = ['vorwahl' => '2951'];
$gemAdd['Altenburg'] = ['vorwahl' => '2982'];
$gemAdd['Burgschleinitz-Kühnring'] = ['vorwahl' => '2984'];
$gemAdd['Langau'] = ['vorwahl' => '2912'];
$gemAdd['Meiseldorf'] = ['vorwahl' => '2983'];
$gemAdd[31117] = ['vorwahl' => '2913', 'plz' => '3753'];;
$gemAdd['Röhrenbach'] = ['vorwahl' => '2989'];
$gemAdd['Röschitz'] = ['vorwahl' => '2984'];
$gemAdd['Rosenburg-Mold'] = ['vorwahl' => '2982'];
$gemAdd['St. Bernhard-Frauenhofen'] = ['vorwahl' => '2982', 'plz' => '3580'];;
$gemAdd['Straning-Grafenberg'] = ['vorwahl' => '2984'];
$gemAdd['Bisamberg'] = ['vorwahl' => '2262'];
$gemAdd['Enzersfeld im Weinviertel'] = ['vorwahl' => '2262'];
$gemAdd['Hagenbrunn'] = ['vorwahl' => ['2262' => '?', '2246' => '?'], 'plz' => '2102'];;
$gemAdd['Leitzersdorf'] = ['vorwahl' => '2266'];
$gemAdd['Leobendorf'] = ['vorwahl' => '2262'];
$gemAdd['Rußbach'] = ['vorwahl' => '2955'];
$gemAdd['Spillern'] = ['vorwahl' => '2266'];
$gemAdd['Stetteldorf am Wagram'] = ['vorwahl' => '2278'];
$gemAdd['Stetten'] = ['vorwahl' => '2262', 'plz' => '2100'];
$gemAdd['Bergern im Dunkelsteinerwald'] = ['vorwahl' => '2714', 'plz' => '3512'];
$gemAdd['Grafenegg'] = ['vorwahl' => '2735'];
$gemAdd['Furth bei Göttweig'] = ['vorwahl' => '2732'];
$gemAdd['Gedersdorf'] = ['vorwahl' => '2735'];
$gemAdd['Jaidhof'] = ['vorwahl' => '2716', 'plz' => '3542'];
$gemAdd['Lengenfeld'] = ['vorwahl' => '2719'];
$gemAdd['Maria Laach am Jauerling'] = ['vorwahl' => '2712'];
$gemAdd['Mautern an der Donau'] = ['vorwahl' => '2732'];
$gemAdd[31330] = ['vorwahl' => '2713'];
$gemAdd['Rohrendorf bei Krems'] = ['vorwahl' => '2732'];
$gemAdd['Straß im Straßertale'] = ['vorwahl' => '2735'];
$gemAdd['Stratzing'] = ['vorwahl' => '2719', 'plz' => '3552'];
$gemAdd['Weinzierl am Walde'] = ['vorwahl' => '2717'];
$gemAdd['Droß'] = ['plz' => '3552'];
$gemAdd['Eschenau'] = ['vorwahl' => '2762'];
$gemAdd['Mitterbach am Erlaufsee'] = ['vorwahl' => '3882'];
$gemAdd[31409] = ['vorwahl' => '2764'];
$gemAdd['Rohrbach an der Gölsen'] = ['vorwahl' => '2764'];
$gemAdd['Traisen'] = ['vorwahl' => '2762'];
$gemAdd['Artstetten-Pöbring'] = ['vorwahl' => '7413'];
$gemAdd['Bergland'] = ['vorwahl' => '7416'];
$gemAdd['Bischofstetten'] = ['vorwahl' => '2748'];
$gemAdd['Dorfstetten'] = ['vorwahl' => '7260'];
$gemAdd['Erlauf'] = ['vorwahl' => '2757'];
$gemAdd['Golling an der Erlauf'] = ['vorwahl' => '2757'];
$gemAdd['Hofamt Priel'] = ['vorwahl' => '7412'];
$gemAdd['Hürm'] = ['vorwahl' => '2754'];
$gemAdd['Kirnberg an der Mank'] = ['vorwahl' => '2755'];
$gemAdd['Klein-Pöchlarn'] = ['vorwahl' => '7413'];
$gemAdd['Krummnußbaum'] = ['vorwahl' => '2757'];
$gemAdd['Leiben'] = ['vorwahl' => '2752'];
$gemAdd['Maria Taferl'] = ['vorwahl' => '7413'];
$gemAdd['Münichreith-Laimbach'] = ['vorwahl' => ['7413' => '?', '2758' => '?']];
$gemAdd['Neumarkt an der Ybbs'] = ['vorwahl' => '7412'];
$gemAdd['Persenbeug-Gottsdorf'] = ['vorwahl' => '7412'];
$gemAdd['Petzenkirchen'] = ['vorwahl' => '7416'];
$gemAdd['Raxendorf'] = ['vorwahl' => '2758'];
$gemAdd['Ruprechtshofen'] = ['vorwahl' => '2756'];
$gemAdd['St. Martin-Karlsbach'] = ['vorwahl' => '7412'];
$gemAdd[31541] = ['vorwahl' => '7415'];
$gemAdd['Schönbühel-Aggsbach'] = ['vorwahl' => '2753'];
$gemAdd['Schollach'] = ['vorwahl' => '2754', 'plz' => '3382'];;
$gemAdd['Weiten'] = ['vorwahl' => '2758'];
$gemAdd['Zelking-Matzleinsdorf'] = ['vorwahl' => '2752'];
$gemAdd['Texingtal'] = ['vorwahl' => '2755'];
$gemAdd['Emmersdorf an der Donau'] = ['vorwahl' => '2752'];
$gemAdd['Altlichtenwarth'] = ['vorwahl' => '2533'];
$gemAdd['Bockfließ'] = ['vorwahl' => '2288'];
$gemAdd['Falkenstein'] = ['vorwahl' => '2554'];
$gemAdd['Fallbach'] = ['vorwahl' => '2524'];
$gemAdd['Gaubitsch'] = ['vorwahl' => '2522', 'plz' => '2154'];;
$gemAdd['Großebersdorf'] = ['vorwahl' => '2245'];
$gemAdd['Großengersdorf'] = ['vorwahl' => '2245'];
$gemAdd['Großharras'] = ['vorwahl' => '2526'];
$gemAdd['Hausbrunn'] = ['vorwahl' => '2533'];
$gemAdd['Hochleithen'] = ['vorwahl' => '2245', 'plz' => ['2123' => '?', '2125' => '?']];
$gemAdd['Kreuttal'] = ['vorwahl' => '2245'];
$gemAdd['Kreuzstetten'] = ['vorwahl' => '2263'];
$gemAdd['Niederleis'] = ['vorwahl' => '2576'];
$gemAdd['Pillichsdorf'] = ['vorwahl' => '2245'];
$gemAdd['Rabensburg'] = ['vorwahl' => '2535'];
$gemAdd['Schrattenberg'] = ['vorwahl' => '2555'];
$gemAdd['Ulrichskirchen-Schleinbach'] = ['vorwahl' => '2245'];
$gemAdd['Unterstinkenbrunn'] = ['vorwahl' => '2526'];
$gemAdd['Wildendürnbach'] = ['vorwahl' => '2523'];
$gemAdd['Ottenthal'] = ['vorwahl' => '2554'];
$gemAdd['Achau'] = ['vorwahl' => '2236'];
$gemAdd['Biedermannsdorf'] = ['vorwahl' => '2236'];
$gemAdd['Achau'] = ['vorwahl' => '2236'];
$gemAdd['Brunn am Gebirge'] = ['vorwahl' => '2236'];
$gemAdd['Gießhübl'] = ['vorwahl' => '2236'];
$gemAdd['Gumpoldskirchen'] = ['vorwahl' => '2252'];
$gemAdd['Guntramsdorf'] = ['vorwahl' => '2236'];
$gemAdd['Hennersdorf'] = ['vorwahl' => '2235'];
$gemAdd['Hinterbrühl'] = ['vorwahl' => ['2236' => 'Hinterbrühl', '2237' => 'Sparbach']];
$gemAdd['Laab im Walde'] = ['vorwahl' => '2239'];
$gemAdd['Laxenburg'] = ['vorwahl' => '2236'];
$gemAdd['Maria Enzersdorf'] = ['vorwahl' => '2236'];
$gemAdd['Perchtoldsdorf'] = ['vorwahl' => '1'];
$gemAdd['Vösendorf'] = ['vorwahl' => '1'];
$gemAdd['Wiener Neudorf'] = ['vorwahl' => '2236'];
$gemAdd['Wienerwald'] = ['vorwahl' => ['2238' => 'Wienerwald', '2258' => 'Grub']];
$gemAdd['Altendorf'] = ['vorwahl' => '2662', 'plz' => ['2632' => '?', '2640' => '?']];
$gemAdd['Aspangberg-St. Peter'] = ['vorwahl' => '2642', 'plz' => ['2870' => '?', '2872' => '?', '2873' => '?']];
$gemAdd[31804] = ['vorwahl' => '2635'];
$gemAdd['Breitenstein'] = ['vorwahl' => '2664'];
$gemAdd['Buchbach'] = ['vorwahl' => '2630', 'plz' => ['2630' => '?', '2640' => '?']];
$gemAdd['Edlitz'] = ['vorwahl' => '2644'];
$gemAdd['Enzenreith'] = ['vorwahl' => '2662', 'plz' => ['2632' => '?', '2640' => '?']];
$gemAdd['Feistritz am Wechsel'] = ['vorwahl' => ['2641' => '?', '2644' => '?']];
$gemAdd['Grafenbach-St. Valentin'] = ['vorwahl' => '2630', 'plz' => '2632'];
$gemAdd['Natschbach-Loipersbach'] = ['vorwahl' => '2635', 'plz' => '2620'];
$gemAdd['Otterthal'] = ['vorwahl' => '2641', 'plz' => '2880'];;
$gemAdd['Payerbach'] = ['vorwahl' => '2666'];
$gemAdd['Prigglitz'] = ['vorwahl' => '2662', 'plz' => ['2630' => '?', '2640' => '?']];
$gemAdd['Raach am Hochgebirge'] = ['vorwahl' => '2662', 'plz' => '2640'];;
$gemAdd['St. Corona am Wechsel'] = ['vorwahl' => '2641', 'plz' => ['2870' => '?', '2880' => '?']];
$gemAdd['St. Egyden am Steinfeld'] = ['vorwahl' => '2638'];
$gemAdd['Schrattenbach'] = ['vorwahl' => '2637', 'plz' => '2733'];
$gemAdd['Thomasberg'] = ['vorwahl' => '2644', 'plz' => ['2813' => '?', '2840' => '?', '2842' => '?', '2870' => '?']];
$gemAdd['Trattenbach'] = ['vorwahl' => '2641'];
$gemAdd['Bürg-Vöstenhof'] = ['vorwahl' => '2630', 'plz' => '2630'];
$gemAdd['Wartmannstetten'] = ['vorwahl' => '2635', 'plz' => '2620'];
$gemAdd['Wimpassing im Schwarzatale'] = ['vorwahl' => '2630'];
$gemAdd['Würflach'] = ['vorwahl' => '2620', 'plz' => '2732'];
$gemAdd['Zöbern'] = ['vorwahl' => '2642'];
$gemAdd['Höflein an der Hohen Wand'] = ['vorwahl' => '2620', 'plz' => ['2724' => '?', '2732' => '?', '2733' => '?']];
$gemAdd['Altlengbach'] = ['vorwahl' => '2774'];
$gemAdd['Asperhofen'] = ['vorwahl' => '2772'];
$gemAdd['Brand-Laaben'] = ['vorwahl' => '2774'];
$gemAdd['Gerersdorf'] = ['vorwahl' => '2749', 'plz' => '3385'];
$gemAdd['Hofstetten-Grünau'] = ['vorwahl' => '2723'];
$gemAdd['Hafnerbach'] = ['vorwahl' => '2749'];
$gemAdd['Haunoldstein'] = ['vorwahl' => '2749'];
$gemAdd['Inzersdorf-Getzersdorf'] = ['vorwahl' => '2782'];
$gemAdd['Kapelln'] = ['vorwahl' => '2784'];
$gemAdd['Karlstetten'] = ['vorwahl' => '2741'];
$gemAdd['Kirchstetten'] = ['vorwahl' => '2743'];
$gemAdd['Loich'] = ['vorwahl' => '2722'];
$gemAdd['Maria-Anzbach'] = ['vorwahl' => '2772'];
$gemAdd['Markersdorf-Haindorf'] = ['vorwahl' => '2749', 'plz' => '3388'];
$gemAdd['Michelbach'] = ['vorwahl' => '2744'];
$gemAdd['Nußdorf ob der Traisen'] = ['vorwahl' => '2783'];
$gemAdd['Obritzberg-Rust'] = ['vorwahl' => ['2742' => '?', '2782' => '?', '2786' => '?']];
$gemAdd['St. Margarethen an der Sierning'] = ['vorwahl' => '2747'];
$gemAdd['Statzendorf'] = ['vorwahl' => '2786'];
$gemAdd['Stössing'] = ['vorwahl' => '2744'];
$gemAdd['Weinburg'] = ['vorwahl' => '2747'];
$gemAdd['Gablitz'] = ['vorwahl' => '2231'];
$gemAdd['Mauerbach'] = ['vorwahl' => '1'];
$gemAdd['Tullnerbach'] = ['vorwahl' => '2233'];
$gemAdd['Wolfsgraben'] = ['vorwahl' => '2233'];
$gemAdd['Gresten-Land'] = ['vorwahl' => '7487', 'plz' => '3264'];
$gemAdd['Randegg'] = ['vorwahl' => '7487'];
$gemAdd['Reinsberg'] = ['vorwahl' => '7487', 'plz' => '3264'];
$gemAdd['St. Anton an der Jeßnitz'] = ['vorwahl' => '7482'];
$gemAdd['St. Georgen an der Leys'] = ['vorwahl' => '7482'];
$gemAdd['Wang'] = ['vorwahl' => '7488'];
$gemAdd['Wieselburg-Land'] = ['vorwahl' => '7416', 'plz' => '3250'];
$gemAdd['Wolfpassing'] = ['vorwahl' => '7488', 'plz' => '3261'];
$gemAdd['Grafenwörth'] = ['vorwahl' => '2738'];
$gemAdd['Großriedenthal'] = ['vorwahl' => '2279'];
$gemAdd['Judenau-Baumgarten'] = ['vorwahl' => '2274'];
$gemAdd['Königsbrunn am Wagram'] = ['vorwahl' => '2278'];
$gemAdd['Königstetten'] = ['vorwahl' => '2273'];
$gemAdd['Langenrohr'] = ['vorwahl' => '2272'];
$gemAdd['Michelhausen'] = ['vorwahl' => '2275'];
$gemAdd['Würmla'] = ['vorwahl' => '2275'];
$gemAdd['Zeiselmauer-Wolfpassing'] = ['vorwahl' => '2242'];
$gemAdd['Muckendorf-Wipfing'] = ['vorwahl' => '2242'];
$gemAdd['Dietmanns'] = ['vorwahl' => '2847'];
$gemAdd['Gastern'] = ['vorwahl' => '2864'];
$gemAdd['Ludweis-Aigen'] = ['vorwahl' => '2847'];
$gemAdd['Thaya'] = ['vorwahl' => '2842'];
$gemAdd['Waidhofen an der Thaya-Land'] = ['vorwahl' => ['2842' => '?', '2848' => '?'], 'plz' => '3830'];
$gemAdd['Waldkirchen an der Thaya'] = ['vorwahl' => '2843'];
$gemAdd['Windigsteig'] = ['vorwahl' => '2849'];
$gemAdd['Bad Schönau'] = ['vorwahl' => '2646'];
$gemAdd['Eggendorf'] = ['vorwahl' => ['2622' => 'Eggendorf', '2628' => 'Maria Theresia']];
$gemAdd['Hohe Wand'] = ['vorwahl' => '2638'];
$gemAdd['Hollenthon'] = ['vorwahl' => '2645'];
$gemAdd['Katzelsdorf'] = ['vorwahl' => '2622'];
$gemAdd['Lichtenwörth'] = ['vorwahl' => '2622'];
$gemAdd['Matzendorf-Hölles'] = ['vorwahl' => '2628', 'plz' => ['2751' => '?', '2603' => '?']];
$gemAdd[32321] = ['vorwahl' => '2632'];
$gemAdd['Muggendorf'] = ['vorwahl' => '2632', 'plz' => '2763'];
$gemAdd['Rohr im Gebirge'] = ['vorwahl' => '2667'];
$gemAdd['Schwarzenbach'] = ['vorwahl' => '2645'];
$gemAdd['Sollenau'] = ['vorwahl' => '2628'];
$gemAdd['Theresienfeld'] = ['vorwahl' => '2622'];
$gemAdd['Waidmannsfeld'] = ['vorwahl' => '2632', 'plz' => ['2761' => '?', '2763' => '?']];
$gemAdd['Waldegg'] = ['vorwahl' => '2633'];
$gemAdd['Weikersdorf am Steinfelde'] = ['vorwahl' => '2638', 'plz' => ['2700' => '?', '2722' => '?']];
$gemAdd['Wöllersdorf-Steinabrückl'] = ['vorwahl' => ['2633' => 'Wöllersdorf', '2622' => 'Steinabrückl']];
$gemAdd['Zillingdorf'] = ['vorwahl' => '2622', 'plz' => ['2491' => '?', '2492' => '?']];
$gemAdd['Bärnkopf'] = ['vorwahl' => '2874', 'plz' => ['3633' => '?', '3665' => '?']];
$gemAdd['Echsenbach'] = ['vorwahl' => '2849'];
$gemAdd['Großgöttfritz'] = ['vorwahl' => '2875'];
$gemAdd['Gutenbrunn'] = ['vorwahl' => '2874'];
$gemAdd['Kirchschlag'] = ['vorwahl' => '2872', 'plz' => ['3631' => '?', '3664' => '?']];
$gemAdd['Altmelon'] = ['vorwahl' => '2813', 'plz' => ['3633' => '?', '3925' => '?', '4372' => '?']];
$gemAdd['Waldhausen'] = ['vorwahl' => '2877'];

# Oberösterreich
$gemAdd['Aspach'] = ['vorwahl' => '7755'];
$gemAdd['Auerbach'] = ['vorwahl' => '7747'];
$gemAdd['Burgkirchen'] = ['vorwahl' => '7724'];
$gemAdd['Feldkirchen bei Mattighofen'] = ['vorwahl' => '7748'];
$gemAdd['Franking'] = ['vorwahl' => '6277'];
$gemAdd['Geretsberg'] = ['vorwahl' => '7748'];
$gemAdd['Gilgenberg am Weilhart'] = ['vorwahl' => '7728'];
$gemAdd['Haigermoos'] = ['vorwahl' => '6277'];
$gemAdd['Handenberg'] = ['vorwahl' => '7748'];
$gemAdd['Helpfau-Uttendorf'] = ['vorwahl' => '7724'];
$gemAdd['Höhnhart'] = ['vorwahl' => '7755'];
$gemAdd['Jeging'] = ['vorwahl' => '7744'];
$gemAdd['Mining'] = ['vorwahl' => '7723'];
$gemAdd['Moosbach'] = ['vorwahl' => '7724'];
$gemAdd['Moosdorf'] = ['vorwahl' => '7748'];
$gemAdd['Palting'] = ['vorwahl' => '6217', 'plz' => '5163'];
$gemAdd['Perwang am Grabensee'] = ['vorwahl' => '6217'];
$gemAdd['Pfaffstätt'] = ['vorwahl' => '7742'];
$gemAdd['Pischelsdorf am Engelbach'] = ['vorwahl' => '7742'];
$gemAdd['Polling im Innkreis'] = ['vorwahl' => '7723'];
$gemAdd['Roßbach'] = ['vorwahl' => ['7724' => '?', '7723' => '?', '7755' => '?']];
$gemAdd['St. Georgen am Fillmannsbach'] = ['vorwahl' => '7748', 'plz' => '5144'];
$gemAdd['St. Johann am Walde'] = ['vorwahl' => '7743'];
$gemAdd['St. Peter am Hart'] = ['vorwahl' => '7722'];
$gemAdd[40439] = ['vorwahl' => '6278', 'plz' => '5121'];
$gemAdd['St. Veit im Innkreise'] = ['vorwahl' => '7723', 'plz' => '5273'];
$gemAdd['Schalchen'] = ['vorwahl' => '7742'];
$gemAdd['Tarsdorf'] = ['vorwahl' => '6278', 'plz' => '5121'];
$gemAdd['Treubach'] = ['vorwahl' => '7724'];
$gemAdd['Überackern'] = ['vorwahl' => '7727'];
$gemAdd['Weng im Innkreis'] = ['vorwahl' => '7723'];
$gemAdd['Fraham'] = ['vorwahl' => '7272', 'plz' => '4070'];
$gemAdd['Hartkirchen'] = ['vorwahl' => '7273'];
$gemAdd['Hinzenbach'] = ['vorwahl' => '7272', 'plz' => '4070'];
$gemAdd['Prambachkirchen'] = ['vorwahl' => '7277'];
$gemAdd['Pupping'] = ['vorwahl' => '7272', 'plz' => '4070'];
$gemAdd['St. Marienkirchen an der Polsenz'] = ['vorwahl' => '7249'];
$gemAdd['Scharten'] = ['vorwahl' => '7272'];
$gemAdd['Stroheim'] = ['vorwahl' => '7272'];
$gemAdd['Grünbach'] = ['vorwahl' => '7942'];
$gemAdd['Hagenberg im Mühlkreis'] = ['vorwahl' => '7236'];
$gemAdd['Kaltenberg'] = ['vorwahl' => '7956', 'plz' => '4273'];
$gemAdd['Lasberg'] = ['vorwahl' => '7947'];
$gemAdd['Lasberg'] = ['vorwahl' => '7947'];
$gemAdd['Leopoldschlag'] = ['vorwahl' => '7949'];
$gemAdd['Pierbach'] = ['vorwahl' => '7267'];
$gemAdd['St. Leonhard bei Freistadt'] = ['vorwahl' => '7952'];
$gemAdd['Tragwein'] = ['vorwahl' => '7263'];
$gemAdd['Unterweitersdorf'] = ['vorwahl' => '7235'];
$gemAdd['Waldburg'] = ['vorwahl' => '7942', 'plz' => '4240'];
$gemAdd['Wartberg ob der Aist'] = ['vorwahl' => '7236'];
$gemAdd['Gschwandt'] = ['vorwahl' => '7612'];
$gemAdd['Ohlsdorf'] = ['vorwahl' => '7612'];
$gemAdd['Pinsdorf'] = ['vorwahl' => '7612'];
$gemAdd['Roitham'] = ['vorwahl' => '7613'];
$gemAdd['St. Konrad'] = ['vorwahl' => '7615'];
$gemAdd['Aistersheim'] = ['vorwahl' => '7734'];
$gemAdd['Bruck-Waasen'] = ['vorwahl' => '7276', 'plz' => '4722'];
$gemAdd['Eschenau im Hausruckkreis'] = ['vorwahl' => '7278', 'plz' => '4724'];
$gemAdd['Gallspach'] = ['vorwahl' => '7248'];
$gemAdd['Geboltskirchen'] = ['vorwahl' => '7732'];
$gemAdd['Heiligenberg'] = ['vorwahl' => '7277'];
$gemAdd['Kallham'] = ['vorwahl' => '7733', 'plz' => '4720'];
$gemAdd['Meggenhofen'] = ['vorwahl' => '7247'];
$gemAdd['Michaelnbach'] = ['vorwahl' => '7277'];
$gemAdd['Natternbach'] = ['vorwahl' => '7278'];
$gemAdd['Pötting'] = ['vorwahl' => '7733', 'plz' => '4720'];
$gemAdd['Pollham'] = ['vorwahl' => '7248', 'plz' => '4710'];
$gemAdd['Rottenbach'] = ['vorwahl' => '7732'];
$gemAdd['St. Agatha'] = ['vorwahl' => '7277'];
$gemAdd['St. Georgen bei Grieskirchen'] = ['vorwahl' => '7248', 'plz' => '4710'];
$gemAdd['St. Thomas'] = ['vorwahl' => '7277'];
$gemAdd['Schlüßlberg'] = ['vorwahl' => '7248'];
$gemAdd['Steegen'] = ['vorwahl' => '7276', 'plz' => '4722'];
$gemAdd['Taufkirchen an der Trattnach'] = ['vorwahl' => '7734'];
$gemAdd['Tollet'] = ['vorwahl' => '7248', 'plz' => '4710'];
$gemAdd['Wallern an der Trattnach'] = ['vorwahl' => '7249'];
$gemAdd['Weibern'] = ['vorwahl' => '7732'];
$gemAdd['Wendling'] = ['vorwahl' => '7736'];
$gemAdd['Edlbach'] = ['vorwahl' => '7562', 'plz' => '4580'];
$gemAdd['Inzersdorf im Kremstal'] = ['vorwahl' => '7582'];
$gemAdd['Micheldorf in Oberösterreich'] = ['vorwahl' => '7582'];
$gemAdd['Nußbach'] = ['vorwahl' => '7587'];
$gemAdd['Oberschlierbach'] = ['vorwahl' => '7582'];
$gemAdd['Roßleithen'] = ['vorwahl' => '7562'];
$gemAdd['Schlierbach'] = ['vorwahl' => '7582'];
$gemAdd['Steinbach am Ziehberg'] = ['vorwahl' => '7582'];
$gemAdd['Steinbach an der Steyr'] = ['vorwahl' => '7257'];
$gemAdd['Vorderstoder'] = ['vorwahl' => '7564'];
$gemAdd['Allhaming'] = ['vorwahl' => '7227'];
$gemAdd['Ansfelden'] = ['vorwahl' => ['7229' => '?', '7227' => '?', '732' => '?']];
$gemAdd['Asten'] = ['vorwahl' => '7224'];
$gemAdd['Eggendorf im Traunkreis'] = ['vorwahl' => ['7228' => 'südöstlich der A1', '7243' => 'nordwestlich der A1']];
$gemAdd['Hofkirchen im Traunkreis'] = ['vorwahl' => '7225'];
$gemAdd['Kirchberg-Thening'] = ['vorwahl' => '7221'];
$gemAdd['Kronstorf'] = ['vorwahl' => '7225'];
$gemAdd['Leonding'] = ['vorwahl' => '732'];
$gemAdd['Niederneukirchen'] = ['vorwahl' => '7224'];
$gemAdd['Oftering'] = ['vorwahl' => '7221'];
$gemAdd['Pasching'] = ['vorwahl' => ['7221' => '?', '7229' => '?']];
$gemAdd['Piberbach'] = ['vorwahl' => '7228'];
$gemAdd['Pucking'] = ['vorwahl' => '7229'];
$gemAdd['St. Marien'] = ['vorwahl' => '7227'];
$gemAdd['Allerheiligen im Mühlkreis'] = ['vorwahl' => '7262', 'plz' => '4320'];
$gemAdd['Arbing'] = ['vorwahl' => '7269'];
$gemAdd['Dimbach'] = ['vorwahl' => '7260'];
$gemAdd['Katsdorf'] = ['vorwahl' => '7235'];
$gemAdd['Klam'] = ['vorwahl' => '7269'];
$gemAdd['Langenstein'] = ['vorwahl' => '7237', 'plz' => ['4222' => '?', '4310' => '?', '4312' => '?']];;
$gemAdd['Luftenberg an der Donau'] = ['vorwahl' => '7237'];
$gemAdd['Mitterkirchen im Machland'] = ['vorwahl' => '7269'];
$gemAdd['Münzbach'] = ['vorwahl' => '7264'];
$gemAdd['Naarn im Machlande'] = ['vorwahl' => '7262'];
$gemAdd['Rechberg'] = ['vorwahl' => '7264'];
$gemAdd['Ried in der Riedmark'] = ['vorwahl' => '7238'];
$gemAdd['St. Nikola an der Donau'] = ['vorwahl' => '7268'];
$gemAdd['St. Thomas am Blasenstein'] = ['vorwahl' => '7265'];
$gemAdd['Saxen'] = ['vorwahl' => '7269'];
$gemAdd['Schwertberg'] = ['vorwahl' => '7262'];
$gemAdd['Aurolzmünster'] = ['vorwahl' => '7752'];
$gemAdd['Eitzing'] = ['vorwahl' => ['7752' => '?', '7751' => '?']];
$gemAdd['Geiersberg'] = ['vorwahl' => '7732'];
$gemAdd['Geinberg'] = ['vorwahl' => '7723'];
$gemAdd['Hohenzell'] = ['vorwahl' => '7752'];
$gemAdd['Kirchdorf am Inn'] = ['vorwahl' => '7758'];
$gemAdd['Kirchheim im Innkreis'] = ['vorwahl' => '7755'];
$gemAdd['Lohnsburg am Kobernaußerwald'] = ['vorwahl' => '7754'];
$gemAdd['Mehrnbach'] = ['vorwahl' => '7752'];
$gemAdd['Mörschwang'] = ['vorwahl' => '7758', 'plz' => '4982'];
$gemAdd['Mühlheim am Inn'] = ['vorwahl' => '7723'];
$gemAdd['Neuhofen im Innkreis'] = ['vorwahl' => '7752'];
$gemAdd['Ort im Innkreis'] = ['vorwahl' => '7751'];
$gemAdd['Pattigham'] = ['vorwahl' => '7754', 'plz' => '4910'];
$gemAdd['Peterskirchen'] = ['vorwahl' => '7750'];
$gemAdd['Pramet'] = ['vorwahl' => '7750'];
$gemAdd['Reichersberg'] = ['vorwahl' => '7758'];
$gemAdd['St. Georgen bei Obernberg am Inn'] = ['vorwahl' => '7758'];
$gemAdd['St. Marienkirchen am Hausruck'] = ['vorwahl' => '7753'];
$gemAdd['Schildorn'] = ['vorwahl' => '7754'];
$gemAdd['Senftenbach'] = ['vorwahl' => '7751', 'plz' => '4973'];
$gemAdd['Taiskirchen im Innkreis'] = ['vorwahl' => ['7764' => '?', '7765' => '?', '7750' => '?']];
$gemAdd['Tumeltsham'] = ['vorwahl' => ['7752' => '?', '7750' => '?']];
$gemAdd['Utzenaich'] = ['vorwahl' => '7751'];
$gemAdd['Weilbach'] = ['vorwahl' => '7757'];
$gemAdd['Wippenham'] = ['vorwahl' => '7757', 'plz' => '4942'];
$gemAdd['Afiesl'] = ['vorwahl' => '7216', 'plz' => '4170'];
$gemAdd['Ahorn'] = ['vorwahl' => '7216', 'plz' => '4184'];
$gemAdd['Altenfelden'] = ['vorwahl' => '7282'];
$gemAdd['Arnreit'] = ['vorwahl' => '7282'];
$gemAdd['Atzesberg'] = ['vorwahl' => '7283', 'plz' => '4152'];
$gemAdd['Auberg'] = ['vorwahl' => '7282', 'plz' => '4171'];
$gemAdd['Haslach an der Mühl'] = ['vorwahl' => '7289'];
$gemAdd['Hörbich'] = ['vorwahl' => '7286', 'plz' => '4132'];
$gemAdd['Julbach'] = ['vorwahl' => '7288'];
$gemAdd['Kirchberg ob der Donau'] = ['vorwahl' => '7282'];
$gemAdd['Klaffer am Hochficht'] = ['vorwahl' => '7288'];
$gemAdd['Kleinzell im Mühlkreis'] = ['vorwahl' => '7282'];
$gemAdd['Kollerschlag'] = ['vorwahl' => '7287'];
$gemAdd['Lichtenau im Mühlkreis'] = ['vorwahl' => '7289', 'plz' => '4170'];
$gemAdd['Nebelberg'] = ['vorwahl' => '7287'];
$gemAdd['Niederkappel'] = ['vorwahl' => '7286'];
$gemAdd['Niederwaldkirchen'] = ['vorwahl' => '7231'];
$gemAdd['Oepping'] = ['vorwahl' => '7289'];
$gemAdd['Pfarrkirchen im Mühlkreis'] = ['vorwahl' => '7285'];
$gemAdd['Putzleinsdorf'] = ['vorwahl' => '7286'];
$gemAdd['Neustift im Mühlkreis'] = ['vorwahl' => '7284'];
$gemAdd['St. Johann am Wimberg'] = ['vorwahl' => '7217'];
$gemAdd['St. Oswald bei Haslach'] = ['vorwahl' => '7289', 'plz' => '4170'];
$gemAdd['St. Peter am Wimberg'] = ['vorwahl' => '7282'];
$gemAdd['St. Stefan am Walde'] = ['vorwahl' => '7216', 'plz' => '4170'];
$gemAdd['St. Ulrich im Mühlkreis'] = ['vorwahl' => '7282'];
$gemAdd['Schönegg'] = ['vorwahl' => '7216', 'plz' => '4184'];
$gemAdd['Altschwendt'] = ['vorwahl' => '7262'];
$gemAdd['Brunnenthal'] = ['vorwahl' => '7712'];
$gemAdd['Diersbach'] = ['vorwahl' => '7719'];
$gemAdd['Dorf an der Pram'] = ['vorwahl' => '7764'];
$gemAdd['Engelhartszell an der Donau'] = ['vorwahl' => '7717'];
$gemAdd['Enzenkirchen'] = ['vorwahl' => '7762'];
$gemAdd['Freinberg'] = ['vorwahl' => '7713'];
$gemAdd['Mayrhof'] = ['vorwahl' => '7767'];
$gemAdd['Rainbach im Innkreis'] = ['vorwahl' => '7716'];
$gemAdd['St. Florian am Inn'] = ['vorwahl' => ['7712' => '?', '7719' => '?']];
$gemAdd['St. Marienkirchen bei Schärding'] = ['vorwahl' => '7711'];
$gemAdd['St. Roman'] = ['vorwahl' => '7716'];
$gemAdd['St. Willibald'] = ['vorwahl' => '7762'];
$gemAdd['Sigharting'] = ['vorwahl' => '7766'];
$gemAdd['Vichtenstein'] = ['vorwahl' => '7714'];
$gemAdd['Wernstein am Inn'] = ['vorwahl' => '7713'];
$gemAdd['Zell an der Pram'] = ['vorwahl' => '7764'];
$gemAdd['Adlwang'] = ['vorwahl' => '7258'];
$gemAdd['Aschach an der Steyr'] = ['vorwahl' => '7259'];
$gemAdd['Dietach'] = ['vorwahl' => '7252', 'plz' => '4407'];
$gemAdd['Garsten'] = ['vorwahl' => '7252'];
$gemAdd['Laussa'] = ['vorwahl' => '7255'];
$gemAdd['Pfarrkirchen bei Bad Hall'] = ['vorwahl' => '7258', 'plz' => '4540'];
$gemAdd['Reichraming'] = ['vorwahl' => '7255'];
$gemAdd['Rohr im Kremstal'] = ['vorwahl' => '7258'];
$gemAdd['St. Ulrich bei Steyr'] = ['vorwahl' => '7252'];
$gemAdd['Waldneukirchen'] = ['vorwahl' => '7258'];
$gemAdd['Alberndorf in der Riedmark'] = ['vorwahl' => '7235'];
$gemAdd['Eidenberg'] = ['vorwahl' => ['7230' => '?', '7212' => '?'], 'plz' => '4201'];
$gemAdd['Engerwitzdorf'] = ['vorwahl' => '7235'];
$gemAdd['Goldwörth'] = ['vorwahl' => '7234'];
$gemAdd['Gramastetten'] = ['vorwahl' => '7239'];
$gemAdd['Haibach im Mühlkreis'] = ['vorwahl' => '7211', 'plz' => '4204'];
$gemAdd['Kirchschlag bei Linz'] = ['vorwahl' => '7215'];
$gemAdd['Lichtenberg'] = ['vorwahl' => '7239', 'plz' => '4040'];
$gemAdd['Oberneukirchen'] = ['vorwahl' => '7212'];
$gemAdd['Ottenschlag im Mühlkreis'] = ['vorwahl' => '7211', 'plz' => '4204'];
$gemAdd['Puchenau'] = ['vorwahl' => ['7239' => '?', '7234' => '?']];
$gemAdd['St. Gotthard im Mühlkreis'] = ['vorwahl' => '7234'];
$gemAdd['Schenkenfelden'] = ['vorwahl' => '7214'];
$gemAdd['Sonnberg im Mühlkreis'] = ['vorwahl' => '7212', 'plz' => '4180'];
$gemAdd['Steyregg'] = ['vorwahl' => '732'];
$gemAdd['Walding'] = ['vorwahl' => '7234'];
$gemAdd['Atzbach'] = ['vorwahl' => '7676'];
$gemAdd['Aurach am Hongar'] = ['vorwahl' => '7662', 'plz' => '4861'];
$gemAdd['Berg im Attergau'] = ['vorwahl' => '7667', 'plz' => '4880'];
$gemAdd['Desselbrunn'] = ['vorwahl' => '7673'];
$gemAdd['Fornach'] = ['vorwahl' => '7682'];
$gemAdd['Gampern'] = ['vorwahl' => '7682'];
$gemAdd['Innerschwand am Mondsee'] = ['vorwahl' => '6232'];
$gemAdd['Lenzing'] = ['vorwahl' => '7672'];
$gemAdd['Manning'] = ['vorwahl' => '7676'];
$gemAdd['Neukirchen an der Vöckla'] = ['vorwahl' => '7682'];
$gemAdd['Niederthalheim'] = ['vorwahl' => '7673'];
$gemAdd['Nußdorf am Attersee'] = ['vorwahl' => '7666'];
$gemAdd['Oberndorf bei Schwanenstadt'] = ['vorwahl' => '7673', 'plz' => '4690'];
$gemAdd['Pfaffing'] = ['vorwahl' => '7682', 'plz' => '4870'];
$gemAdd['Pilsbach'] = ['vorwahl' => ['7672' => '?', '7676' => '?'], 'plz' => ['4840' => '?', '4841' => '?', '4800' => '?']];
$gemAdd['Pitzenberg'] = ['vorwahl' => '7673', 'plz' => '4690'];
$gemAdd['Pöndorf'] = ['vorwahl' => '7684'];
$gemAdd['Puchkirchen am Trattberg'] = ['vorwahl' => '7682'];
$gemAdd['Pühret'] = ['vorwahl' => '7674', 'plz' => ['4800' => '?', '4690' => '?']];
$gemAdd['Redleiten'] = ['vorwahl' => '7683'];
$gemAdd['Redlham'] = ['vorwahl' => ['7674' => '?', '7673' => '?']];
$gemAdd['Regau'] = ['vorwahl' => '7672'];
$gemAdd['Rüstorf'] = ['vorwahl' => '7673', 'plz' => '4690'];
$gemAdd['Rutzenham'] = ['vorwahl' => '7673', 'plz' => '4690'];
$gemAdd['St. Lorenz'] = ['vorwahl' => '6232', 'plz' => '5310'];
$gemAdd['Schlatt'] = ['vorwahl' => '7673'];
$gemAdd['Schörfling am Attersee'] = ['vorwahl' => '7662'];
$gemAdd['Straß im Attergau'] = ['vorwahl' => '7667'];
$gemAdd['Tiefgraben'] = ['vorwahl' => '6232', 'plz' => '5310'];
$gemAdd['Timelkam'] = ['vorwahl' => '7672'];
$gemAdd['Ungenach'] = ['vorwahl' => '7672'];
$gemAdd['Weißenkirchen im Attergau'] = ['vorwahl' => '7684', 'plz' => '4890'];
$gemAdd['Wolfsegg am Hausruck'] = ['vorwahl' => '7676'];
$gemAdd['Zell am Pettenfirst'] = ['vorwahl' => '7675'];
$gemAdd['Aichkirchen'] = ['vorwahl' => '7735', 'plz' => '4671'];
$gemAdd['Bachmanning'] = ['vorwahl' => '7735'];
$gemAdd['Bad Wimsbach-Neydharting'] = ['vorwahl' => '7245'];
$gemAdd['Buchkirchen'] = ['vorwahl' => ['7242' => '?', '7243' => '?']];
$gemAdd['Eberstalzell'] = ['vorwahl' => '7241'];
$gemAdd['Edt bei Lambach'] = ['vorwahl' => '7245'];
$gemAdd['Fischlham'] = ['vorwahl' => '7241', 'plz' => '4652'];
$gemAdd['Holzhausen'] = ['vorwahl' => '7243'];
$gemAdd['Krenglbach'] = ['vorwahl' => '7249'];
$gemAdd['Neukirchen bei Lambach'] = ['vorwahl' => '7245'];
$gemAdd['Offenhausen'] = ['vorwahl' => '7247'];
$gemAdd['Pennewang'] = ['vorwahl' => '7245'];
$gemAdd['Pichl bei Wels'] = ['vorwahl' => '7247'];
$gemAdd['Schleißheim'] = ['vorwahl' => '7242', 'plz' => '4600'];
$gemAdd['Stadl-Paura'] = ['vorwahl' => '7245'];
$gemAdd['Steinhaus'] = ['vorwahl' => '7242'];
$gemAdd['Thalheim bei Wels'] = ['vorwahl' => '7242'];
$gemAdd['Weißkirchen an der Traun'] = ['vorwahl' => '7243'];

# Salzburg
$gemAdd['Adnet'] = ['vorwahl' => '6245'];
$gemAdd['Kuchl'] = ['vorwahl' => '6244'];
$gemAdd['Oberalm'] = ['vorwahl' => '6245'];
$gemAdd['Puch bei Hallein'] = ['vorwahl' => '6245'];
$gemAdd['Scheffau am Tennengebirge'] = ['vorwahl' => '6244', 'plz' => '5440'];
$gemAdd['Bad Vigaun'] = ['vorwahl' => '6245'];
$gemAdd['Anif'] = ['vorwahl' => '6246'];
$gemAdd['Bergheim'] = ['vorwahl' => '662'];
$gemAdd['Berndorf bei Salzburg'] = ['vorwahl' => '6217'];
$gemAdd['Bürmoos'] = ['vorwahl' => '6274'];
$gemAdd['Dorfbeuern'] = ['vorwahl' => '6274'];
$gemAdd['Ebenau'] = ['vorwahl' => '6221'];
$gemAdd['Elixhausen'] = ['vorwahl' => '662'];
$gemAdd['Elsbethen'] = ['vorwahl' => '662'];
$gemAdd['Göming'] = ['vorwahl' => '6272'];
$gemAdd['Hallwang'] = ['vorwahl' => '662'];
$gemAdd['Hintersee'] = ['plz' => '5324'];
$gemAdd['Köstendorf'] = ['vorwahl' => '6216'];
$gemAdd['Plainfeld'] = ['vorwahl' => '6229'];
$gemAdd['St. Georgen bei Salzburg'] = ['vorwahl' => '6272'];
$gemAdd['Schleedorf'] = ['vorwahl' => '6216'];
$gemAdd['Seeham'] = ['vorwahl' => '6217'];
$gemAdd['Wals-Siezenheim'] = ['vorwahl' => '662'];
$gemAdd['Altenmarkt im Pongau'] = ['vorwahl' => '6452'];
$gemAdd['Eben im Pongau'] = ['vorwahl' => '6458'];
$gemAdd['Forstau'] = ['vorwahl' => '6454'];
$gemAdd['Goldegg'] = ['vorwahl' => '6415'];
$gemAdd['Pfarrwerfen'] = ['vorwahl' => '6468'];
$gemAdd['St. Martin am Tennengebirge'] = ['vorwahl' => '6463'];
$gemAdd['St. Veit im Pongau'] = ['vorwahl' => '6415'];
$gemAdd['St. Andrä im Lungau'] = ['vorwahl' => '6474'];
$gemAdd['Thomatal'] = ['vorwahl' => '6476'];
$gemAdd['Unternberg'] = ['vorwahl' => '6474'];
$gemAdd['Weißpriach'] = ['vorwahl' => '6473'];
$gemAdd['Hollersbach im Pinzgau'] = ['vorwahl' => '6562'];
$gemAdd['Maishofen'] = ['vorwahl' => '6542'];
$gemAdd['St. Martin bei Lofer'] = ['vorwahl' => '6588'];
$gemAdd['Stuhlfelden'] = ['vorwahl' => '6562'];
$gemAdd['Viehhofen'] = ['vorwahl' => '6542'];
$gemAdd['Wald im Pinzgau'] = ['vorwahl' => '6565'];
$gemAdd['Weißbach bei Lofer'] = ['vorwahl' => '6582'];

# Steiermark
$gemAdd['Frauental'] = ['vorwahl' => '3462'];
$gemAdd['Lannach'] = ['vorwahl' => '3136'];
$gemAdd['St. Josef'] = ['vorwahl' => '3136'];
$gemAdd[60329] = ['vorwahl' => '3467'];
$gemAdd['Wettmannstätten'] = ['vorwahl' => '3185'];
$gemAdd[60347] = ['vorwahl' => ['3465' => '?', '3457' => '?']];
$gemAdd[60348] = ['vorwahl' => '3463'];
$gemAdd['Wies'] = ['vorwahl' => ['3465' => '?', '3467' => '?', '3468' => '?', '3466' => '?']];
$gemAdd['Feldkirchen'] = ['vorwahl' => ['3116' => '?', '3135' => '?']];
$gemAdd['Gössendorf'] = ['vorwahl' => ['3116' => '?', '3135' => '?']];
$gemAdd['Hart'] = ['vorwahl' => '3116'];
$gemAdd['Haselsdorf-Tobelbad'] = ['vorwahl' => '3136'];
$gemAdd['Hausmannstätten'] = ['vorwahl' => '3135'];
$gemAdd['Kainbach'] = ['vorwahl' => ['316' => '?', '3133' => '?']];
$gemAdd['Laßnitzhöhe'] = ['vorwahl' => '3133'];
$gemAdd['Lieboch'] = ['vorwahl' => '3136'];
$gemAdd['St. Bartholomä'] = ['vorwahl' => '3123', 'plz' => ['8113' => '?', '8151' => '?']];
$gemAdd[60642] = ['vorwahl' => '3132'];
$gemAdd['Semriach'] = ['vorwahl' => '3127'];
$gemAdd['Stattegg'] = ['vorwahl' => '316', 'plz' => '8046'];
$gemAdd['Stiwoll'] = ['vorwahl' => '3142', 'plz' => '8113'];
$gemAdd['Thal'] = ['vorwahl' => '316'];
$gemAdd['Vasoldsberg'] = ['vorwahl' => ['3135' => '?', '316' => '?', '3133' => '?', '3134' => '?']];
$gemAdd['Weinitzen'] = ['vorwahl' => '3132', 'plz' => ['8044' => '?', '8045' => '?']];
$gemAdd['Werndorf'] = ['vorwahl' => '3135'];
$gemAdd['Wundschuh'] = ['vorwahl' => '3135'];
$gemAdd['Deutschfeistritz'] = ['vorwahl' => '3127'];
$gemAdd['Fernitz-Mellach'] = ['vorwahl' => '3135'];
$gemAdd['Gratwein-Straßengel'] = ['vorwahl' => '3124'];
$gemAdd['Hitzendorf'] = ['vorwahl' => '3137'];
$gemAdd['Raaba-Grambach'] = ['vorwahl' => '316'];
$gemAdd['Seiersberg-Pirka'] = ['vorwahl' => '316', 'plz' => ['8054' => '?', '8055' => '?', '8073' => '?', '8144' => '?']];
$gemAdd['Unterpremstätten-Zettling'] = ['vorwahl' => '3136'];
$gemAdd['Allerheiligen'] = ['vorwahl' => '3182'];
$gemAdd['Empersdorf'] = ['vorwahl' => '3134', 'plz' => ['8081' => '?', '8302' => '?']];
$gemAdd['Gabersdorf'] = ['vorwahl' => '3452'];
$gemAdd['Gralla'] = ['vorwahl' => '3452'];
$gemAdd['Großklein'] = ['vorwahl' => '3456'];
$gemAdd['Heimschuh'] = ['vorwahl' => '3452'];
$gemAdd['Hengsberg'] = ['vorwahl' => ['3185' => '?', '3182' => '?']];
$gemAdd['Lang'] = ['vorwahl' => '3182', 'plz' => '8403'];
$gemAdd['Lebring-St. Margarethen'] = ['vorwahl' => '3182'];
$gemAdd['Oberhaag'] = ['vorwahl' => '3455'];
$gemAdd['Ragnitz'] = ['vorwahl' => '3183', 'plz' => '8413'];
$gemAdd['St. Andrä-Höch'] = ['vorwahl' => ['3456' => '?', '3457' => '?', '3185' => '?']];
$gemAdd[61032] = ['vorwahl' => '3455'];
$gemAdd['St. Nikolai'] = ['vorwahl' => ['3185' => '?', '3456' => '?']];
$gemAdd['Tillmitsch'] = ['vorwahl' => '3452'];
$gemAdd['Wagna'] = ['vorwahl' => '3452'];
$gemAdd['Gamlitz'] = ['vorwahl' => '3453'];
$gemAdd['St. Veit'] = ['vorwahl' => '3453'];
$gemAdd['Straß-Spielfeld'] = ['vorwahl' => '3453'];
$gemAdd['Niklasdorf'] = ['vorwahl' => '3842'];
$gemAdd['Proleb'] = ['vorwahl' => '3842', 'plz' => '8712'];
$gemAdd['St. Peter-Freienstein'] = ['vorwahl' => '3842'];
$gemAdd[61115] = ['vorwahl' => '3832'];
$gemAdd['Aigen'] = ['vorwahl' => '3682'];
$gemAdd['Altaussee'] = ['vorwahl' => '3622'];
$gemAdd['Altenmark'] = ['vorwahl' => '3632'];
$gemAdd['Ardning'] = ['vorwahl' => '3612'];
$gemAdd['Grundlsee'] = ['vorwahl' => '3622'];
$gemAdd['Lassing'] = ['vorwahl' => '3612'];
$gemAdd[61236] = ['vorwahl' => '3687'];
$gemAdd['Wörschach'] = ['vorwahl' => '3682'];
$gemAdd['Aich'] = ['vorwahl' => '3686'];
$gemAdd['Michaelerberg-Pruggern'] = ['vorwahl' => '3685'];
$gemAdd['Öblarn'] = ['vorwahl' => '3684'];
$gemAdd['Niederwölz'] = ['vorwahl' => '3582'];
$gemAdd['Schöder'] = ['vorwahl' => '3536'];
$gemAdd['Ranten'] = ['vorwahl' => '3535'];
$gemAdd['Ligist'] = ['vorwahl' => '3143'];
$gemAdd['Mooskirchen'] = ['vorwahl' => '3137'];
$gemAdd['Rosental'] = ['vorwahl' => '3142'];
$gemAdd[61621] = ['vorwahl' => '3140', 'plz' => '8580'];
$gemAdd['Stallhofen'] = ['vorwahl' => '3142'];
$gemAdd['Bärnbach'] = ['vorwahl' => '3142'];
$gemAdd['Albersdorf-Prebuch'] = [
        'vorwahl' => ['3112' => '?', '3178' => '?'],
        'plz' => ['8181' => '?', '8200' => '?', '8211' => '?']
];
$gemAdd['Floing'] = ['vorwahl' => '3177'];
$gemAdd['Hofstätten'] = ['vorwahl' => '3112', 'plz' => '8200'];
$gemAdd['Ludersdorf-Wilfersdorf'] = ['vorwahl' => '3112', 'plz' => ['8200' => '?', '8063' => '?']];
$gemAdd[61728] = ['vorwahl' => '3174', 'plz' => '8190'];
$gemAdd['Mitterdorf'] = ['vorwahl' => '3178', 'plz' => '8181'];
$gemAdd['Mortantsch'] = ['vorwahl' => '3172', 'plz' => '8160'];
$gemAdd['Naas'] = ['vorwahl' => '3172', 'plz' => '8160'];
$gemAdd['Rettenegg'] = ['vorwahl' => '3173'];
$gemAdd[61744] = ['vorwahl' => '3173'];
$gemAdd[61745] = ['vorwahl' => '3179'];
$gemAdd[61746] = ['vorwahl' => '3115'];
$gemAdd['Strallegg'] = ['vorwahl' => '3174'];
$gemAdd['Thannhausen'] = ['vorwahl' => '3172', 'plz' => '8160'];
$gemAdd['Fladnitz'] = ['vorwahl' => '3179'];
$gemAdd['Gersdorf'] = ['vorwahl' => '3113', 'plz' => '8213'];
$gemAdd['Gutenberg-Stenzengreith'] = ['vorwahl' => ['3132' => '?', '3172' => '?'], 'plz' => '8164'];
$gemAdd['Ilztal'] = ['vorwahl' => ['3112' => '?', '3113' => '?', '3118' => '?']];
$gemAdd['Kobenz'] = ['vorwahl' => ['3512' => 'Unterfarrach', '3514' => 'Oberfarrach']];
$gemAdd['St. Georgen ob Judenburg'] = ['vorwahl' => '3583'];
$gemAdd['St. Peter ob Judenburg'] = ['vorwahl' => ['3579' => '?', '3572' => '?']];
$gemAdd['St. Marein-Feistritz'] = ['vorwahl' => '3515'];
$gemAdd['Spielberg'] = ['vorwahl' => ['3512' => '?', '3577' => '?']];
$gemAdd['Weißkirchen'] = ['vorwahl' => '3577'];
$gemAdd[62128] = ['vorwahl' => '3864', 'plz' => '8642'];
$gemAdd['Stanz'] = ['vorwahl' => '3865'];
$gemAdd['Kapfenberg'] = ['vorwahl' => '3862'];
$gemAdd['Thörl'] = ['vorwahl' => '3861'];
$gemAdd['Blumau'] = ['vorwahl' => '3383'];
$gemAdd['Buch-St. Magdalena'] = ['vorwahl' => '3332'];
$gemAdd['Ebersdorf'] = ['vorwahl' => '3333'];
$gemAdd['Greinbach'] = ['vorwahl' => '3332', 'plz' => ['8230' => '?', '8225' => '?']];
$gemAdd['Hartberg Umgebung'] = ['vorwahl' => '3332', 'plz' => ['8230' => '?', '8225' => '?', '8274' => '?']];
$gemAdd['Ottendorf'] = ['vorwahl' => '3114'];
$gemAdd['Pinggau'] = ['vorwahl' => '3339'];
$gemAdd['Pöllauberg'] = ['vorwahl' => '3335', 'plz' => '8225'];
$gemAdd[62242] = ['vorwahl' => ['3336' => '?', '3173' => '?']];
$gemAdd[62244] = ['vorwahl' => '3332'];
$gemAdd['Schäffern'] = ['vorwahl' => '3339'];
$gemAdd['Wenigzell'] = ['vorwahl' => '3336'];
$gemAdd['Dechantskirchen'] = ['vorwahl' => '3339'];
$gemAdd['Feistritztal'] = ['vorwahl' => '3113'];
$gemAdd['Grafendorf'] = ['vorwahl' => '3338'];
$gemAdd['Großwilfersdorf'] = ['vorwahl' => '3385'];
$gemAdd['Hartl'] = ['vorwahl' => ['3334' => '?', '3176' => '?'], 'plz' => ['8224' => '?', '8265' => '?', '8272' => '?']];
$gemAdd['Loipersdorf'] = ['vorwahl' => '3382'];
$gemAdd['Neudau'] = ['vorwahl' => '3383'];
$gemAdd[62276] = ['vorwahl' => '3332'];
$gemAdd[62277] = ['vorwahl' => '3338'];
$gemAdd['Edelsbach'] = ['vorwahl' => '3152'];
$gemAdd['Eichkögl'] = ['vorwahl' => '3115', 'plz' => ['8322' => '?', '8311' => '?', '8332' => '?']];
$gemAdd['Jagerberg'] = ['vorwahl' => '3184'];
$gemAdd['Klöch'] = ['vorwahl' => '3475'];
$gemAdd['Mettersdorf'] = ['vorwahl' => '3477'];
$gemAdd['Murfeld'] = ['vorwahl' => '3453', 'plz' => '8471'];
$gemAdd['Tieschen'] = ['vorwahl' => '3475'];
$gemAdd['Unterlamm'] = ['vorwahl' => '3155'];
$gemAdd['Pirching'] = ['vorwahl' => '3134', 'plz' => '8081'];
$gemAdd[62389] = ['vorwahl' => '3116'];
$gemAdd['Tieschen'] = ['vorwahl' => '3475'];
$gemAdd['Tieschen'] = ['vorwahl' => '3475'];

# Tirol
$gemAdd['Arzl im Pitztal'] = ['vorwahl' => '5412'];
$gemAdd['Imsterberg'] = ['vorwahl' => '5412'];
$gemAdd['Jerzens'] = ['vorwahl' => '5414'];
$gemAdd['Karres'] = ['vorwahl' => '5412'];
$gemAdd['Karrösten'] = ['vorwahl' => '5412'];
$gemAdd['Mils bei Imst'] = ['vorwahl' => '5418'];
$gemAdd['Mötz'] = ['vorwahl' => '5263'];
$gemAdd['Obsteig'] = ['vorwahl' => '5264'];
$gemAdd['Rietz'] = ['vorwahl' => '5262'];
$gemAdd['Sautens'] = ['vorwahl' => '5252'];
$gemAdd['Stams'] = ['vorwahl' => '5263'];
$gemAdd['Tarrenz'] = ['vorwahl' => '5412'];
$gemAdd['Absam'] = ['vorwahl' => '5223'];
$gemAdd['Aldrans'] = ['vorwahl' => '512'];
$gemAdd['Ampass'] = ['vorwahl' => '512'];
$gemAdd['Baumkirchen'] = ['vorwahl' => '5224'];
$gemAdd['Birgitz'] = ['vorwahl' => '5234'];
$gemAdd['Ellbögen'] = ['vorwahl' => '512'];
$gemAdd['Flaurling'] = ['vorwahl' => '5262'];
$gemAdd['Fritzens'] = ['vorwahl' => '5224'];
$gemAdd['Gnadenwald'] = ['vorwahl' => '5223'];
$gemAdd['Götzens'] = ['vorwahl' => '5234'];
$gemAdd['Grinzens'] = ['vorwahl' => '5234'];
$gemAdd['Hatting'] = ['vorwahl' => '5238'];
$gemAdd['Inzing'] = ['vorwahl' => '5238'];
$gemAdd['Kolsass'] = ['vorwahl' => '5224'];
$gemAdd['Kolsassberg'] = ['vorwahl' => '5224'];
$gemAdd['Lans'] = ['vorwahl' => '512'];
$gemAdd['Mieders'] = ['vorwahl' => '5225'];
$gemAdd['Mils'] = ['vorwahl' => '5223'];
$gemAdd['Mühlbachl'] = ['vorwahl' => '5273', 'plz' => '6143'];
$gemAdd['Mutters'] = ['vorwahl' => '512'];
$gemAdd['Natters'] = ['vorwahl' => '512'];
$gemAdd['Oberhofen im Inntal'] = ['vorwahl' => '5262'];
$gemAdd['Obernberg am Brenner'] = ['vorwahl' => '5274'];
$gemAdd['Oberperfuss'] = ['vorwahl' => '5232'];
$gemAdd['Patsch'] = ['vorwahl' => '512'];
$gemAdd['Pettnau'] = ['vorwahl' => '5238'];
$gemAdd['Pfaffenhofen'] = ['vorwahl' => '5262'];
$gemAdd['Pfons'] = ['vorwahl' => '5273', 'plz' => '6143'];
$gemAdd['Polling in Tirol'] = ['vorwahl' => '5238'];
$gemAdd['Ranggen'] = ['vorwahl' => '5232'];
$gemAdd['Reith bei Seefeld'] = ['vorwahl' => '5212'];
$gemAdd['Rinn'] = ['vorwahl' => '5223'];
$gemAdd['Rum'] = ['vorwahl' => '512'];
$gemAdd['St. Sigmund im Sellrain'] = ['vorwahl' => '5236'];
$gemAdd['Schmirn'] = ['vorwahl' => '5279'];
$gemAdd['Schönberg im Stubaital'] = ['vorwahl' => '5225'];
$gemAdd['Sistrans'] = ['vorwahl' => '512'];
$gemAdd['Telfes im Stubai'] = ['vorwahl' => '5225'];
$gemAdd['Thaur'] = ['vorwahl' => '5223'];
$gemAdd['Tulfes'] = ['vorwahl' => '5223'];
$gemAdd['Unterperfuss'] = ['vorwahl' => '5232'];
$gemAdd['Vals'] = ['plz' => '6154'];
$gemAdd['Völs'] = ['vorwahl' => '512'];
$gemAdd['Volders'] = ['vorwahl' => '5224'];
$gemAdd['Wattenberg'] = ['vorwahl' => '5224'];
$gemAdd['Wildermieming'] = ['vorwahl' => '5264'];
$gemAdd['Aurach bei Kitzbühel'] = ['vorwahl' => '5356'];
$gemAdd['Brixen im Thale'] = ['vorwahl' => '5334'];
$gemAdd['Going am Wilden Kaiser'] = ['vorwahl' => '5358'];
$gemAdd['Itter'] = ['vorwahl' => '5335'];
$gemAdd['Kirchdorf in Tirol'] = ['vorwahl' => '5352'];
$gemAdd['Oberndorf in Tirol'] = ['vorwahl' => '5352'];
$gemAdd['Reith bei Kitzbühel'] = ['vorwahl' => '5356'];
$gemAdd['St. Jakob in Haus'] = ['vorwahl' => '5354'];
$gemAdd['St. Ulrich am Pillersee'] = ['vorwahl' => '5354'];
$gemAdd['Schwendt'] = ['vorwahl' => '5375'];
$gemAdd['Angath'] = ['vorwahl' => '5332'];
$gemAdd['Bad Häring'] = ['vorwahl' => '5332'];
$gemAdd['Breitenbach am Inn'] = ['vorwahl' => '5338'];
$gemAdd['Erl'] = ['vorwahl' => '5373'];
$gemAdd['Kirchbichl'] = ['vorwahl' => '5332'];
$gemAdd['Kramsach'] = ['vorwahl' => '5337'];
$gemAdd['Langkampfen'] = ['vorwahl' => '5332'];
$gemAdd['Mariastein'] = ['vorwahl' => '5332'];
$gemAdd['Münster'] = ['vorwahl' => '5337'];
$gemAdd['Niederndorf'] = ['vorwahl' => '5373'];
$gemAdd['Niederndorferberg'] = ['vorwahl' => '5373'];
$gemAdd['Radfeld'] = ['vorwahl' => '5337'];
$gemAdd['Reith bei Kitzbühel'] = ['vorwahl' => '5356', 'plz' => '6370'];
$gemAdd['Rattenberg'] = ['vorwahl' => '5337'];
$gemAdd['Reith im Alpbachtal'] = ['vorwahl' => '5337'];
$gemAdd['Rettenschöss'] = ['vorwahl' => '5373'];
$gemAdd['Scheffau am Wilden Kaiser'] = ['vorwahl' => '5358'];
$gemAdd['Schwoich'] = ['vorwahl' => '5372'];
$gemAdd['Angerberg'] = ['vorwahl' => '5332'];
$gemAdd['Faggen'] = ['vorwahl' => '5472'];
$gemAdd['Fendels'] = ['vorwahl' => '5472'];
$gemAdd['Fiss'] = ['vorwahl' => '5476'];
$gemAdd['Grins'] = ['vorwahl' => '5442'];
$gemAdd['Kaunerberg'] = ['vorwahl' => '5472'];
$gemAdd['Kauns'] = ['vorwahl' => '5472'];
$gemAdd['Ladis'] = ['vorwahl' => '5472'];
$gemAdd['Pians'] = ['vorwahl' => '5442'];
$gemAdd['Ried im Oberinntal'] = ['vorwahl' => '5472'];
$gemAdd['Spiss'] = ['vorwahl' => '5474'];
$gemAdd['Stanz bei Landeck'] = ['vorwahl' => '5442', 'plz' => '6500'];
$gemAdd['Strengen'] = ['vorwahl' => '5447'];
$gemAdd['Tobadill'] = ['vorwahl' => '5442'];
$gemAdd['Zams'] = ['vorwahl' => '5442'];
$gemAdd['Amlach'] = ['vorwahl' => '4852'];
$gemAdd['Anras'] = ['vorwahl' => '4846'];
$gemAdd['Dölsach'] = ['vorwahl' => '4852'];
$gemAdd['Gaimberg'] = ['vorwahl' => '4852'];
$gemAdd['Hopfgarten in Defereggen'] = ['vorwahl' => '4846'];
$gemAdd['Innervillgraten'] = ['vorwahl' => '4843'];
$gemAdd['Iselsberg-Stronach'] = ['vorwahl' => '4852'];
$gemAdd['Lavant'] = ['vorwahl' => '4852'];
$gemAdd['Leisach'] = ['vorwahl' => '4852'];
$gemAdd['Nußdorf-Debant'] = ['vorwahl' => '4852'];
$gemAdd['Oberlienz'] = ['vorwahl' => '4852'];
$gemAdd['St. Johann im Walde'] = ['vorwahl' => '4872'];
$gemAdd['Schlaiten'] = ['vorwahl' => '4853'];
$gemAdd['Strassen'] = ['vorwahl' => '4846'];
$gemAdd['Thurn'] = ['vorwahl' => '4852'];
$gemAdd['Tristach'] = ['vorwahl' => '4852'];
$gemAdd['Untertilliach'] = ['vorwahl' => '4847'];
$gemAdd['Heinfels'] = ['vorwahl' => '4842'];
$gemAdd['Bach'] = ['vorwahl' => '5634'];
$gemAdd['Berwang'] = ['vorwahl' => '5674'];
$gemAdd['Biberwier'] = ['vorwahl' => '5673'];
$gemAdd['Breitenwang'] = ['vorwahl' => '5672', 'plz' => '6600'];
$gemAdd['Ehenbichl'] = ['vorwahl' => '5672', 'plz' => '6600'];
$gemAdd['Forchach'] = ['vorwahl' => '5632'];
$gemAdd['Grän'] = ['vorwahl' => '5675'];
$gemAdd['Gramais'] = ['vorwahl' => '5634'];
$gemAdd['Häselgehr'] = ['vorwahl' => '5634'];
$gemAdd['Heiterwang'] = ['vorwahl' => '5674'];
$gemAdd['Hinterhornbach'] = ['vorwahl' => '5632'];
$gemAdd['Höfen'] = ['vorwahl' => '5672'];
$gemAdd['Holzgau'] = ['vorwahl' => '5633'];
$gemAdd['Kaisers'] = ['vorwahl' => '5633', 'plz' => '6655'];
$gemAdd['Lechaschau'] = ['vorwahl' => '5672', 'plz' => '6600'];
$gemAdd['Lermoos'] = ['vorwahl' => '5673'];
$gemAdd['Musau'] = ['vorwahl' => '5677', 'plz' => '6600'];
$gemAdd['Namlos'] = ['vorwahl' => '5674'];
$gemAdd['Nesselwängle'] = ['vorwahl' => '5675'];
$gemAdd['Pfafflar'] = ['vorwahl' => '5635'];
$gemAdd['Pflach'] = ['vorwahl' => '5672', 'plz' => '6600'];
$gemAdd['Pinswang'] = ['vorwahl' => '5677', 'plz' => '6600'];
$gemAdd['Schattwald'] = ['vorwahl' => '5675'];
$gemAdd['Vorderhornbach'] = ['vorwahl' => '5632'];
$gemAdd['Wängle'] = ['vorwahl' => '5672'];
$gemAdd['Zöblen'] = ['vorwahl' => '5675', 'plz' => '6677'];
$gemAdd['Aschau im Zillertal'] = ['vorwahl' => '5282'];
$gemAdd['Brandberg'] = ['vorwahl' => '5285', 'plz' => '6290'];
$gemAdd['Bruck am Ziller'] = ['vorwahl' => '5288'];
$gemAdd['Eben am Achensee'] = ['vorwahl' => ['5243' => 'Eben', '5245' => 'Hinterriß']];
$gemAdd['Finkenberg'] = ['vorwahl' => '5285'];
$gemAdd['Gallzein'] = ['vorwahl' => '5244'];
$gemAdd['Gerlosberg'] = ['vorwahl' => '5282', 'plz' => '6280'];
$gemAdd['Hainzenberg'] = ['vorwahl' => '5282'];
$gemAdd['Hart im Zillertal'] = ['vorwahl' => '5288'];
$gemAdd['Hippach'] = ['vorwahl' => '5282'];
$gemAdd['Pill'] = ['vorwahl' => '5242'];
$gemAdd['Ramsau im Zillertal'] = ['vorwahl' => '5282'];
$gemAdd['Ried im Zillertal'] = ['vorwahl' => '5283'];
$gemAdd['Rohrberg'] = ['vorwahl' => '5282', 'plz' => '6280'];
$gemAdd['Schlitters'] = ['vorwahl' => '5288'];
$gemAdd['Schwendau'] = ['vorwahl' => ['5285' => '?', '5282' => '?'], 'plz' => ['6283' => '?', '6290' => '?', '6292' => '?']];
$gemAdd['Stans'] = ['vorwahl' => '5242'];
$gemAdd['Steinberg am Rofan'] = ['vorwahl' => '5248', 'plz' => '6215'];
$gemAdd['Strass im Zillertal'] = ['vorwahl' => '5244'];
$gemAdd['Stumm'] = ['vorwahl' => '5283'];
$gemAdd['Stummerberg'] = ['vorwahl' => '5283'];
$gemAdd['Terfens'] = ['vorwahl' => ['5224' => '?', '5242' => '?']];
$gemAdd['Uderns'] = ['vorwahl' => '5288'];
$gemAdd['Weer'] = ['vorwahl' => '5224'];
$gemAdd['Weerberg'] = ['vorwahl' => '5224'];
$gemAdd['Wiesing'] = ['vorwahl' => '5244'];
$gemAdd['Zellberg'] = ['vorwahl' => '5282'];

# Vorarlberg
$gemAdd['Bartholomäberg'] = ['vorwahl' => '5556'];
$gemAdd['Blons'] = ['vorwahl' => '5553'];
$gemAdd['Bludesch'] = ['vorwahl' => ['5550' => '?', '5525' => '?']];
$gemAdd['Bürs'] = ['vorwahl' => '5552'];
$gemAdd['Bürserberg'] = ['vorwahl' => '5552'];
$gemAdd['Fontanella'] = ['vorwahl' => '5554'];
$gemAdd['Innerbraz'] = ['vorwahl' => '5552'];
$gemAdd['Lorüns'] = ['vorwahl' => '5552', 'plz' => '6700'];
$gemAdd['Ludesch'] = ['vorwahl' => '5550'];
$gemAdd['Nüziders'] = ['vorwahl' => '5552'];
$gemAdd[80119] = ['vorwahl' => '5552'];
$gemAdd['St. Gerold'] = ['vorwahl' => '5550'];
$gemAdd['Silbertal'] = ['vorwahl' => '5556'];
$gemAdd['Stallehr'] = ['vorwahl' => '5552', 'plz' => '6700'];
$gemAdd['Thüringerberg'] = ['vorwahl' => '5550'];
$gemAdd['Tschagguns'] = ['vorwahl' => '5556'];
$gemAdd['Vandans'] = ['vorwahl' => '5556'];
$gemAdd['Andelsbuch'] = ['vorwahl' => '5512'];
$gemAdd['Bildstein'] = ['vorwahl' => '5572', 'plz' => '6858'];
$gemAdd['Bizau'] = ['vorwahl' => '5514'];
$gemAdd['Buch'] = ['vorwahl' => '5579', 'plz' => '6960'];
$gemAdd['Eichenberg'] = ['vorwahl' => '5574', 'plz' => '6911'];
$gemAdd['Fußach'] = ['vorwahl' => '5578'];
$gemAdd['Gaißau'] = ['vorwahl' => '5578'];
$gemAdd['Hard'] = ['vorwahl' => '5574'];
$gemAdd['Hohenweiler'] = ['vorwahl' => '5573'];
$gemAdd['Kennelbach'] = ['vorwahl' => '5574'];
$gemAdd[80221] = ['vorwahl' => '5513'];
$gemAdd['Langenegg'] = ['vorwahl' => '5513'];
$gemAdd['Lauterach'] = ['vorwahl' => '5574'];
$gemAdd['Lingenau'] = ['vorwahl' => '5513'];
$gemAdd['Lochau'] = ['vorwahl' => '5574'];
$gemAdd['Möggers'] = ['vorwahl' => '5573', 'plz' => '6900'];
$gemAdd['Reuthe'] = ['vorwahl' => '5514', 'plz' => '6870'];
$gemAdd['Riefensberg'] = ['vorwahl' => '5513'];
$gemAdd['Schnepfau'] = ['vorwahl' => '5518'];
$gemAdd['Schoppernau'] = ['vorwahl' => '5515'];
$gemAdd['Schwarzach'] = ['vorwahl' => '5572'];
$gemAdd['Schwarzenberg'] = ['vorwahl' => '5512'];
$gemAdd['Sibratsgfäll'] = ['vorwahl' => '5513'];
$gemAdd['Sulzberg'] = ['vorwahl' => '5516'];
$gemAdd[80239] = ['vorwahl' => '5583'];
$gemAdd['Wolfurt'] = ['vorwahl' => '5574'];
$gemAdd['Altach'] = ['vorwahl' => '5575'];
$gemAdd['Düns'] = ['vorwahl' => '5524', 'plz' => '6822'];
$gemAdd['Dünserberg'] = ['vorwahl' => '5524', 'plz' => '6822'];
$gemAdd['Frastanz'] = ['vorwahl' => '5522'];
$gemAdd['Fraxern'] = ['vorwahl' => '5523', 'plz' => '6833'];
$gemAdd['Göfis'] = ['vorwahl' => '5522'];
$gemAdd['Klaus'] = ['vorwahl' => '5523'];
$gemAdd['Koblach'] = ['vorwahl' => '5523'];
$gemAdd['Laterns'] = ['vorwahl' => '5526', 'plz' => '6830'];
$gemAdd['Mäder'] = ['vorwahl' => '5523'];
$gemAdd['Meiningen'] = ['vorwahl' => '5522'];
$gemAdd['Rankweil'] = ['vorwahl' => '5522'];
$gemAdd['Röns'] = ['vorwahl' => '5524', 'plz' => '6822'];
$gemAdd['Röthis'] = ['vorwahl' => '5522'];
$gemAdd['Schlins'] = ['vorwahl' => '5524'];
$gemAdd['Schnifis'] = ['vorwahl' => '5524', 'plz' => '6822'];
$gemAdd['Sulz'] = ['vorwahl' => '5522', 'plz' => '6832'];
$gemAdd['Übersaxen'] = ['vorwahl' => '5522'];
$gemAdd['Viktorsberg'] = ['vorwahl' => '5523'];
$gemAdd['Weiler'] = ['vorwahl' => '5523'];
$gemAdd['Zwischenwasser'] = ['vorwahl' => '5522'];


$color = array();
$color[1] = array();
$color[1][1] = "#000075";
$color[1][2] = "#469990";
$color[1][3] = "#f58231";
$color[1][4] = "#800000";
$color[1][5] = "#911eb4";
$color[1][6] = "#e6194B";
$color[1][7] = "#3cb44b";
$color[1][8] = "#3cb44b";
$color[1][9] = "#3cb44b";

$outFeatures = array();
$telefon = array();
foreach ($telefonDaten['data'] as $ort) {
    $ortsnetzname = $ort['ortsnetzname'];
    if (substr($ortsnetzname, 0, 6) == 'Sankt ') {
        $ortsnetzname = str_replace('Sankt ', 'St. ', $ortsnetzname);
    }
    if (array_key_exists($ort['ortsnetzkennzahl'], $mapping)) {
        checkMapping($ort['ortsnetzkennzahl'], $ortsnetzname, $mapping[$ort['ortsnetzkennzahl']]);
        $ortsnetzname = $mapping[$ort['ortsnetzkennzahl']];
    }

    if (array_key_exists($ort['ortsnetzkennzahl'], $telefon)) {
        print "ortsnetzkennzahl already exists!\n";
        print_r($ort);
        print "-----\n";
    }
    $tel = array();
    $tel['name'] = $ortsnetzname;
    $tel['original'] = $ort['ortsnetzname'];
    $telefon[$ort['ortsnetzkennzahl']][] = $tel;
}

$plzArray = array();
foreach ($plzDaten['data'] as $ort) {
    $ortsname = $ort['ort'];
    if (substr($ortsname, 0, 6) == 'Sankt ') {
        $ortsname = str_replace('Sankt ', 'St. ', $ortsname);
    }
    if (array_key_exists($ort['plz'], $plzmapping)) {
        checkMapping($ort['plz'], $ortsname, $plzmapping[$ort['plz']]);
        $ortsname = $plzmapping[$ort['plz']];
    }

    $plzArray1 = array();
    $plzArray1['name'] = $ortsname;
    $plzArray1['original'] = $ort['ort'];
    $plzArray[$ort['plz']][] = $plzArray1;
}

$gemKey = array();
$gemKzKey = array(); // gemKz (iso string) -> feature index
foreach ($gemeinden['features'] as $key => $gemeinde) {
    $gemName = trim($gemeinde['properties']['name']);
    $gemKZ = trim($gemeinde['properties']['iso']); 
    if ($gemKZ != $gemeinde['properties']['iso']) {
        print "$gemKZ - $gemName: Kennzahl not truncated!\n";
    }
    if ($gemName != $gemeinde['properties']['name']) {
        print "$gemKZ - $gemName: Name not truncated!\n";
    }

    if (!array_key_exists($gemName, $gemKey)) {
        $gemKey[$gemName] = array();
    }

    $gemKey[$gemName][] = $key;
    $gemKzKey[$gemKZ] = $key;
}

print "found gemKey: ".count($gemKey)."\n\n";

// add gemAdd data to PLZ and Vorwahl data to process
foreach ($gemAdd as $gemAddKey => $gem) {
    // Resolve key: either a name string or a numeric gemKz
    $gemName = null;
    $gemAddIso = null; // set when key is a gemKz, used to pin exact feature in downstream loops
    if (is_int($gemAddKey)) {
        // numeric key -> treat as gemKz (iso)
        $gemAddIso = (string)$gemAddKey;
        if (array_key_exists($gemAddIso, $gemKzKey)) {
            $featureIdx = $gemKzKey[$gemAddIso];
            $gemName = $gemeinden['features'][$featureIdx]['properties']['name'];
        } else {
            print "$gemAddKey from gemAdd was not found in Gemeinde List (by gemKz)!!!\n";
            continue;
        }
    } else {
        $gemName = trim($gemAddKey);
        if (!array_key_exists($gemName, $gemKey)) {
            print "$gemName from gemAdd was not found in Gemeinde List!!!\n";
            continue;
        }
    }

    if (array_key_exists('plz', $gem)) {
        if (is_array($gem['plz'])) {
            foreach ($gem['plz'] as $xplz => $xName) {
                $newPlz = array();
                $newPlz['name'] = $gemName;
                $newPlz['validFor'] = $xName;
                if ($gemAddIso !== null) {
                    $newPlz['iso'] = $gemAddIso;
                }
                if (array_key_exists($xplz, $plzArray)) {
                    $newPlz['original'] = $plzArray[$xplz][0]['original'];
                } else {
                    # Add new PLZ to PLZ list
                    $newPlz['original'] = $xplz;
                    print_r($newPlz);
                }
                $plzArray[$xplz][] = $newPlz;
            }
        } else {
            $newPlz = array();
            $newPlz['name'] = $gemName;
            if ($gemAddIso !== null) {
                $newPlz['iso'] = $gemAddIso;
            }
            if (array_key_exists($gem['plz'], $plzArray)) {
                $newPlz['original'] = $plzArray[$gem['plz']][0]['original'];
            } else {
                # Add new PLZ to PLZ list
                $newPlz['original'] = $gem['plz'];
            }
            $plzArray[$gem['plz']][] = $newPlz;
        }
    }
    if (array_key_exists('vorwahl', $gem)) {
        if (is_array($gem['vorwahl'])) {
            foreach ($gem['vorwahl'] as $xvw => $xName) {
                if (array_key_exists($xvw, $telefon)) {
                    $tel = array();
                    $tel['name'] = $gemName;
                    $tel['validFor'] = $xName;
                    if ($gemAddIso !== null) {
                        $tel['iso'] = $gemAddIso;
                    }
                    $tel['original'] = $telefon[$xvw][0]['original'];
                    $telefon[$xvw][] = $tel;
                } else {
                    print "$gemName - $xvw from gemAdd was not found in telefon List!!!\n";
                }
            }
        } else {
            if (array_key_exists($gem['vorwahl'], $telefon)) {
                $tel = array();
                $tel['name'] = $gemName;
                if ($gemAddIso !== null) {
                    $tel['iso'] = $gemAddIso;
                }
                $tel['original'] = $telefon[$gem['vorwahl']][0]['original'];
                $telefon[$gem['vorwahl']][] = $tel;
            } else {
                print "$gemName - {$gem['vorwahl']} from gemAdd was not found in telefon List!!!\n";
            }
        }
    }
}

$found = array('VWfound' => 0, 'VWnotfound' => 0, 'plzfound' => 0, 'plznotfound' => 0);
foreach ($telefon as $vorwahl => $gemeinde) {
    foreach ($gemeinde as $gemName) {
        $gemId = null;
        if (array_key_exists('iso', $gemName) && array_key_exists($gemName['iso'], $gemKzKey)) {
            // pinned via gemKz from gemAdd — resolve directly, no name ambiguity
            $gemId = $gemKzKey[$gemName['iso']];
        } elseif (array_key_exists($gemName['name'], $gemKey)) {
            $gemId = $gemKey[$gemName['name']][0];
            if (count($gemKey[$gemName['name']]) > 1) {
                if (array_key_exists($vorwahl, $vorwahl2gkz)) {
                    foreach ($gemKey[$gemName['name']] as $id) {
                        if ($gemeinden['features'][$id]['properties']['iso'] == $vorwahl2gkz[$vorwahl]) {
                            $gemId = $id;
                            break;
                        }
                    }
                } else {
                    print "More then one GemKeys found for $vorwahl - ".$gemName['name'].":\n";
                    foreach ($gemKey[$gemName['name']] as $id) {
                        print_r($gemeinden['features'][$id]['properties']);
                    }
                }
            }
        }

        if ($gemId !== null) {
            if (!array_key_exists('vorwahl', $gemeinden['features'][$gemId]['properties'])) {
                $gemeinden['features'][$gemId]['properties']['vorwahl'] = array();
            }
            $gemeinden['features'][$gemId]['properties']['vorwahl']['0'.$vorwahl] = $gemName['original'];
            if (array_key_exists('validFor', $gemName)) {
                $gemeinden['features'][$gemId]['properties']['vorwahl']['0'.$vorwahl] .= ' (für ';
                $gemeinden['features'][$gemId]['properties']['vorwahl']['0'.$vorwahl] .= $gemName['validFor'];
                $gemeinden['features'][$gemId]['properties']['vorwahl']['0'.$vorwahl] .= ')';
            }

            if (strlen($vorwahl) == 1) {
                $gemeinden['features'][$gemId]['properties']['color'] = '#'.
                    $vorwahl.$vorwahl.$vorwahl.$vorwahl.$vorwahl.$vorwahl;
                $gemeinden['features'][$gemId]['properties']['color1'] = $color[1][substr($vorwahl, 0, 1)];
            } elseif(strlen($vorwahl) == 2) {
                $gemeinden['features'][$gemId]['properties']['color'] = '#'.
                    substr($vorwahl, 0, 1).
                    substr($vorwahl, 0, 1).
                    substr($vorwahl, 0, 1).
                    substr($vorwahl, 1, 1).
                    substr($vorwahl, 1, 1).
                    substr($vorwahl, 1, 1);
                $gemeinden['features'][$gemId]['properties']['color1'] = $color[1][substr($vorwahl, 0, 1)];
            } elseif(strlen($vorwahl) == 3) {
                $gemeinden['features'][$gemId]['properties']['color'] = '#'.
                    substr($vorwahl, 0, 1).
                    substr($vorwahl, 0, 1).
                    substr($vorwahl, 1, 1).
                    substr($vorwahl, 1, 1).
                    substr($vorwahl, 2, 1).
                    substr($vorwahl, 2, 1);
                $gemeinden['features'][$gemId]['properties']['color1'] = $color[1][substr($vorwahl, 0, 1)];
            } else {
                $gemeinden['features'][$gemId]['properties']['color'] = '#'.
                    substr($vorwahl, 0, 1).
                    substr($vorwahl, 0, 1).
                    substr($vorwahl, 1, 1).
                    substr($vorwahl, 1, 1).
                    substr($vorwahl, 2, 1).
                    substr($vorwahl, 3, 1);
                $gemeinden['features'][$gemId]['properties']['color1'] = $color[1][substr($vorwahl, 0, 1)];
            }
            $outFeatures[$gemId] = $gemeinden['features'][$gemId];
            $found['VWfound']++;
        } else {
            print "\$mapping[$vorwahl] = \"".$gemName['name']."\";\n";
            $found['VWnotfound']++;
        }
    }
}

foreach ($plzArray as $plz => $gemeinde) {
    foreach ($gemeinde as $gemName) {
        $gemId = null;
        if (array_key_exists('iso', $gemName) && array_key_exists($gemName['iso'], $gemKzKey)) {
            // pinned via gemKz from gemAdd — resolve directly, no name ambiguity
            $gemId = $gemKzKey[$gemName['iso']];
        } elseif (array_key_exists($gemName['name'], $gemKey)) {
            $gemId = $gemKey[$gemName['name']][0];
            if (count($gemKey[$gemName['name']]) > 1) {
                if (array_key_exists($plz, $plz2gkz)) {
                    foreach ($gemKey[$gemName['name']] as $id) {
                        if ($gemeinden['features'][$id]['properties']['iso'] == $plz2gkz[$plz]) {
                            $gemId = $id;
                            break;
                        }
                    }
                } else {
                    print "More then one GemKeys found for $plz ".$gemName['name'].":\n";
                    foreach ($gemKey[$gemName['name']] as $id) {
                        print_r($gemeinden['features'][$id]['properties']);
                    }
                }
            }
        }

        if ($gemId !== null) {
            if (!array_key_exists('plz', $gemeinden['features'][$gemId]['properties'])) {
                $gemeinden['features'][$gemId]['properties']['plz'] = array();
            }
            $gemeinden['features'][$gemId]['properties']['plz'][$plz] = $gemName['original'];
            if (array_key_exists('validFor', $gemName)) {
                $gemeinden['features'][$gemId]['properties']['plz'][$plz] .= ' (für ';
                $gemeinden['features'][$gemId]['properties']['plz'][$plz] .= $gemName['validFor'];
                $gemeinden['features'][$gemId]['properties']['plz'][$plz] .= ')';
            }

            $gemeinden['features'][$gemId]['properties']['plzcolor'] = '#'.
                substr($plz, 0, 1).
                substr($plz, 0, 1).
                substr($plz, 1, 1).
                substr($plz, 1, 1).
                substr($plz, 2, 1).
                substr($plz, 3, 1);
            $gemeinden['features'][$gemId]['properties']['plzcolor1'] = $color[1][substr($plz, 0, 1)];
            $outFeatures[$gemId] = $gemeinden['features'][$gemId];
            $found['plzfound']++;
        } else {
            print '$plzmapping['.$plz.'] = "'.$gemName['name'].'"; // '.$plz.' '.$gemName['name']."\n";
            $found['plznotfound']++;
        }
    }
}


$out = <<<JSON
{
  "type":"FeatureCollection",
  "crs":{
    "type":"name",
    "properties":{
      "name":"urn:ogc:def:crs:OGC:1.3:CRS84"
    }
  },
  "features":[

JSON;

foreach ($outFeatures as $outKey => $outFeature) {
    $out .= "    ";
    $minX = INF;
    $minY = INF;
    $maxX = -INF;
    $maxY = -INF;

    foreach ($outFeature['geometry']['coordinates'] as $key => $val) {
        foreach ($val as $k1 => $v1) {
            if (is_array($v1[0])) {
                foreach ($v1 as $k2 => $v2) {
                    if ($v2[0] < $minX) $minX = $v2[0];
                    if ($v2[0] > $maxX) $maxX = $v2[0];
                    if ($v2[1] < $minY) $minY = $v2[1];
                    if ($v2[1] > $maxY) $maxY = $v2[1];
                }
            } else {
                if ($v1[0] < $minX) $minX = $v1[0];
                if ($v1[0] > $maxX) $maxX = $v1[0];
                if ($v1[1] < $minY) $minY = $v1[1];
                if ($v1[1] > $maxY) $maxY = $v1[1];
            }
        }
    }
    $outFeature['featureBounds'] = array();
    $outFeature['featureBounds']['minX'] = $minX;
    $outFeature['featureBounds']['maxX'] = $maxX;
    $outFeature['featureBounds']['minY'] = $minY;
    $outFeature['featureBounds']['maxY'] = $maxY;

    $outStr = json_encode($outFeature);
    if ($outStr === false) {
        print_r($outFeature);
        die("json_encode failed: ".json_last_error_msg());
    }
    $out .= json_encode($outFeature);
    if (next($outFeatures) !== false) {
        $out .= ",";
    }
    $out .= "\n";
}
$out .= <<<JSON
  ]
}
JSON;

file_put_contents('vorwahlen+plz.json', $out);
print_r($found);

# zip vorwahlen+plz.json
$zip = new ZipArchive();
$zip->open('vorwahlen+plz.json.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
$zip->addFile('vorwahlen+plz.json');
$zip->close();

function checkMapping($gkz, $new, $orig) {
    if (false) {
        print "$gkz - orig: $orig\n";
        print "$gkz - new:  $new\n";
        print "-----\n";
    }
}

