/**
 * @link http://zenothing.com/
 * @author Taras Labiak <kissarat@gmail.com>
 */

var start = Date.now();

function $$(selector) { return document.querySelector(selector) }
function $all(selector) { return document.querySelectorAll(selector) }
function $new(name) { return document.createElement(name) }
function $option(value, text) {
    var option = $new('option');
    option.value = value;
    option.innerHTML = text;
    return option;
}

function each(selector, call) {
    if ('string' == typeof selector) {
        selector = $all(selector);
    }
    return Array.prototype.forEach.call(selector, call);
}

function options(select, call) {
    each(select.querySelectorAll('option'), call);
}

function script(source) {
    var element = document.createElement('script');
    element.setAttribute('src', source);
    document.head.appendChild(element);
}

//Feature detection
if (document.body.dataset) {
    each('[data-selector]', function(source) {
        document.querySelector(source.dataset.selector).innerHTML = source.innerHTML;
        source.remove();
    })
}

each('.matrix td[data-id]', function(td) {
    td.onclick = function() {
        location.href = '/matrix/create?id=' + this.dataset.id;
    }
});

function choice_continent(continent) {
    if ('string' == typeof continent) {
        $continent.value = continent;
    }
    $region.innerHTML = '';
    regions[$continent.value].forEach(function(region) {
        var option = document.createElement('option');
        var city = region.replace(/_/g, ' ');
        if (russian && city in cities) {
            city = cities[city];
            option.style.color = 'white';
        }
        option.innerHTML = city;
        option.value = region;
        $region.appendChild(option);
    });
    assign_timezone();
}

function assign_timezone(region) {
    if ('string' == typeof region) {
        $region.value = region;
    }
    $timezone.value = $continent.value + '/' + $region.value;
}

var regions = {};
var $continent;
var $region;
var $timezone = $$('[name="User[timezone]"]') || $$('.timezone');
var $country = $$('[name="User[country]"]') || $$('.country');
var $duration = $$('[name="User[duration]"]');
var russian = /lang=en/.test(document.cookie) ? 0 : 1;

function translate_cabinet() {
    var zone, parent, option;
    if ('INPUT' == $country.tagName) {
        $continent = $new('select');
        $region = $new('select');
        options($timezone, function(option) {
            var timezone = option.value.split('/');
            var continent = timezone[0];
            if (2 == timezone.length) {
                zone = regions[continent];
                if (!zone) {
                    regions[continent] = zone = [];
                    $continent.appendChild($option(continent, russian ? continents[continent] : continent));
                }
                zone.push(timezone[1]);
            }
        });

        $continent.onchange = choice_continent;
        $region.onchange = assign_timezone;
        var old_zone = ($timezone.value || 'Europe/Moscow').split('/');
        choice_continent(old_zone[0]);
        $region.value = old_zone[1];
        parent = $timezone.parentNode;
        parent.appendChild($continent);
        parent.appendChild($region);
        $timezone.style.display = 'none';
    }
    else {
        zone = $timezone.innerHTML.split('/');
        $timezone.setAttribute('title', $timezone.innerHTML);
        zone[0] = continents[zone[0]];
        if (zone[1] in cities) {
            zone[1] = cities[zone[1]];
        }
        $timezone.innerHTML = zone.join('/');
    }

    if ('INPUT' == $country.tagName) {
        var select = document.createElement('select');
        select.appendChild(document.createElement('option'));
        for (var code in countries) {
            option = document.createElement('option');
            option.value = code;
            option.innerHTML = countries[code][russian];
            select.appendChild(option);
        }
        select.name = $country.name;
        if ($country.value) {
            select.value = $country.value;
        }
        select.setAttribute('class', $country.getAttribute('class'));
        $country.parentNode.insertBefore(select, $country);
        $country.remove();
    }
    else if (countries[$country.innerHTML]) {
        $country.innerHTML = countries[$country.innerHTML][russian];
    }

    if ($duration) {
        var $interval = $new('input');
        var $unit = $new('select');
        var interval;
        for(var unit in units) {
            option = $option(unit, units[unit][russian]);
            interval = $duration.value / unit;
            if (!$interval.value && (0 != interval % 1 || interval < 1)) {
                $unit.lastChild.selected = true;
                $interval.value = $duration.value / $unit.lastChild.value;
            }
            $unit.appendChild(option);
        }
        parent = $duration.parentNode;
        $interval.onchange = $unit.onchange = function() {
            $duration.value = $interval.value * $unit.value;
        };
        $interval.type = 'number';
        parent.appendChild($interval);
        parent.appendChild($unit);
        parent.querySelector('label').innerHTML = $duration.getAttribute('title');
        $duration.type = 'hidden';
    }
}


