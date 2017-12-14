'use strict';
/**
 * create a new js class by this project name.
 * @return {[type]} [description]
 * @Authoe bluelife
 * @Email thebulelife@outlook.com
 * @Data 2017-01-02
 */
function Adlinkx($) {
    this.domain = 'http://dsp.baixiaox.com';
    this.winW = document.documentElement.clientWidth || document.body.clientWidth;
    this.winH = document.documentElement.clientHeight || document.body.clientHeight;
    this.description = '此JS类是根据当前项目需要扩展的一些快捷使用的方法。有些方法可能常用的JS类库中已有，有些可能原生的JS中已支持，为了兼容低版本浏览器做了一些修改[覆盖]，主要还是看那些库中文档有点麻烦就自己根据项目需求写了一些方法';
    this.className = 'Adlinkx';
    this.author = 'bluelife';
    this.version = '0.0.1';
    this.date = '2017-01-02';
    this.email = 'thebulelife@outlook.com';
    this.jQuery = $;
}

/**
 * 显示信息
 * @return {[type]} [description]
 */
Adlinkx.prototype.information = function() {
    if (arguments.length > 0) {
        for (var i = 0; i < arguments.length; i++) {
            console.log(this.ucfirst(arguments[i]) + ':' + this[arguments[i]]);
        }
    } else {
        console.log('ClassName:' + this.className);
        console.log('Description:' + this.description);
        console.log('Author:' + this.author);
        console.log('Version:' + this.version);
        console.log('Date:' + this.date);
        console.log('Email:' + this.email);
    }
}

/**
 * alert Functions
 * @return {[type]} [description]
 * @Authoe bluelife
 * @Email thebulelife@outlook.com
 * @Data 2017-01-02
 * @uses  Adlinkx.alert('正在注册......'[,s[,callback]]);
 */
Adlinkx.prototype.alert = function() {
    var _this = this;
    var text = 'alert message',
        timer = 2000,
        callback = null;
    switch (arguments.length) {
        case 2:
            text = arguments[0];
            timer = parseInt(arguments[1] * 1000);
            break;
        case 3:
            text = arguments[0];
            timer = parseInt(arguments[1] * 1000);
            callback = arguments[2];
            break;
        default:
            text = arguments[0];
            break;
    }
    this.createElement('div', {
        'className': 'alert-mask-layer',
        'width': this.winW + 'px',
        'height': this.winH + 'px',
        'position': 'fixed',
        'left': 0,
        'top': 0,
        'color': '',
        'borderRadius': '5px',
        'background': '#494949',
        'opacity': 0.6,
        'zIndex': 10000
    });
    this.createElement('div', {
        'className': 'alert-popup-layer',
        'width': 300 + 'px',
        'height': 160 + 'px',
        'position': 'fixed',
        'left': parseInt((this.winW - 300) / 2) + 'px',
        'top': parseInt((this.winH - 160) / 2) + 'px',
        'color': '#494949',
        'borderRadius': '5px',
        'background': '#FFF',
        'boxShadow': '2px 2px 4px #494949',
        'fontWeight': 'bold',
        'line-height': 160 + 'px',
        'text-align': 'center',
        'opacity': 1,
        'zIndex': 10001,
        'innerHTML': text
    }, function() {
        callback ? (
            _this.setTimeOut(function() {
                callback();
                var maskLayer = document.getElementsByClassName('alert-mask-layer')[0];
                var popupLayer = document.getElementsByClassName('alert-popup-layer')[0];
                _this.removeChild(popupLayer);
                _this.removeChild(maskLayer);
            }, timer)
        ) : (
            _this.setTimeOut(function() {
                var maskLayer = document.getElementsByClassName('alert-mask-layer')[0];
                var popupLayer = document.getElementsByClassName('alert-popup-layer')[0];
                _this.removeChild(popupLayer);
                _this.removeChild(maskLayer);
            }, timer)
        );
    });



    // if (callback) {
    //     callback();
    // } else {
    //     this.setTimeOut(function(){
    //      var maskLayer = document.getElementsByClassName('mask-layer')[0];
    //      var popupLayer = document.getElementsByClassName('popup-layer')[0];
    //      document.body.removeChild(popupLayer);
    //      document.body.removeChild(maskLayer);
    //      // console.log(maskLayer);
    //      // console.log(popupLayer);
    //     },timer);
    // }
}


