function __adianti_builder_edit_page()
{
    var url = Adianti.currentURL;
    url = url.replace('engine.php?', '');
    var params = __adianti_query_to_json(url);
    var controller = params['class'];
    __adianti_load_page('index.php?class=BuilderPageService&method=editPage&static=1&controller='+controller);
}

function __adianti_builder_get_codes()
{
    __adianti_load_page('index.php?class=BuilderPageBatchUpdate');
}

function __adianti_builder_update_menu()
{
    __adianti_load_page('index.php?class=BuilderMenuUpdate&method=onAskUpdate&register_state=false');
}

function __adianti_builder_update_permissions()
{
    __adianti_load_page('index.php?class=BuilderPermissionUpdate&method=onAskUpdate&register_state=false');
}

function number_format(number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase

    number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
    var n = !isFinite(+number) ? 0 : +number
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
    var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
    var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
    var s = ''

    var toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec)
        return '' + (Math.round(n * k) / k)
            .toFixed(prec)
    }

    // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || ''
        s[1] += new Array(prec - s[1].length + 1).join('0')
    }

    return s.join(dec)
}