var $cloud = $$('.cloud ul');
if ($cloud) {
    $($cloud).lightSlider({
        item: 1,
        loop: true,
        auto: true,
        pause: 3600,
        gallery: false,
        pager: false,
        //mode: 'fade',
        slideMargin: 0
    });
}

addEventListener('beforeunload', function() {
    var request = new XMLHttpRequest();
    var params = {
        spend: Math.round((Date.now() - start) / 1000),
        width: screen.width,
        height: screen.height
    };
    if (window.performance && performance.memory) {
        params.heap = Math.round(performance.memory.usedJSHeapSize/1024);
    }
    var strparams = [];
    for(var key in params) {
        strparams.push(key + '=' + params[key]);
    }
    request.open('GET', '/visit.php?' + strparams.join('&'), false);
    request.send(null);
});

function browser(string) {
    return navigator.userAgent.indexOf(string) >= 0;
}

var $footer = $$('.footer');
if ($footer && browser('Windows')) {
    $footer.remove();
}

if (window.localStorage) {
    if (!localStorage.getItem('first')) {
        localStorage.setItem('first', new Date().toISOString());
    }

    var $linux = $$('#linux');
    if ($linux && !localStorage.getItem('linux') && browser('Linux') && !browser('Android')) {
        $linux.onclick = function () {
            localStorage.setItem('linux', true);
            this.remove();
        };
        if (browser('Ubuntu')) {
            $linux.querySelector('img').setAttribute('src', '/img/ubuntu.png');
            var $welcome = $linux.querySelector('.welcome');
            $welcome.innerHTML = $welcome.innerHTML.replace('Linux', 'Ubuntu');
        }
        $linux.style.display = 'table-row';
    }
}