/**
 * createElement Functions 创建元素
 * @return {[type]} [description]
 * @Authoe bluelife
 * @Email thebulelife@outlook.com
 * @Data 2017-01-02
 */
Adlinkx.prototype.createElement = function(els, opations, fn) {
    var i = '';
    var createElement = document.createElement(els);
    for (i in opations) {
        i == 'className' || i == 'id' || i == 'innerHTML' ? (
            createElement[i] = opations[i]
        ) : (
            i == 'opacity' ? (
                createElement.style['opacity'] = opations[i],
                createElement.style['filter'] = 'alpha(opacity:' + (opations[i] * 100) + ')'
            ) : (
                createElement.style[this.ucwords([i], 2)] = opations[i]
            )
        );
    }
    this.appendChild(createElement) ? (fn ? fn() : null) : null;
    return this;
}

/**
 * appendChild Functions 向页面中添加元素
 * @return {[type]} [description]
 * @Authoe bluelife
 * @Email thebulelife@outlook.com
 * @Data 2017-01-02
 */
Adlinkx.prototype.appendChild = function(els) {
    return document.body.appendChild(els) ? true : false;
}

/**
 * removeChild Functions 删除页面中的元素
 * @return {[type]} [description]
 * @Authoe bluelife
 * @Email thebulelife@outlook.com
 * @Data 2017-01-02
 */
Adlinkx.prototype.removeChild = function(els) {
    return document.body.removeChild(els) ? true : false;
}


/**
 * setTimeOut Functions 设置延时方法
 * @param {Function} fn [description]
 * @param {[type]}   s  [description]
 */
Adlinkx.prototype.setTimeOut = function(fn, s) {
    setTimeout(fn, s);
}

/**
 * [confirm 控件]
 * @param  {[type]} title [description]
 * @param  {[type]} json  [description]
 * @return {[type]}       [description]
 * @uses  
 *  Adlinkx.confirm('您确定要删除创意吗？', {
        'confirm': {
            'title': '确定',
            'callback': function() { alert('确定'); }
        },
        'cancel': {
            'title': '取消',
            'callback': function() { alert('取消'); }
        }
    });
 */
Adlinkx.prototype.confirm = function(title, json) {
    var _this = this,
        text = title ? title : 'confirm widget !',
        confirm_buts_title = json.confirm.title ? json.confirm.title : 'confirm',
        confirm_buts_callback = json.confirm.callback ? json.confirm.callback : null,
        cancel_buts_title = json.cancel.title ? json.cancel.title : 'cancel',
        cancel_buts_callback = json.cancel.callback ? json.cancel.callback : null;
    this.createElement('div', {
        'className': 'confirm-mask-layer',
        'width': this.winW + 'px',
        'height': this.winH + 'px',
        'position': 'fixed',
        'left': 0,
        'top': 0,
        'color': '',
        'background': '#494949',
        'opacity': 0.6,
        'zIndex': 10000
    });
    this.createElement('div', {
        'className': 'confirm-popup-layer',
        'width': 300 + 'px',
        'height': 160 + 'px',
        'position': 'fixed',
        'left': parseInt((this.winW - 300) / 2) + 'px',
        'top': parseInt((this.winH - 160) / 2) + 'px',
        'color': '#494949',
        'borderRadius': '5px',
        'background': '#FFF',
        'overflow': 'hidden',
        'zIndex': 10000,
        'border': '1px solid #494949',
        'innerHTML': '<div class="confirm-title-block">' + text + '</div><div class="confirm-buttons-group-block"><a href="javascript:void(0);" class="JW-confirm-button" data-type="confirm">' + confirm_buts_title + '</a><a href="javascript:void(0);" class="JW-confirm-button" data-type="cancel">' + cancel_buts_title + '</a></div><style>.confirm-title-block{width:100%;height:110px;text-align:center;line-height:110px;word-wrap:break-word;}\n.confirm-buttons-group-block{width:100%;height:49px;border-top:1px solid #17a08c;}\n.JW-confirm-button{display:block;float:left;width:50%;heihgt:49px;line-height:49px;text-align:center;color:#FFF;font-size:14px;background:#1CAF9A;}\n.JW-confirm-button:hover{background:#17a08c;}</style>'
    });

    var JWconfirmBut = document.getElementsByClassName('JW-confirm-button');
    for (var i = 0; i < JWconfirmBut.length; i++) {
        this.addEvent(JWconfirmBut[i], 'click', function() {
            var confirmMaskLayer = document.getElementsByClassName('confirm-mask-layer')[0];
            var confirmPopupLayer = document.getElementsByClassName('confirm-popup-layer')[0];
            _this.removeChild(confirmMaskLayer);
            _this.removeChild(confirmPopupLayer);
            if (_this.attr(this, 'data-type') == 'confirm') { //确定
                confirm_buts_callback ? confirm_buts_callback() : null;
            } else {
                cancel_buts_callback ? cancel_buts_callback() : null;
            }
        });
    }
}

