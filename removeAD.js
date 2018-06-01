// ==UserScript==
// @name         屏蔽谷歌广告 和 CSDN 广告
// @namespace
// @version      0.1
// @description  try to take over the world!
// @author       LYL
// @include http://*
// @include https://*
// @match        *
// @require      https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js
// @grant        none
// ==/UserScript==

(function() {
    'use strict';
    //CSDN 去除广告
    $("#asideProfile").next('div').remove();
    $("#asideFooter .aside-box").remove();
    $(".box-box-large").remove();
    $(".recommend-box .recommend-ad-box").remove();

    //开源中国 google 去广告
    $('#answer_list_content_pjax').prev('div').remove();
})();