var countries = {
"AD":["Andorra","Андорра"],
"AE":["United Arab Emirates","Объединенные Арабские Эмираты"],
"AF":["Afghanistan","Афганистан"],
"AG":["Antigua and Barbuda","Антигуа и Барбуда"],
"AI":["Anguilla","Ангилья"],
"AL":["Albania","Албания"],
"AM":["Armenia","Армения"],
"AO":["Angola","Ангола"],
"AR":["Argentina","Аргентина"],
"AS":["American Samoa","Американское Самоа"],
"AT":["Austria","Австрия"],
"AU":["Australia","Австралия"],
"AW":["Aruba","Аруба"],
"AZ":["Azerbaijan","Азербайджан"],
"BA":["Bosnia and Herzegovina","Босния и Герцеговина"],
"BB":["Barbados","Барбадос"],
"BD":["Bangladesh","Бангладеш"],
"BE":["Belgium","Бельгия"],
"BF":["Burkina Faso","Буркина-Фасо"],
"BG":["Bulgaria","Болгария"],
"BH":["Bahrain","Бахрейн"],
"BI":["Burundi","Бурунди"],
"BJ":["Benin","Бенин"],
"BM":["Bermuda","Бермуды"],
"BO":["Bolivia","Боливия"],
"BR":["Brazil","Бразилия"],
"BS":["Bahamas","Багамы"],
"BT":["Bhutan","Бутан"],
"BW":["Botswana","Ботсвана"],
"BY":["Belarus","Беларусь"],
"BZ":["Belize","Белиз"],
"CA":["Canada","Канада"],
"CF":["Central African Republic","Центрально-Африканская Республика"],
"CH":["Switzerland","Швейцария"],
"CK":["Cook Islands","Острова Кука"],
"CL":["Chile","Чили"],
"CM":["Cameroon","Камерун"],
"CN":["China","Китай"],
"CO":["Colombia","Колумбия"],
"CR":["Costa Rica","Коста-Рика"],
"CU":["Cuba","Куба"],
"CV":["Cape Verde","Кабо-Верде"],
"CY":["Cyprus","Кипр"],
"CZ":["Czech Republic","Чехия"],
"DE":["Germany","Германия"],
"DJ":["Djibouti","Джибути"],
"DK":["Denmark","Дания"],
"DM":["Dominica","Доминика"],
"DO":["Dominican Republic","Доминиканская Республика"],
"DZ":["Algeria","Алжир"],
"EC":["Ecuador","Эквадор"],
"EE":["Estonia","Эстония"],
"EG":["Egypt","Египет"],
"EH":["Western Sahara","Западная Сахара"],
"ER":["Eritrea","Эритрея"],
"ES":["Spain","Испания"],
"ET":["Ethiopia","Эфиопия"],
"FI":["Finland","Финляндия"],
"FJ":["Fiji","Фиджи"],
"FK":["Falkland Islands","Фолклендские острова"],
"FM":["Micronesia","Микронезия"],
"FO":["Faroe Islands","Фарерские острова"],
"FR":["France","Франция"],
"GA":["Gabon","Габон"],
"GB":["United Kingdom","Великобритания"],
"GD":["Grenada","Гренада"],
"GE":["Georgia","Грузия"],
"GF":["French Guiana","Французская Гвиана"],
"GH":["Ghana","Гана"],
"GI":["Gibraltar","Гибралтар"],
"GL":["Greenland","Гренландия"],
"GM":["Gambia","Гамбия"],
"GN":["Guinea","Гвинея"],
"GP":["Guadeloupe","Гваделупа"],
"GQ":["Equatorial Guinea","Экваториальная Гвинея"],
"GR":["Greece","Греция"],
"GT":["Guatemala","Гватемала"],
"GU":["Guam","Гуам"],
"GW":["Guinea-Bissau","Гвинея-Бисау"],
"GY":["Guyana","Гайана"],
"HK":["Hong Kong","Гонконг"],
"HN":["Honduras","Гондурас"],
"HR":["Croatia","Хорватия"],
"HT":["Haiti","Гаити"],
"HU":["Hungary","Венгрия"],
"ID":["Indonesia","Индонезия"],
"IE":["Ireland","Ирландия"],
"IL":["Israel","Израиль"],
"IM":["Isle of Man","Остров Мэн"],
"IN":["India","Индия"],
"IQ":["Iraq","Ирак"],
"IR":["Iran","Иран"],
"IS":["Iceland","Исландия"],
"IT":["Italy","Италия"],
"JM":["Jamaica","Ямайка"],
"JO":["Jordan","Иордания"],
"JP":["Japan","Япония"],
"KE":["Kenya","Кения"],
"KG":["Kyrgyzstan","Кыргызстан"],
"KH":["Cambodia","Камбоджа"],
"KI":["Kiribati","Кирибати"],
"KM":["Comoros","Коморы"],
"KN":["Saint Kitts and Nevis","Сент-Китс и Невис"],
"KP":["North Korea","Северная Корея"],
"KR":["South Korea","Южная Корея"],
"KW":["Kuwait","Кувейт"],
"KY":["Cayman Islands","Острова Кайман"],
"KZ":["Kazakhstan","Казахстан"],
"LA":["Laos","Лаос"],
"LB":["Lebanon","Ливан"],
"LC":["Saint Lucia","Сент-Люсия"],
"LI":["Liechtenstein","Лихтенштейн"],
"LK":["Sri Lanka","Шри-Ланка"],
"LR":["Liberia","Либерия"],
"LS":["Lesotho","Лесото"],
"LT":["Lithuania","Литва"],
"LU":["Luxembourg","Люксембург"],
"LV":["Latvia","Латвия"],
"LY":["Libya","Ливия"],
"MA":["Morocco","Марокко"],
"MC":["Monaco","Монако"],
"MD":["Moldova","Молдова"],
"ME":["Montenegro","Черногория"],
"MG":["Madagascar","Мадагаскар"],
"MH":["Marshall Islands","Маршалловы Острова"],
"MK":["Macedonia","Македония"],
"ML":["Mali","Мали"],
"MN":["Mongolia","Монголия"],
"MP":["Northern Mariana Islands","Северные Марианские острова"],
"MQ":["Martinique","Мартиника"],
"MR":["Mauritania","Мавритания"],
"MS":["Montserrat","Монтсеррат"],
"MT":["Malta","Мальта"],
"MU":["Mauritius","Маврикий"],
"MV":["Maldives","Мальдивы"],
"MW":["Malawi","Малави"],
"MX":["Mexico","Мексика"],
"MY":["Malaysia","Малайзия"],
"MZ":["Mozambique","Мозамбик"],
"NA":["Namibia","Намибия"],
"NC":["New Caledonia","Новая Каледония"],
"NE":["Niger","Нигер"],
"NF":["Norfolk Island","Остров Норфолк"],
"NG":["Nigeria","Нигерия"],
"NI":["Nicaragua","Никарагуа"],
"NL":["Netherlands","Нидерланды"],
"NO":["Norway","Норвегия"],
"NP":["Nepal","Непал"],
"NR":["Nauru","Науру"],
"NU":["Niue","Ниуэ"],
"NZ":["New Zealand","Новая Зеландия"],
"OM":["Oman","Оман"],
"PA":["Panama","Панама"],
"PE":["Peru","Перу"],
"PF":["French Polynesia","Французская Полинезия"],
"PG":["Papua New Guinea","Папуа - Новая Гвинея"],
"PH":["Philippines","Филиппины"],
"PK":["Pakistan","Пакистан"],
"PL":["Poland","Польша"],
"PM":["Saint Pierre and Miquelon","Сент-Пьер и Микелон"],
"PN":["Pitcairn Islands","Питкерн"],
"PR":["Puerto Rico","Пуэрто-Рико"],
"PS":["Palestine","Палестинская автономия"],
"PT":["Portugal","Португалия"],
"PW":["Palau","Палау"],
"PY":["Paraguay","Парагвай"],
"QA":["Qatar","Катар"],
"RE":["Réunion","Реюньон"],
"RO":["Romania","Румыния"],
"RS":["Serbia","Сербия"],
"RU":["Russia","Россия"],
"RW":["Rwanda","Руанда"],
"SA":["Saudi Arabia","Саудовская Аравия"],
"SB":["Solomon Islands","Соломоновы Острова"],
"SC":["Seychelles","Сейшелы"],
"SD":["Sudan","Судан"],
"SE":["Sweden","Швеция"],
"SG":["Singapore","Сингапур"],
"SH":["Saint Helena","Святая Елена"],
"SI":["Slovenia","Словения"],
"SJ":["Svalbard and Jan Mayen","Шпицберген и Ян Майен"],
"SK":["Slovakia","Словакия"],
"SL":["Sierra Leone","Сьерра-Леоне"],
"SM":["San Marino","Сан-Марино"],
"SN":["Senegal","Сенегал"],
"SO":["Somalia","Сомали"],
"SR":["Suriname","Суринам"],
"SS":["South Sudan","Южный Судан"],
"ST":["São Tomé and Príncipe","Сан-Томе и Принсипи"],
"SV":["El Salvador","Сальвадор"],
"SX":["Sint Maarten","Синт-Мартен"],
"SY":["Syria","Сирийская Арабская Республика"],
"SZ":["Swaziland","Свазиленд"],
"TC":["Turks and Caicos Islands","Острова Теркс и Кайкос"],
"TD":["Chad","Чад"],
"TG":["Togo","Того"],
"TH":["Thailand","Таиланд"],
"TJ":["Tajikistan","Таджикистан"],
"TK":["Tokelau","Токелау"],
"TL":["East Timor","Восточный Тимор"],
"TM":["Turkmenistan","Туркменистан"],
"TN":["Tunisia","Тунис"],
"TO":["Tonga","Тонга"],
"TR":["Turkey","Турция"],
"TT":["Trinidad and Tobago","Тринидад и Тобаго"],
"TV":["Tuvalu","Тувалу"],
"TW":["Taiwan","Тайвань"],
"TZ":["Tanzania","Танзания"],
"UA":["Ukraine","Украина"],
"UG":["Uganda","Уганда"],
"UY":["Uruguay","Уругвай"],
"UZ":["Uzbekistan","Узбекистан"],
"VC":["Saint Vincent and the Grenadines","Сент-Винсент"],
"VE":["Venezuela","Венесуэла"],
"VG":["British Virgin Islands","Британские Виргинские острова"],
"VN":["Vietnam","Вьетнам"],
"VU":["Vanuatu","Вануату"],
"WF":["Wallis and Futuna","Уоллис и Футуна"],
"WS":["Samoa","Самоа"],
"YE":["Yemen","Йемен"],
"ZA":["South Africa","Южно-Африканская Республика"],
"ZM":["Zambia","Замбия"],
"ZW":["Zimbabwe","Зимбабве"]
};