/**
 * ucwords Functions 将字符串首字母转换成大写[将css格式属性名[border-color]转成js设置属性格式的形式[borderColor]],如果不指定第二个参数，则将全部转换
 * @param  {[type]} str [description]
 * @param  {[type]} num [description]
 * @return {[type]}     [description]
 */
Adlinkx.prototype.ucwords = function() {
    var argus = arguments,
        tmp = [],
        ucwords = '';
    switch (argus.length) {
        case 2:
            if (/[(\-)|(\s)]+/.test(argus[0])) {
                var arr = argus[0].toString().split('-');
                for (var i = 0; i < arr.length; i++) {
                    ucwords += argus[1] ? (
                        i == parseInt(argus[1] - 1) ? (arr[parseInt(argus[1] - 1)].charAt(0).toUpperCase() + arr[parseInt(argus[1] - 1)].substr(1, parseInt(arr[parseInt(argus[1] - 1)].length - 1))) : arr[i]
                    ) : (arr[i].charAt(0).toUpperCase() + arr[i].substr(1, parseInt(arr[i].length - 1)));
                }
                tmp.push(ucwords);
            } else {
                tmp = argus[0];
            }
            break;
        default:
            if (/[(\-)|(\s)]+/.test(argus[0])) {
                var arr = argus[0].toString().split('-');
                for (var i = 0; i < arr.length; i++) {
                    arr[i].charAt(0).toUpperCase() + arr[i].substr(1, parseInt(arr[i].length - 1))
                }
                tmp.push(ucwords);
            } else {
                tmp = argus[0];
            }
            break;
    }
    return tmp;
}

/**
 * [ucfirst 一个单词首字母大写]
 * @param  {[type]} str [description]
 * @return {[type]}     [description]
 */
Adlinkx.prototype.ucfirst = function(str) {
    return str ? str.charAt(0).toUpperCase() + str.substr(1, parseInt(str.length - 1)) : '';
}


Adlinkx.prototype.addEvent = function(els, ev, fn) {
    els.attachEvent ? (els.attachEvent('on' + ev, function() {
        fn.call(els);
        window.event.cancelBubble = true; //IE,阻止冒泡 
        window.event.returnValue = false //IE,取消默认事件
        return false;
    })) : (els.addEventListener(ev, function(e) {
        fn.call(els);
        e.stopPropagation(); //标准,阻止冒泡
        e.preventDefault(); //标准,取消默认事件
        return false;
    }, false));
}


Adlinkx.prototype.forEach = function(arr, fn) {
    alert(arr.length);
    if (Array.forEach == void 0 || !Array.forEach || Array.forEach == null) {
        Array.prototype.forEach = function(callback) {
            for (var i = 0; i < this.length; i++) {
                callback.call(window, this[i]);
            }
        }
    }

    arr.forEach(fn);
}

Adlinkx.prototype.json = function(json, fn) {
    var i = '';
    for (i in json) {
        fn.call(this, i, json[i]);
    }
}

Adlinkx.prototype.attr = function() {
    if (arguments.length == 2) {
        if (typeof arguments[1] == 'string') {
            return arguments[0].getAttribute(arguments[1]);
        } else {
            this.json(arguments[1], function(key, value) {
                arguments[0].setAttribute(key, value);
            });
        }
    } else {
        arguments[0].setAttribute(arguments[1], arguments[2]);
    }
}


