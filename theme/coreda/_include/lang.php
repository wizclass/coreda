<?
    $bbs_page = strpos($_SERVER['PHP_SELF'],'/bbs/');

    if($bbs_page>-1 || $lang_visible == 1){?>
    <style>
        a.gflag{display:none !important}
    </style>
    <?}
?>

<style type="text/css">
    a.gflag {
        vertical-align: middle;
        font-size: 15px;
        padding: 0px;
        background-repeat: no-repeat;
        background-image: url(https://www.drupal.org/files/issues/2018-06-04/32.png);
        width:32px;height:32px;
        display:inline-block;
        margin-right:10px;
    }

    a.gflag img {
        border: 0;
        width:32px;height:32px;
    }

    a.gflag:hover {
        background-image: url(https://www.drupal.org/files/issues/2018-06-04/32a.png);
    }

    #goog-gt-tt {
        display: none !important;
    }

    .goog-te-banner-frame {
        display: none !important;
    }

    .goog-te-menu-value:hover {
        text-decoration: none !important;
    }

    body {
        top: 0 !important;
    }

    #google_translate_element2 {
        display: none !important;
    }
</style>



<a href="#" onclick="doGTranslate('ko|ko');return false;" title="한국어" class="gflag nturl"
   style="background-position:-0px -205px;"><img src="//gtranslate.net/flags/blank.png" 
                                               alt="한국어"/></a>
<a href="#" onclick="doGTranslate('ko|en');return false;" title="English" class="gflag nturl"
   style="background-position:-0px -5px;"><img src="//gtranslate.net/flags/blank.png" 
                                                   alt="English"/></a>
<a href="#" onclick="doGTranslate('ko|zh-CN');return false;" title="中文" class="gflag nturl"
   style="background-position:-300px -5px;"><img src="//gtranslate.net/flags/blank.png" 
                                                   alt="中文"/></a>
<a href="#" onclick="doGTranslate('ko|vi');return false;" title="Tiếng Việt" class="gflag nturl"
style="background-position:-200px -405px;"><img src="//gtranslate.net/flags/blank.png" 
                                                alt="Tiếng Việt"/></a>                                                   
<a href="#" onclick="doGTranslate('ko|ja');return false;" title="日本語" class="gflag nturl"
   style="background-position:-700px -105px;"><img src="//gtranslate.net/flags/blank.png" 
                                                   alt="日本語"/></a>

<div id="google_translate_element2"></div>

<script type="text/javascript">
    function googleTranslateElementInit2() {
        new google.translate.TranslateElement({
            pageLanguage: 'ko',
            autoDisplay: false
        }, 'google_translate_element2');
        
    }
    
</script>

<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
<script type="text/javascript">
    /* <![CDATA[ */
    eval(function (p, a, c, k, e, r) {
        e = function (c) {
            return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
        };
        if (!''.replace(/^/, String)) {
            while (c--) r[e(c)] = k[c] || e(c);
            k = [function (e) {
                return r[e]
            }];
            e = function () {
                return '\\w+'
            };
            c = 1
        }
        while (c--) if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
        return p
    }('6 7(a,b){n{4(2.9){3 c=2.9("o");c.p(b,f,f);a.q(c)}g{3 c=2.r();a.s(\'t\'+b,c)}}u(e){}}6 h(a){4(a.8)a=a.8;4(a==\'\')v;3 b=a.w(\'|\')[1];3 c;3 d=2.x(\'y\');z(3 i=0;i<d.5;i++)4(d[i].A==\'B-C-D\')c=d[i];4(2.j(\'k\')==E||2.j(\'k\').l.5==0||c.5==0||c.l.5==0){F(6(){h(a)},G)}g{c.8=b;7(c,\'m\');7(c,\'m\')}}', 43, 43, '||document|var|if|length|function|GTranslateFireEvent|value|createEvent||||||true|else|doGTranslate||getElementById|google_translate_element2|innerHTML|change|try|HTMLEvents|initEvent|dispatchEvent|createEventObject|fireEvent|on|catch|return|split|getElementsByTagName|select|for|className|goog|te|combo|null|setTimeout|500'.split('|'), 0, {}))
    /* ]]> */
</script>