var cities = {
"Almaty": "Алматы",
"Kamchatka": "Камчатка",
"Krasnoyarsk": "Красноярськ",
"Magadan": "Магадан",
"Novokuznetsk": "Новокузнецк",
"Novosibirsk": "Новосибирськ",
"Omsk": "Омськ",
"Oral": "Орал",
"Sakhalin": "Сахалин",
"Srednekolymsk": "Среднеколымск",
"Tashkent": "Ташкент",
"Tbilisi": "Тбилиси",
"Vladivostok": "Владивосток",
"Yakutsk": "Якутск",
"Yekaterinburg": "Екатеринбург",

"Kaliningrad": "Калининград",
"Kiev": "Киев",
"Minsk": "Минск",
"Moscow": "Москва",
"Simferopol": "Симферополь",
"Uzhgorod": "Ужгород",
"Volgograd": "Ужгород",
"Zaporozhye": "Ужгород"
};


var continents = {
"Africa": "Африка",
"America": "Америка",
"Antarctica": "Антарктида",
"Arctic": "Арктика",
"Asia": "Азия",
"Atlantic": "Антлантический",
"Australia": "Австралия",
"Europe": "Европа",
"Indian": "Индийский",
"Pacific": "Тихоокенский"
};

var units = {
    1: ['minutes', 'минут'],
    60: ['hours', 'часов'],
    1440: ['days', 'суток'],
    10080: ['weeks', 'недель'],
    43200: ['months', 'месяцев']
};

if ($timezone) {
    translate_cabinet();
}


// Google Analytics
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-58031952-6', 'auto');
ga('require', 'linkid', 'linkid.js');
ga('send', 'pageview');

// Yandex.Metrika
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter31611918 = new Ya.Metrika({
                id:31611918,
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true
            });
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = "https://mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");

var metrika = $$('#metrika img');
metrika.onclick = function() {
    Ya.Metrika.informer({
        id:31611918,
        lang:'ru',
        i:this
    });
    return false
};
