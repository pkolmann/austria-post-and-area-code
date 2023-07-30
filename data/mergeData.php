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
}

print "found gemKey: ".count($gemKey)."\n\n";

// add gemAdd data to PLZ and Vorwahl data to process
foreach ($gemAdd as $gemName => $gem) {
    if (array_key_exists($gemName, $gemKey)) {
        if (array_key_exists('plz', $gem)) {
            if (is_array($gem['plz'])) {
                foreach ($gem['plz'] as $xplz => $xName) {
                    if (array_key_exists($xplz, $plzArray)) {
                        $newPlz = array();
                        $newPlz['name'] = $gemName;
                        $newPlz['validFor'] = $xName;
                        $newPlz['original'] = $plzArray[$xplz][0]['original'];
                        $plzArray[$xplz][] = $newPlz;
                    } else {
                        print "$gemName - $xplz from gemAdd was not found in PLZ List!!!\n";
                    }
                }
            } else {
                if (array_key_exists($gem['plz'], $plzArray)) {
                    $newPlz = array();
                    $newPlz['name'] = $gemName;
                    $newPlz['original'] = $plzArray[$gem['plz']][0]['original'];
                    $plzArray[$gem['plz']][] = $newPlz;
                } else {
                    print "$gemName - $xplz from gemAdd was not found in PLZ List!!!\n";
                }
            }
        }
        if (array_key_exists('vorwahl', $gem)) {
            if (is_array($gem['vorwahl'])) {
                foreach ($gem['vorwahl'] as $xvw => $xName) {
                    if (array_key_exists($xvw, $telefon)) {
                        $tel = array();
                        $tel['name'] = $gemName;
                        $tel['validFor'] = $xName;
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
                    $tel['original'] = $telefon[$gem['vorwahl']][0]['original'];
                    $telefon[$gem['vorwahl']][] = $tel;
                } else {
                    print "$gemName - $xplz from gemAdd was not found in PLZ List!!!\n";
                }
            }
        }
    } else {
        print "$gemName from gemAdd was not found in Gemeinde List!!!\n";
    }
}


$found = array('VWfound' => 0, 'VWnotfound' => 0, 'plzfound' => 0, 'plznotfound' => 0);
foreach ($telefon as $vorwahl => $gemeinde) {
    foreach ($gemeinde as $gemName) {
        if (array_key_exists($gemName['name'], $gemKey)) {
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
        if (array_key_exists($gemName['name'], $gemKey)) {
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


function checkMapping($gkz, $new, $orig) {
    if (false) {
        print "$gkz - orig: $orig\n";
        print "$gkz - new:  $new\n";
        print "-----\n";
    }
}

