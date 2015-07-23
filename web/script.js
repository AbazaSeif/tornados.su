/**
 * @author Taras Labiak <kissarat@gmail.com>
 * @link http://zenothing.com/
 */

function $$(selector) { return document.querySelector(selector) }
function $all(selector) { return document.querySelectorAll(selector) }
function each(selector, call) {
    return Array.prototype.forEach.call($all(selector), call);
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

each('.matrix th', function(th, i) {
    if (i > 0) {
        th.innerHTML = '<img src="/img/' + i + '.jpg" /> ' + th.innerHTML;
    }
});

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
        option.innerHTML = region.replace(/_/g, ' ');
        option.value = region;
        $region.appendChild(option);
    });
    assign_timezone();
}

function assign_timezone() {
    $timezone.value = $continent.value + '/' + $region.value;

}

var $continent = $$('[name=continent]');
var $region = $$('[name=region]');
var $timezone = $$('[name="User[timezone]"]');
if ($continent && $region && $timezone) {
    $continent.onchange = choice_continent;
    var zone = ($timezone.value || 'Europe/Moscow').split('/');
    choice_continent(zone[0]);
    $region.value = zone[1];
    $region.onchange = assign_timezone;
}

var start = Date.now();

// Google Analytics
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-58031952-5', 'auto');
ga('send', 'pageview');

// Yandex.Metrika
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter31493068 = new Ya.Metrika({
                id:31493068,
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
        id:31493068,
        lang:'ru',
        i:this
    });
    return false
};

addEventListener('beforeunload', function() {
    var request = new XMLHttpRequest();
    request.open('GET', '/visit.php?spend='
        + Math.round((Date.now() - start) / 1000), false);
    request.send(null);
});
