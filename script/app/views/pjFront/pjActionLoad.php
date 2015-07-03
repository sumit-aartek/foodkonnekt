<?php
mt_srand();
$index = mt_rand(1, 9999);
$front_messages = __('front_messages', true, false);
$login_messages = __('login_messages', true, false);
$forgot_messages = __('forgot_messages', true, false);
?>
<div id="fdResponsive_<?php echo $index; ?>" class="fdResponsive">
    <div id="fdContainer_<?php echo $index; ?>" class="fdContainer"></div>
</div>

<script type="text/javascript">
    var pjQ = pjQ || {},
            FoodDelivery_<?php echo $index; ?>;
    (function () {
        "use strict";
        var daysOff = [],
                datesOff = [],
                datesOn = [];
<?php
if (isset($tpl['days_off']) && is_array($tpl['days_off'])) {
    foreach ($tpl['days_off'] as $location_id => $type_arr) {
        printf("daysOff[%u] = [];", $location_id);
        foreach ($type_arr as $type => $days_off) {
            printf("daysOff[%u]['%s'] = [];", $location_id, $type);
            if (count($days_off) > 0) {
                printf("daysOff[%u]['%s'] = [%s];", $location_id, $type, join(",", $days_off));
            }
        }
    }
}
if (isset($tpl['dates_off']) && is_array($tpl['dates_off'])) {
    foreach ($tpl['dates_off'] as $location_id => $type_arr) {
        printf("datesOff[%u] = [];", $location_id);
        foreach ($type_arr as $type => $dates_off) {
            printf("datesOff[%u]['%s'] = [];", $location_id, $type);
            if (count($dates_off) > 0) {
                printf("datesOff[%u]['%s'] = ['%s'];", $location_id, $type, join("','", $dates_off));
            }
        }
    }
}
if (isset($tpl['dates_on']) && is_array($tpl['dates_on'])) {
    foreach ($tpl['dates_on'] as $location_id => $type_arr) {
        printf("datesOn[%u] = [];", $location_id);
        foreach ($type_arr as $type => $dates_on) {
            printf("datesOn[%u]['%s'] = [];", $location_id, $type);
            if (count($dates_on) > 0) {
                printf("datesOn[%u]['%s'] = ['%s'];", $location_id, $type, join("','", $dates_on));
            }
        }
    }
}
?>
        var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),
                loadCssHack = function (url, callback) {
                    var link = document.createElement('link');
                    link.type = 'text/css';
                    link.rel = 'stylesheet';
                    link.href = url;

                    document.getElementsByTagName('head')[0].appendChild(link);

                    var img = document.createElement('img');
                    img.onerror = function () {
                        if (callback && typeof callback === "function") {
                            callback();
                        }
                    };
                    img.src = url;
                },
                loadRemote = function (url, type, callback) {
                    if (type === "css" && isSafari) {
                        loadCssHack(url, callback);
                        return;
                    }
                    var _element, _type, _attr, scr, s, element;

                    switch (type) {
                        case 'css':
                            _element = "link";
                            _type = "text/css";
                            _attr = "href";
                            break;
                        case 'js':
                            _element = "script";
                            _type = "text/javascript";
                            _attr = "src";
                            break;
                    }

                    scr = document.getElementsByTagName(_element);
                    s = scr[scr.length - 1];
                    element = document.createElement(_element);
                    element.type = _type;
                    if (type == "css") {
                        element.rel = "stylesheet";
                    }
                    if (element.readyState) {
                        element.onreadystatechange = function () {
                            if (element.readyState == "loaded" || element.readyState == "complete") {
                                element.onreadystatechange = null;
                                if (callback && typeof callback === "function") {
                                    callback();
                                }
                            }
                        };
                    } else {
                        element.onload = function () {
                            if (callback && typeof callback === "function") {
                                callback();
                            }
                        };
                    }
                    element[_attr] = url;
                    s.parentNode.insertBefore(element, s.nextSibling);
                },
                loadScript = function (url, callback) {
                    loadRemote(url, "js", callback);
                },
                loadCss = function (url, callback) {
                    loadRemote(url, "css", callback);
                },
                options = {
                    server: "<?php echo PJ_INSTALL_URL; ?>",
                    folder: "<?php echo PJ_INSTALL_FOLDER; ?>",
                    index: <?php echo $index; ?>,
                    hide: <?php echo isset($_GET['hide']) && (int) $_GET['hide'] === 1 ? 1 : 0; ?>,
                    locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : $controller->pjActionGetLocale(); ?>,
                    startDay: <?php echo (int) $tpl['option_arr']['o_week_start']; ?>,
                    dateFormat: "<?php echo $tpl['option_arr']['o_date_format']; ?>",
                    dayNames: ["<?php echo join('","', __('day_short_names', true, false)); ?>"],
                    monthNamesFull: ["<?php echo join('","', __('months', true, false)); ?>"],
                    daysOff: daysOff,
                    datesOff: datesOff,
                    datesOn: datesOn,
                    messages: {
                        1: "<?php echo pjSanitize::clean($front_messages[1]); ?>",
                        2: "<?php echo pjSanitize::clean($front_messages[2]); ?>",
                        3: "<?php echo pjSanitize::clean($front_messages[3]); ?>",
                        4: "<?php echo pjSanitize::clean($front_messages[4]); ?>",
                        5: "<?php echo pjSanitize::clean($front_messages[5]); ?>",
                        6: "<?php echo pjSanitize::clean($front_messages[6]); ?>",
                        7: "<?php echo pjSanitize::clean($front_messages[7]); ?>",
                        8: "<?php echo pjSanitize::clean($front_messages[8]); ?>",
                        9: "<?php echo pjSanitize::clean($front_messages[9]); ?>",
                        10: "<?php echo pjSanitize::clean($front_messages[10]); ?>",
                        11: "<?php echo pjSanitize::clean($front_messages[11]); ?>",
                        12: "<?php echo pjSanitize::clean($front_messages[12]); ?>",
                    },
                    login_messages: {
                        100: "<?php echo pjSanitize::clean($login_messages[100]); ?>",
                        101: "<?php echo pjSanitize::clean($login_messages[101]); ?>"
                    },
                    forgot_messages: {
                        100: "<?php echo pjSanitize::clean($forgot_messages[100]); ?>",
                        101: "<?php echo pjSanitize::clean($forgot_messages[101]); ?>",
                        200: "<?php echo pjSanitize::clean($forgot_messages[200]); ?>"
                    },
                    email_exiting_message: "<?php echo pjSanitize::clean(__('front_existing_email', true)); ?>"
                };
        loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>pjQ/pjQuery.min.js", function () {
            loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>pjQ/pjQuery.validate.min.js", function () {
                loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>calendarJS/calendar.min.js", function () {
                    loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>pjQ/pjQuery.ResizeSensor.js", function () {
                        loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjFoodDelivery.js", function () {
                            FoodDelivery_<?php echo $index; ?> = new FoodDelivery(options);                            
                        });
                    });
                });
            });
        });

    })();
</script>
<!-- Manmohan Code here -->
<script type="text/javascript">
    function tabs(selectedtab) {
        var s_tab_content = 'tab_content_' + selectedtab;
        var contents = document.getElementsByTagName('div');

        for (var x = 0; x < contents.length; x++)
        {
            name = contents[x].getAttribute('name');
            if (name == 'tab_content') {
                if (contents[x].id == s_tab_content) {
                    contents[x].style.display = 'block';
                } else {
                    contents[x].style.display = 'none';
                }
            }
        }

        var s_tab = 'tab_' + selectedtab;
        var tabs = document.getElementsByTagName('a');
        for (var x = 0; x < tabs.length; x++)
        {
            name = tabs[x].getAttribute('name');
            if (name == 'tab') {
                if (tabs[x].id == s_tab) {
                    tabs[x].className = 'current';
                } else {
                    tabs[x].className = '';
                }
            }
        }
    }
</script>
<!-- End of code here -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="https://malsup.github.com/jquery.cycle.all.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.slideshow').cycle({
		fx: 'scrollLeft,scrollRight',
		timeout: 0,
		next: '#next',
		prev: '#prev'
	});
});
</script>