Adlinkx.prototype.ckeckLogin = function() {
    var _this = this;
    var crrentPage = window.location.href.toString().split('\/').pop();
    if (crrentPage == 'login' || crrentPage == 'register') {
        return false;
    } else {
        this.jQuery.ajax({
            url: encodeURI(this.domain + '/user/ckeckLogin'),
            type: 'GET',
            async: true,
            success: function(res) {
                // console.log(res);
                res = typeof res === 'string' ? (JSON.parse ? JSON.parse(res) : this.jQuery.parseJSON('res')) : res;
                if (res.code == 1 && res.msg == false) {
                    _this.alert('您尚末登录，请登录。', 1.8, function() {
                        window.location.href = _this.domain + '/user/login';
                    });
                    return false;
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
}

Adlinkx.prototype.isUndefind = function(obj) {
    return obj === void 0 ? true : false;
}

Adlinkx.prototype.pages = function(json) {
    var _this = this;
    var href = json.is_ajax ? 'javascript:void(0);' : json.url;
    var tmp = '';
    var pages_html = '';
    var first_html = '<a href="' + (json.is_ajax ? href : (href + '0')) + '" class="pages-buts" data-but-fn="next" data-num="0">' + (json.first.text ? json.first.text : 'first') + '</a>';
    var last_html = '<a href="' + (json.is_ajax ? href : (href + json.count)) + '" class="pages-buts" data-but-fn="last" data-num="' + json.count + '">' + (json.last.text ? json.last.text : 'last') + '</a>'
    var next_but_html = '<a href="' + (json.is_ajax ? href : (href + (parseInt(json.current) + 1))) + '" class="pages-buts" data-but-fn="next"  data-num="' + (parseInt(json.current) + 1) + '">' + (json.next.text ? json.next.text : 'next') + '</a>';
    var previ_but_html = '<a href="' + (json.is_ajax ? href : (href + (parseInt(json.current) - 1))) + '" class="pages-buts" data-but-fn="previ"  data-num="' + (parseInt(json.current) - 1) + '">' + (json.previ.text ? json.previ.text : 'previ') + '</a>';
    var search_html = '<div class="pages-search-bloxk-box"><input type="text" name="search-number" id="search-number" value="' + json.current + '" /><a href="' + (json.is_ajax ? href : href) + '" class="pages-search-but">' + (json.search.text ? json.search.text : 'search') + '</a></div>';
    for (var i = 0; i < json.count; i++) {
        if ((i + 1) == json.current) {
            tmp += '<a href="' + (json.is_ajax ? href : (href + i)) + '" class="pages-buts native" data-but-fn="pages-but" data-num="' + (i + 1) + '">' + (i + 1) + '</a>';
        } else {
            tmp += '<a href="' + (json.is_ajax ? href : (href + i)) + '" class="pages-buts" data-but-fn="pages-but" data-num="' + (i + 1) + '">' + (i + 1) + '</a>';
        }
    }
    pages_html += (json.count == 0 ? tmp : (json.count == 1 ? (tmp + '<div class="pages-show-text">共<span style="margin:0 4px;">' + json.count + '</span>页，当前第<span style="margin:0 4px;">' + json.current + '</span>页</div>') : ((json.first.enable ? first_html : '') + (json.previ.enable ? previ_but_html : '') + tmp + (json.next.enable ? next_but_html : '') + (json.last.enable ? last_html : '') + '<div class="pages-show-text">共<span style="margin:0 4px;">' + json.count + '</span>页，当前第<span style="margin:0 4px;">' + json.current + '</span>页</div>' + (json.search.enable ? search_html : '')))) +
        '<style>' +
        '.pages-buts{display:block;float:left;width:auto;padding:0 10px;height:30px;line-height:30px;text-align:center;background:#FFF;border:1px solid ' + json.color + ';border-radius:2px;color:' + json.color + ';text-decoration:none;margin:0 0 0 4px;}' +
        '.native,.pages-buts:hover,.pages-search-but:hover{background:' + json.color + ';color:#FFF;}' +
        '.pages-show-text{float:left;width:auto;height:30px;line-height:30px;padding:0 8px;}' +
        '.pages-search-bloxk-box{float:left;width:auto;height:30px;}' +
        '#search-number{display:block;float:left;width:50px;height:30px;line-height:30px;text-align:crent;border:1px solid #c1c1c1;padding:0;border-right:none;}' +
        '.pages-search-but{display:block;float:left;width:80px;height:30px;line-height:30px;text-align:center;border:1px solid ' + json.color + ';border-radius:0 2px 2px 0;background:' + json.color + ';color:#FFF;text-decoration:none;}' +
        '</style>';
    this.jQuery('#' + json.id).html(pages_html);
    this.jQuery(document).on('click', '.pages-buts', function() {
        var offset = _this.jQuery(this).attr('data-num');
        var key_words = _this.jQuery('#search-keywords-box') ? _this.jQuery('#search-keywords-box').val() : '';
        var url = key_words ? json.url + key_words + '/' + offset + '/' + json.num : json.url + offset + '/' + json.num;
        _this.jQuery.ajax({
            url: encodeURI(url),
            type: 'GET',
            success: json.callback,
            error: function(err) {
                console.error(err);
            }
        });
    });
    // document.getElementById(json.id).innerHTML = pages_html;
    // var page_buts = document.getElementsByClassName('pages-buts');
    // for(var j=0;j<page_buts.length;j++){
    //     page_buts[i].onclick = function(){
    //         var offset = this.getAttribute('data-num');
    //     }
    // }
}

Adlinkx.prototype.popup_layer = function(title, text, json) {
    var _this = this;
    var but_html = '';
    if (json.confirm && !json.cancel) {
        but_html = '<div class="poup-footer-block"><a href="javascript:void(0);" class="confirm-but-block">' + (json.confirm ? json.confirm.title : '确定') + '</a></div>';
    } else if (!json.confirm && json.cancel) {
        but_html = '<div class="poup-footer-block"><a href="javascript:void(0);" class="cancel-but-block">' + (json.cancel ? json.cancel.title : '取消') + '</a></div>';
    } else if (json.confirm && json.cancel) {
        but_html = '<div class="poup-footer-block"><a href="javascript:void(0);" class="confirm-but-block">' + (json.confirm ? json.confirm.title : '确定') + '</a><a href="javascript:void(0);" class="cancel-but-block">' + (json.cancel ? json.cancel.title : '取消') + '</a></div>';
    } else {
        but_html = '';
    }
    this.createElement('div', {
        'className': 'popup-mask-layer',
        'width': this.winW + 'px',
        'height': this.winH + 'px',
        'position': 'fixed',
        'left': 0,
        'top': 0,
        'color': '',
        'background': '#494949',
        'opacity': 0.6,
        'zIndex': 10000
    });
    this.createElement('div', {
        'className': 'popup-content-layer',
        'width': json.width + 'px',
        'height': json.height + 'px',
        'position': 'fixed',
        'left': parseInt((this.winW - json.width) / 2) + 'px',
        'top': parseInt((this.winH - json.height) / 2) + 'px',
        'color': '#494949',
        'background': '#FFF',
        'overflow': 'hidden',
        'zIndex': 10000,
        'border': '1px solid #c1c1c1',
        'innerHTML': '<div class="popup-header-block"><span class="popup-title">' + title + ':</span><a href="javascript:void(0);" class="popup-close-but" title="关闭"><i class="fa fa-times"></i></a></div><div class="popup-body-block">' + text + '</div>' + but_html +
            '<style>' +
            '.popup-header-block{width:' + parseInt(json.width - 10) + 'px;height:40px;line-height:40px;padding:0 0 0 20px;}' +
            '.popup-title{display:block;float:left;width:auto;height:40px;line-height:40px;}' +
            '.popup-close-but{display:block;float:right;width:40px;line-height40px;text-align:center;}' +
            '.popup-body-block{width:' + parseInt(json.width - 20) + 'px;height:' + parseInt(json.height - 40) + 'px;padding:10px 0 10px 20px;overflow:auto;}' +
            '.poup-footer-block{width:100%;height:40px;}' +
            '.confirm-but-block{display:inline-block;width:118px;height:38px;line-height:38px;text-align:center;background:#0781ec;border:1px solid #066eca;color:#FFF;border-radius:2px;margin-left:280px;}' +
            '.cancel-but-block{display:inline-block;width:118px;height:38px;line-height:38px;text-align:center;border:1px solid #c1c1c1;background:#FFFl;color:#c1c1c1;border-radius:2px;margin-left:20px;}' +
            '</style>'
    });

    this.jQuery(document).on('click', '.popup-close-but', function() {
        _this.jQuery('.popup-mask-layer').remove();
        _this.jQuery('.popup-content-layer').remove();
    });

    this.jQuery(document).on('click', '.cancel-but-block', function() {
        if (json.cancel && json.cancel.callback) {
            json.cancel.callback();
        }
        _this.jQuery('.popup-mask-layer').remove();
        _this.jQuery('.popup-content-layer').remove();
    });

    this.jQuery(document).on('click', '.confirm-but-block', function() {
        if (json.confirm && json.confirm.callback) {
            json.confirm.callback();
        }

    });
}

/**
 * [autoResizeImage 自动设置图像大小]
 * @param  {[type]} width  [description]
 * @param  {[type]} height [description]
 * @param  {[type]} img    [description]
 * @return {[type]}        [description]
 */
Adlinkx.prototype.autoResizeImage = function(width, height, img) {
    var imgOBJ = new Image();
    imgOBJ.scr = img;
    var Ratio = 1; // 比例，默认1：1 
    var ratio_w;
    var ratio_w;
    var w = imgOBJ.width;
    var h = imgOBJ.height;
    ratio_w = width / w;
    ratio_h = height / h;
    if (width == 0 && height == 0) {
        Ratio = 1;
    } else if (width == 0) { //  
        if (ratio_h < 1) Ratio = ratio_h;
    } else if (height == 0) {
        if (ratio_w < 1) Ratio = ratio_w;
    } else if (ratio_w < 1 || ratio_h < 1) {
        Ratio = (ratio_w <= ratio_h ? ratio_w : ratio_h);
    }
    if (Ratio < 1) {
        w = w * Ratio;
        h = h * Ratio;
    }
    objImg.height = h;
    objImg.width = w;
}

/**
 * [isNaN 检测一个变量是不是非数字]
 * @param  {[type]}  x [description]
 * @return {Boolean}        [description]
 */
Adlinkx.prototype.isNaN = function(x) {
    return isNaN(x);
}

/**
 * [getStyleValue 获取元素的实际(计算后的)样式值，可以获取行间样式、style、外部样式等值。]
 * @param  {[type]} e   [要获取的样式的元素]
 * @param  {[type]} key [要获取的样式的值]
 * @return {[type]}     [返回实际的样式后]
 */
Adlinkx.prototype.getStyleValue = function(e, key) {
    return e.currentStyle ? e.currentStyle[key] : window.getComputedStyle(e, null)[key];
}

/**
 * [number_format 格式化数字]
 * @param  {[type]}  num        [数值(Number或者String)]
 * @param  {[type]}  cent       [要保留的小数位(Number)]
 * @param  {Boolean} isThousand [是否需要千分位 0:不需要,1:需要(数值类型)]
 * @param  {[type]}  count      [显示数值的整数位数，小数点以后除外]
 * @return {[type]}             [返回格式化后的数字]
 */
Adlinkx.prototype.number_format = function(num, cent, isThousand, count) {
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num)) //检查传入数值为数值类型.  
        num = "0";
    if (isNaN(cent)) //确保传入小数位为数值型数值.  
        cent = 0;
    cent = parseInt(cent);
    cent = Math.abs(cent); //求出小数位数,确保为正整数.  
    if (isNaN(isThousand)) //确保传入是否需要千分位为数值类型.  
        isThousand = 0;
    isThousand = parseInt(isThousand);
    if (isThousand < 0)
        isThousand = 0;
    if (isThousand >= 1) //确保传入的数值只为0或1  
        isThousand = 1;
    sign = (num == (num = Math.abs(num))); //获取符号(正/负数)  
    //Math.floor:返回小于等于其数值参数的最大整数  
    num = Math.floor(num * Math.pow(10, cent) + 0.50000000001); //把指定的小数位先转换成整数.多余的小数位四舍五入.  
    cents = num % Math.pow(10, cent); //求出小数位数值.  
    num = Math.floor(num / Math.pow(10, cent)).toString(); //求出整数位数值.  
    cents = cents.toString(); //把小数位转换成字符串,以便求小数位长度.  
    while (cents.length < cent) { //补足小数位到指定的位数.  
        cents = "0" + cents;
    }
    if (isThousand == 0) {
        //不需要千分位符.
        return (((sign) ? '' : '-') + num + '.' + cents);
    } else {
        //对整数部分进行千分位格式化.
        if (num.length < count) {
            var c = '';
            for (var j = 0; j < parseInt(count - num.length); j++) {
                c += '0';
            }
            num = c + num;
        }
        //对整数部分进行千分位格式化.  
        for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
            num = num.substring(0, num.length - (4 * i + 3)) + ',' +
            num.substring(num.length - (4 * i + 3));
        return (((sign) ? '' : '-') + num + '.' + cents);
    }

}

window.ADLINKX = new Adlinkx(jQuery);
ADLINKX.information('className', 'description', 'author', 'version', 'date', 'email');
ADLINKX.ckeckLogin();